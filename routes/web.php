<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PublicController;
use App\Livewire\Admin\AttendanceManager;
use App\Livewire\Admin\MemberManager;
use App\Livewire\Admin\PermissionManager;
use App\Livewire\Admin\RoleManager;
use App\Livewire\Admin\UserAccessManager;
use App\Livewire\Jemaat\GuestAttendance;
use App\Livewire\User\Attendances;
use App\Livewire\User\Dashboard;
use App\Livewire\User\Events;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/services', [PublicController::class, 'services'])->name('services');
Route::view('/events', 'events')->name('events');
Route::view('/prayer-tree', 'prayer')->name('prayer');

// Guest Attendance Route
Route::get('/guest/attendance', GuestAttendance::class)->name('guest.attendance');

// Attendance Verification Routes (Both routes will check auth inside the controller)
Route::get('/attendance/nfc/current', [AttendanceController::class, 'verifyViaNfc'])->name('attendance.nfc');
Route::get('/attendance/qr/{event}', [AttendanceController::class, 'verifyViaQr'])->name('attendance.qr')->middleware('signed:relative');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/my-attendances', Attendances::class)->name('user.attendances');
    Route::get('/upcoming-events', Events::class)->name('user.events');

    // Debug route to see what ValidateSignature sees
    Route::get('/debug-signature', function (Request $request) {
        $event = Event::find(2); // assuming ID 2 is what they tested
        if (! $event) {
            $event = Event::latest()->first();
        }

        $webGeneratedUrl = $event ? $event->qrVerificationUrl() : 'No service found';

        return [
            'web_generated_url' => $webGeneratedUrl,
            'is_identical' => $webGeneratedUrl === 'http://localhost:8000/attendance/qr/2?signature='.$request->query('signature'),
        ];
    });
});

require __DIR__.'/settings.php';

Route::middleware(['auth', 'role:Root|Pengurus'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/', 'admin.dashboard')->name('dashboard');
    Route::get('/roles', RoleManager::class)->name('roles')->middleware('permission:roles.view');
    Route::get('/permissions', PermissionManager::class)->name('permissions')->middleware('permission:permissions.view');
    Route::get('/users', UserAccessManager::class)->name('users')->middleware('permission:users.view');

    // Services
    Route::livewire('/events', 'pages::admin.events.index')->name('events.index');
    Route::livewire('/events/create', 'pages::admin.events.form')->name('events.create');
    Route::livewire('/events/{event}/edit', 'pages::admin.events.form')->name('events.edit');

    // Members & Attendances
    Route::get('/members', MemberManager::class)->name('members');
    Route::get('/attendances', AttendanceManager::class)->name('attendances');

    Route::livewire('/profile', 'pages::admin.profile')->name('profile');
    Route::livewire('/security', 'pages::admin.security')
        ->middleware(['password.confirm'])
        ->name('security');
    Route::livewire('/appearance', 'pages::admin.appearance')->name('appearance');
});
