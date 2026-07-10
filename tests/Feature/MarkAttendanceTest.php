<?php

use App\Livewire\Jemaat\MarkAttendance;
use App\Models\Event;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

beforeEach(function () {
    Config::set('attendance.church_latitude', -6.924585);
    Config::set('attendance.church_longitude', 107.621815);
    Config::set('attendance.max_radius_meters', 75);
});

it('can verify location within allowed radius', function () {
    $service = Event::create([
        'event_date' => today(),
        'start_time' => now()->subHour()->format('H:i'),
        'end_time' => now()->addHour()->format('H:i'),
        'event_type_id' => 1,
    ]);

    // Same coordinates as church
    Livewire::test(MarkAttendance::class, ['service' => $service])
        ->call('verifyCoordinates', -6.924585, 107.621815)
        ->assertSet('isVerifying', false)
        ->assertSet('gpsValid', true)
        ->assertSet('errorMessage', null);
});

it('rejects location outside allowed radius', function () {
    $service = Event::create([
        'event_date' => today(),
        'start_time' => now()->subHour()->format('H:i'),
        'end_time' => now()->addHour()->format('H:i'),
        'event_type_id' => 1,
    ]);

    // Jakarta coordinates (far away from Bandung)
    Livewire::test(MarkAttendance::class, ['service' => $service])
        ->call('verifyCoordinates', -6.2088, 106.8456)
        ->assertSet('isVerifying', false)
        ->assertSet('gpsValid', false)
        ->assertSee('Lokasi Anda saat ini berada terlalu jauh');
});

it('shows error when no service is active', function () {
    Livewire::test(MarkAttendance::class)
        ->call('verifyCoordinates', -6.924585, 107.621815)
        ->assertSet('isVerifying', false)
        ->assertSet('gpsValid', false)
        ->assertSee('Tidak ada ibadah yang sedang berlangsung');
});

it('can submit correct OTP', function () {
    $service = Event::create([
        'event_date' => today(),
        'start_time' => now()->subHour()->format('H:i'),
        'end_time' => now()->addHour()->format('H:i'),
        'event_type_id' => 1,
        'attendance_otp' => '123456',
    ]);

    Livewire::test(MarkAttendance::class, ['service' => $service])
        ->set('otp', '123456')
        ->call('submitOtp')
        ->assertSet('verificationSuccess', true)
        ->assertSet('errorMessage', null);
});

it('rejects incorrect OTP', function () {
    $service = Event::create([
        'event_date' => today(),
        'start_time' => now()->subHour()->format('H:i'),
        'end_time' => now()->addHour()->format('H:i'),
        'event_type_id' => 1,
        'attendance_otp' => '123456',
    ]);

    Livewire::test(MarkAttendance::class, ['service' => $service])
        ->set('otp', '654321')
        ->call('submitOtp')
        ->assertSet('verificationSuccess', false)
        ->assertSee('Kode OTP tidak valid');
});
