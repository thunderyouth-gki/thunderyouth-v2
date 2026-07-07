<?php

use App\Livewire\Jemaat\MarkAttendance;
use App\Models\Service;
use Livewire\Livewire;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    Config::set('attendance.church_latitude', -6.924585);
    Config::set('attendance.church_longitude', 107.621815);
    Config::set('attendance.max_radius_meters', 75);
});

it('can verify location within allowed radius', function () {
    $service = Service::create([
        'service_date' => today(),
        'start_time' => '07:00',
        'end_time' => '09:00',
        'service_type' => 'Morning Service'
    ]);

    // Same coordinates as church
    Livewire::test(MarkAttendance::class, ['service' => $service])
        ->call('verifyCoordinates', -6.924585, 107.621815)
        ->assertSet('isVerifying', false)
        ->assertSet('verificationSuccess', true)
        ->assertSet('errorMessage', null);
});

it('rejects location outside allowed radius', function () {
    $service = Service::create([
        'service_date' => today(),
        'start_time' => '07:00',
        'end_time' => '09:00',
        'service_type' => 'Morning Service'
    ]);

    // Jakarta coordinates (far away from Bandung)
    Livewire::test(MarkAttendance::class, ['service' => $service])
        ->call('verifyCoordinates', -6.2088, 106.8456)
        ->assertSet('isVerifying', false)
        ->assertSet('verificationSuccess', false)
        ->assertSee('Lokasi Anda saat ini berada terlalu jauh');
});

it('shows error when no service is active', function () {
    Livewire::test(MarkAttendance::class)
        ->call('verifyCoordinates', -6.924585, 107.621815)
        ->assertSet('isVerifying', false)
        ->assertSet('verificationSuccess', false)
        ->assertSee('Tidak ada ibadah yang sedang berlangsung');
});
