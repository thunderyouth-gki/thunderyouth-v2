<?php

namespace App\Livewire\Jemaat;

use App\Models\Attendance;
use App\Models\Event;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;
use Livewire\Component;

class HomePresenceButton extends Component
{
    public ?int $eventId = null;

    public bool $isLive = false;

    public bool $isFinished = false;

    public bool $isVerifying = false;

    public bool $gpsValid = false;

    public bool $verificationSuccess = false;

    public bool $hasAttended = false;

    public ?string $errorMessage = null;

    public string $otp = '';

    public function mount(?Event $event = null): void
    {
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
            }
        }

        $this->checkIfAttended();
    }

    private function checkIfAttended(): void
    {
        if (! $this->eventId) {
            return;
        }

        if (auth()->check()) {
            $user = auth()->user();
            $memberId = $user->member ? $user->member->id : null;
            $guestName = $user->member ? null : $user->name;

            $this->hasAttended = Attendance::where('event_id', $this->eventId)
                ->where(function ($q) use ($memberId, $guestName) {
                    if ($memberId) {
                        $q->where('member_id', $memberId);
                    } else {
                        $q->where('guest_name', $guestName);
                    }
                })
                ->exists();
        } else {
            $deviceId = Cookie::get('attendance_device_id');
            if ($deviceId) {
                $this->hasAttended = Attendance::where('event_id', $this->eventId)
                    ->where('device_id', $deviceId)
                    ->exists();
            }
        }
    }

    public function verifyCoordinates(float $latitude, float $longitude): void
    {
        $this->isVerifying = true;
        $this->errorMessage = null;

        if ($this->hasAttended) {
            $this->errorMessage = 'Anda sudah mencatat kehadiran untuk ibadah ini.';
            $this->isVerifying = false;
            $this->dispatch('notify', message: $this->errorMessage, type: 'error');

            return;
        }

        if (! $this->eventId) {
            $this->errorMessage = 'Tidak ada ibadah yang sedang berlangsung saat ini.';
            $this->isVerifying = false;
            $this->dispatch('notify', message: $this->errorMessage, type: 'error');

            return;
        }

        $churchLat = (float) config('attendance.church_latitude');
        $churchLng = (float) config('attendance.church_longitude');
        $maxRadius = (int) config('attendance.max_radius_meters');

        $distance = $this->calculateDistance($latitude, $longitude, $churchLat, $churchLng);

        if ($distance <= $maxRadius) {
            $this->gpsValid = true;
            $this->dispatch('modal-show', name: 'otp-modal-'.$this->eventId);
        } else {
            $distanceFormatted = round($distance);
            $this->errorMessage = "Lokasi terlalu jauh ({$distanceFormatted} m). Maksimal {$maxRadius} m.";
            $this->dispatch('notify', message: $this->errorMessage, type: 'error');
        }

        $this->isVerifying = false;
    }

    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function submitOtp(): void
    {
        $this->errorMessage = null;

        if ($this->hasAttended) {
            $this->dispatch('notify', message: 'Anda sudah mencatat kehadiran untuk ibadah ini.', type: 'error');

            return;
        }

        $event = Event::find($this->eventId);

        if (! $event || ! $event->is_live) {
            $this->dispatch('notify', message: 'Ibadah tidak sedang berlangsung.', type: 'error');

            return;
        }

        if (! $event->attendance_otp) {
            $this->dispatch('notify', message: 'Sistem OTP belum dikonfigurasi untuk ibadah ini.', type: 'error');

            return;
        }

        if (trim($this->otp) === $event->attendance_otp) {
            $this->recordAttendance('GPS');
            $this->verificationSuccess = true;
            $this->hasAttended = true;
            $this->dispatch('modal-close', name: 'otp-modal-'.$this->eventId);
            $this->dispatch('notify', message: 'Selamat! Kehadiran Anda berhasil diverifikasi.', type: 'success');
        } else {
            $this->dispatch('notify', message: 'Kode OTP tidak valid.', type: 'error');
        }
    }

    protected function recordAttendance(string $method): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        $member = $user->member;
        $memberId = $member ? $member->id : null;
        $guestName = $member ? null : $user->name;

        // Check again to avoid race conditions
        $alreadyAttended = Attendance::where('event_id', $this->eventId)
            ->where(function ($q) use ($memberId, $guestName) {
                if ($memberId) {
                    $q->where('member_id', $memberId);
                } else {
                    $q->where('guest_name', $guestName);
                }
            })
            ->exists();

        if (! $alreadyAttended) {
            Attendance::create([
                'event_id' => $this->eventId,
                'member_id' => $memberId,
                'guest_name' => $guestName,
                'method' => $method,
                'check_in_time' => now(),
            ]);
        }
    }

    public function render(): View
    {
        return view('livewire.jemaat.home-presence-button');
    }
}
