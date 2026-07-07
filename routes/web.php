<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PublicController;
use App\Livewire\Admin\PermissionManager;
use App\Livewire\Admin\RoleManager;
use App\Livewire\Admin\UserAccessManager;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/services', [PublicController::class, 'services'])->name('services');
Route::view('/events', 'events')->name('events');
Route::view('/prayer-tree', 'prayer')->name('prayer');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::underConstruction('dashboard', 'dashboard')->name('dashboard');

    // Attendance Verification Routes
    Route::get('/attendance/nfc/current', [AttendanceController::class, 'verifyViaNfc'])->name('attendance.nfc');

    // Debug route to see what ValidateSignature sees
    Route::get('/debug-signature', function (Request $request) {
        $service = Service::find(2); // assuming ID 2 is what they tested
        if (! $service) {
            $service = Service::latest()->first();
        }

        $webGeneratedUrl = $service ? $service->qrVerificationUrl() : 'No service found';

        return [
            'web_generated_url' => $webGeneratedUrl,
            'is_identical' => $webGeneratedUrl === 'http://localhost:8000/attendance/qr/2?signature='.$request->query('signature'),
        ];
    });

    Route::get('/attendance/qr/{service}', [AttendanceController::class, 'verifyViaQr'])->name('attendance.qr')->middleware('signed:relative');
});

require __DIR__.'/settings.php';

Route::middleware(['auth', 'role:Root|Pengurus'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/', 'admin.dashboard')->name('dashboard');
    Route::get('/roles', RoleManager::class)->name('roles')->middleware('permission:roles.view');
    Route::get('/permissions', PermissionManager::class)->name('permissions')->middleware('permission:permissions.view');
    Route::get('/users', UserAccessManager::class)->name('users')->middleware('permission:users.view');

    // Services
    Route::livewire('/services', 'pages::admin.services.index')->name('services.index');
    Route::livewire('/services/create', 'pages::admin.services.form')->name('services.create');
    Route::livewire('/services/{service}/edit', 'pages::admin.services.form')->name('services.edit');
    Route::livewire('/profile', 'pages::admin.profile')->name('profile');
    Route::livewire('/security', 'pages::admin.security')
        ->middleware(['password.confirm'])
        ->name('security');
    Route::livewire('/appearance', 'pages::admin.appearance')->name('appearance');
});
