<?php

namespace App\Livewire\Jemaat;

use App\Models\Service;
use Livewire\Component;

class HomePresenceButton extends Component
{
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
            $this->dispatch('notify', message: $this->errorMessage, type: 'error');

            return;
        }

        $churchLat = (float) config('attendance.church_latitude');
        $churchLng = (float) config('attendance.church_longitude');
        $maxRadius = (int) config('attendance.max_radius_meters');

        $distance = $this->calculateDistance($latitude, $longitude, $churchLat, $churchLng);

        if ($distance <= $maxRadius) {
            $this->gpsValid = true;
            $this->dispatch('modal-show', name: 'otp-modal-'.$this->serviceId);
        } else {
            $distanceFormatted = round($distance);
            $this->errorMessage = "Lokasi terlalu jauh ({$distanceFormatted} m). Maksimal {$maxRadius} m.";
            $this->dispatch('notify', message: $this->errorMessage, type: 'error');
        }

        $this->isVerifying = false;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
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

    public function submitOtp()
    {
        $this->errorMessage = null;

        $service = Service::find($this->serviceId);

        if (! $service || ! $service->is_live) {
            $this->dispatch('notify', message: 'Ibadah tidak sedang berlangsung.', type: 'error');
            return;
        }

        if (! $service->attendance_otp) {
            $this->dispatch('notify', message: 'Sistem OTP belum dikonfigurasi untuk ibadah ini.', type: 'error');
            return;
        }

        if (trim($this->otp) === $service->attendance_otp) {
            $this->verificationSuccess = true;
            $this->dispatch('modal-close', name: 'otp-modal-'.$this->serviceId);
            $this->dispatch('notify', message: 'Selamat! Kehadiran Anda berhasil diverifikasi.', type: 'success');
        } else {
            $this->dispatch('notify', message: 'Kode OTP tidak valid.', type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.jemaat.home-presence-button');
    }
}
