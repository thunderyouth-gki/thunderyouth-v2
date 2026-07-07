<?php

namespace App\Livewire\Jemaat;

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

    public ?string $errorMessage = null;

    public string $otp = '';

    public function mount(?Service $service = null)
    {
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
    }

    public function verifyCoordinates($latitude, $longitude)
    {
        $this->isVerifying = true;
        $this->errorMessage = null;

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
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
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

    public function submitOtp()
    {
        $this->errorMessage = null;

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
            $this->verificationSuccess = true;
        } else {
            $this->errorMessage = 'Kode OTP tidak valid.';
        }
    }

    public function render()
    {
        return view('livewire.jemaat.mark-attendance');
    }
}
