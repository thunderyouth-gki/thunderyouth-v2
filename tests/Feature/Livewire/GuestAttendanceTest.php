<?php

use App\Livewire\Jemaat\GuestAttendance;
use App\Models\Service;
use App\Models\Member;
use App\Models\User;
use App\Models\Attendance;
use Livewire\Livewire;

beforeEach(function () {
    $this->service = Service::factory()->create([
        'service_date' => today(),
        'start_time' => now()->subHour()->format('H:i:s'),
        'end_time' => now()->addHour()->format('H:i:s'),
        'attendance_otp' => '123456',
    ]);
});

it('can render the guest attendance component', function () {
    Livewire::test(GuestAttendance::class)
        ->assertStatus(200);
});

it('verifies OTP correctly', function () {
    Livewire::test(GuestAttendance::class)
        ->set('otpInput', '123456')
        ->call('verifyOtp')
        ->assertSet('gpsValid', true)
        ->assertSet('errorMessage', null);
});

it('fails OTP verification if incorrect', function () {
    Livewire::test(GuestAttendance::class)
        ->set('otpInput', '111111')
        ->call('verifyOtp')
        ->assertSet('gpsValid', false)
        ->assertSee('Kode OTP tidak valid.');
});

it('bypasses GPS if valid OTP is provided', function () {
    Livewire::withQueryParams(['otp' => '123456'])
        ->test(GuestAttendance::class)
        ->assertSet('gpsValid', true)
        ->assertSet('method', 'QR Code');
});

it('can save guest attendance directly if no member matches', function () {
    Livewire::test(GuestAttendance::class)
        ->set('gpsValid', true) // Bypass GPS for test
        ->set('guestName', 'John Doe Guest')
        ->call('submitGuest')
        ->assertSet('verificationSuccess', true)
        ->assertSet('showMatches', false);

    $this->assertDatabaseHas('attendances', [
        'service_id' => $this->service->id,
        'guest_name' => 'John Doe Guest',
    ]);
});

it('prevents multiple attendance records from the same device', function () {
    Livewire::test(GuestAttendance::class)
        ->set('gpsValid', true)
        ->set('guestName', 'First Guest')
        ->set('deviceId', 'device-123')
        ->call('submitGuest')
        ->assertSet('verificationSuccess', true);

    Livewire::test(GuestAttendance::class)
        ->set('gpsValid', true)
        ->set('guestName', 'Second Guest')
        ->set('deviceId', 'device-123')
        ->call('submitGuest')
        ->assertSet('verificationSuccess', false)
        ->assertSet('errorMessage', 'Perangkat ini sudah mencatat kehadiran untuk ibadah ini.');
});

it('shows matches if guest name resembles a member', function () {
    Member::factory()->create([
        'name' => 'Budi Santoso',
    ]);

    Livewire::test(GuestAttendance::class)
        ->set('gpsValid', true)
        ->set('guestName', 'Budi')
        ->call('submitGuest')
        ->assertSet('showMatches', true)
        ->assertCount('matchedMembers', 1);

    // Database should NOT have the attendance yet
    $this->assertDatabaseMissing('attendances', [
        'guest_name' => 'Budi',
    ]);
});

it('records attendance if guest confirms they are not on the list', function () {
    Member::factory()->create([
        'name' => 'Budi Santoso',
    ]);

    Livewire::test(GuestAttendance::class)
        ->set('gpsValid', true)
        ->set('guestName', 'Budi')
        ->call('submitGuest')
        ->assertSet('showMatches', true)
        ->call('notMyName')
        ->assertSet('verificationSuccess', true);

    $this->assertDatabaseHas('attendances', [
        'guest_name' => 'Budi',
    ]);
});

it('redirects to login if matched member has user account', function () {
    $user = User::factory()->create();
    $member = Member::factory()->create([
        'name' => 'Siti Aisyah',
        'user_id' => $user->id,
    ]);

    Livewire::test(GuestAttendance::class)
        ->set('gpsValid', true)
        ->set('guestName', 'Siti')
        ->call('submitGuest')
        ->call('selectMember', $member->id)
        ->assertRedirect(route('login'));
});

it('redirects to register if matched member has no user account', function () {
    $member = Member::factory()->create([
        'name' => 'Rina Melati',
        'user_id' => null,
    ]);

    Livewire::test(GuestAttendance::class)
        ->set('gpsValid', true)
        ->set('guestName', 'Rina')
        ->call('submitGuest')
        ->call('selectMember', $member->id)
        ->assertRedirect(route('register'));
});
