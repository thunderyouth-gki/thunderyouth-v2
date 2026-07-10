<?php

namespace App\Livewire\Jemaat;

use App\Models\Attendance;
use App\Models\Member;
use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class GuestAttendance extends Component
{
    #[Locked]
    public ?int $eventId = null;

    public bool $isLive = false;

    public bool $isFinished = false;

    public bool $isVerifying = false;

    public bool $gpsValid = false;

    public bool $verificationSuccess = false;

    public bool $hasAttended = false;

    public ?string $errorMessage = null;

    public string $guestName = '';

    public string $method = 'Manual';

    public ?string $otp = null;

    public string $otpInput = '';

    /** @var Collection<int, Member>|array<int, Member> */
    public $matchedMembers = [];

    public bool $showMatches = false;

    public string $deviceId = '';

    public function mount(?Event $event = null): void
    {
        $this->otp = request()->query('otp');
        $this->method = request()->query('method', 'Manual');

        $this->deviceId = Cookie::get('attendance_device_id') ?? (string) Str::uuid();
        if (! Cookie::has('attendance_device_id')) {
            Cookie::queue('attendance_device_id', $this->deviceId, 60 * 24 * 365); // 1 year
        }

        if ($event) {
            $this->eventId = $event->id;
            $this->isLive = $event->is_live;
            $this->isFinished = $event->is_finished;
        } else {
            $activeService = Event::whereDate('event_date', today())->first();
            if ($activeService) {
                $this->eventId = $activeService->id;
                $this->isLive = $activeService->is_live;
                $this->isFinished = $activeService->is_finished;

                // If OTP is provided, verify it immediately
                if ($this->otp && $this->otp === $activeService->attendance_otp) {
                    $this->gpsValid = true;
                    $this->method = request()->query('method', 'QR');
                }
            }
        }

        $this->checkIfAttended();
    }

    private function checkIfAttended(): void
    {
        if (! $this->eventId || ! $this->deviceId) {
            return;
        }

        $this->hasAttended = Attendance::where('event_id', $this->eventId)
            ->where('device_id', $this->deviceId)
            ->exists();
    }

    public function verifyOtp(): void
    {
        $this->validate([
            'otpInput' => 'required|string|size:6',
        ]);

        if ($this->hasAttended) {
            $this->errorMessage = 'Perangkat ini sudah mencatat kehadiran untuk ibadah ini.';

            return;
        }

        $this->isVerifying = true;
        $this->errorMessage = null;

        if (! $this->eventId) {
            $this->errorMessage = 'Tidak ada ibadah yang sedang berlangsung saat ini.';
            $this->isVerifying = false;

            return;
        }

        $activeService = Event::find($this->eventId);

        if (! $activeService || ! $activeService->is_live) {
            $this->errorMessage = 'Ibadah sudah selesai atau tidak aktif.';
            $this->isVerifying = false;

            return;
        }

        if ($this->otpInput === $activeService->attendance_otp) {
            $this->gpsValid = true;
            if (! in_array($this->method, ['QR', 'NFC'])) {
                $this->method = 'Manual';
            }
        } else {
            $this->errorMessage = 'Kode OTP tidak valid.';
        }

        $this->isVerifying = false;
    }

    public function submitGuest(): void
    {
        $this->validate([
            'guestName' => 'required|string|min:3|max:255',
        ]);

        if ($this->hasAttended) {
            $this->errorMessage = 'Perangkat ini sudah mencatat kehadiran untuk ibadah ini.';

            return;
        }

        if (! $this->gpsValid || ! $this->eventId) {
            return;
        }

        $activeService = Event::find($this->eventId);
        if (! $activeService || ! $activeService->is_live) {
            $this->errorMessage = 'Ibadah sudah selesai atau tidak aktif.';

            return;
        }

        // Check if device already attended
        $deviceAttended = Attendance::where('event_id', $this->eventId)
            ->where('device_id', $this->deviceId)
            ->exists();

        if ($deviceAttended) {
            $this->errorMessage = 'Perangkat ini sudah mencatat kehadiran untuk ibadah ini.';

            return;
        }

        // Search for matching members
        $matches = Member::where('name', 'like', "%{$this->guestName}%")
            ->get();

        if ($matches->count() > 0) {
            $this->matchedMembers = $matches;
            $this->showMatches = true;

            return;
        }

        $this->saveAttendance();
    }

    public function selectMember(int|string $memberId): mixed
    {
        /** @var Member|null $member */
        $member = Member::find($memberId);
        if ($member) {
            if ($member->user_id) {
                return redirect()->route('login')->with('message', 'Akun Anda sudah terdaftar. Silakan Sign In untuk mencatat kehadiran.');
            } else {
                return redirect()->route('register')->with('message', 'Nama Anda sudah terdata sebagai jemaat, namun belum memiliki akun portal. Silakan buat akun terlebih dahulu.');
            }
        }

        return null;
    }

    public function notMyName(): void
    {
        $this->saveAttendance();
    }

    private function saveAttendance(): void
    {
        // Check again to avoid race conditions
        $deviceAttended = Attendance::where('event_id', $this->eventId)
            ->where('device_id', $this->deviceId)
            ->exists();

        if ($deviceAttended) {
            $this->errorMessage = 'Perangkat ini sudah mencatat kehadiran untuk ibadah ini.';

            return;
        }

        Attendance::create([
            'event_id' => $this->eventId,
            'guest_name' => $this->guestName,
            'device_id' => $this->deviceId,
            'method' => $this->method,
            'check_in_time' => now(),
        ]);

        $this->verificationSuccess = true;
        $this->hasAttended = true;
        $this->showMatches = false;
    }

    public function render(): View
    {
        return view('livewire.jemaat.guest-attendance')->layout('components.layouts.app');
    }
}
