<?php

namespace App\Livewire\Jemaat;

use App\Models\Attendance;
use App\Models\Service;
use Livewire\Attributes\Locked;
use Livewire\Component;

class MarkAttendance extends Component
{
    #[Locked]
    public ?int $serviceId = null;

    public bool $isLive = false;

    public bool $isFinished = false;

    public bool $isVerifying = false;

    public bool $gpsValid = false;

    public bool $verificationSuccess = false;

    public bool $hasAttended = false;

    public ?string $errorMessage = null;

    public string $otp = '';

    public string $method = 'GPS';

    public function mount(?Service $service = null, string $method = 'GPS'): void
    {
        $this->method = $method;
        if ($service) {
            $this->serviceId = $service->id;
            $this->isLive = $service->is_live;
            $this->isFinished = $service->is_finished;
        } else {
            // Find the active service for today if not provided (e.g. for GPS method directly from dashboard)
            $activeService = Service::whereDate('service_date', today())->first();
            if ($activeService) {
                $this->serviceId = $activeService->id;
                $this->isLive = $activeService->is_live;
                $this->isFinished = $activeService->is_finished;
            }
        }

        $this->checkIfAttended();
    }

    private function checkIfAttended(): void
    {
        if (!$this->serviceId || !auth()->check()) {
            return;
        }

        $user = auth()->user();
        $memberId = $user->member ? $user->member->id : null;
        $guestName = $user->member ? null : $user->name;

        $this->hasAttended = Attendance::where('service_id', $this->serviceId)
            ->where(function ($q) use ($memberId, $guestName) {
                if ($memberId) {
                    $q->where('member_id', $memberId);
                } else {
                    $q->where('guest_name', $guestName);
                }
            })
            ->exists();
    }

    public function verifyCoordinates(float $latitude, float $longitude): void
    {
        $this->isVerifying = true;
        $this->errorMessage = null;

        if ($this->hasAttended) {
            $this->errorMessage = 'Anda sudah mencatat kehadiran untuk ibadah ini.';
            $this->isVerifying = false;
            return;
        }

        if (! $this->serviceId) {
            $this->errorMessage = 'Tidak ada ibadah yang sedang berlangsung saat ini.';
            $this->isVerifying = false;

            return;
        }

        $churchLat = (float) config('attendance.church_latitude');
        $churchLng = (float) config('attendance.church_longitude');
        $maxRadius = (int) config('attendance.max_radius_meters');

        $distance = $this->calculateDistance($latitude, $longitude, $churchLat, $churchLng);

        if ($distance <= $maxRadius) {
            $this->gpsValid = true;
            $this->recordAttendance($this->method);
        } else {
            // Round to nearest integer for display
            $distanceFormatted = round($distance);
            $this->errorMessage = "Lokasi Anda saat ini berada terlalu jauh dari gereja ({$distanceFormatted} meter). Maksimal jarak yang diizinkan adalah {$maxRadius} meter.";
        }

        $this->isVerifying = false;
    }

    /**
     * Calculate the great-circle distance between two points on the Earth.
     * Returns distance in meters.
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // in meters

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
            $this->errorMessage = 'Anda sudah mencatat kehadiran untuk ibadah ini.';
            return;
        }

        $service = Service::find($this->serviceId);

        if (! $service || ! $service->is_live) {
            $this->errorMessage = 'Ibadah tidak sedang berlangsung.';

            return;
        }

        if (! $service->attendance_otp) {
            $this->errorMessage = 'Sistem OTP belum dikonfigurasi untuk ibadah ini.';

            return;
        }

        if (trim($this->otp) === $service->attendance_otp) {
            $this->recordAttendance('Manual');
            $this->verificationSuccess = true;
            $this->hasAttended = true;
        } else {
            $this->errorMessage = 'Kode OTP tidak valid.';
        }
    }

    protected function recordAttendance(string $method): void
    {
        $user = auth()->user();
        if (!$user) {
            return;
        }

        $member = $user->member;
        $memberId = $member ? $member->id : null;
        $guestName = $member ? null : $user->name;

        // Check if attendance already recorded today
        $alreadyAttended = Attendance::where('service_id', $this->serviceId)
            ->where(function ($q) use ($memberId, $guestName) {
                if ($memberId) {
                    $q->where('member_id', $memberId);
                } else {
                    $q->where('guest_name', $guestName);
                }
            })
            ->exists();

        if (!$alreadyAttended) {
            Attendance::create([
                'service_id' => $this->serviceId,
                'member_id' => $memberId,
                'guest_name' => $guestName,
                'method' => $method,
                'check_in_time' => now(),
            ]);
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.jemaat.mark-attendance');
    }
}
