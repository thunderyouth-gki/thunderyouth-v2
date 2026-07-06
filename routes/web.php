<?php

use App\Livewire\Admin\PermissionManager;
use App\Livewire\Admin\RoleManager;
use App\Livewire\Admin\UserAccessManager;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::view('/services', 'services')->name('services');
Route::view('/events', 'events')->name('events');
Route::view('/prayer-tree', 'prayer')->name('prayer');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::underConstruction('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';

Route::middleware(['auth', 'role:Root|Pengurus'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/', 'admin.dashboard')->name('dashboard');
    Route::get('/roles', RoleManager::class)->name('roles')->middleware('permission:roles.view');
    Route::get('/permissions', PermissionManager::class)->name('permissions')->middleware('permission:permissions.view');
    Route::get('/users', UserAccessManager::class)->name('users')->middleware('permission:users.view');

    Route::livewire('/profile', 'pages::admin.profile')->name('profile');
    Route::livewire('/security', 'pages::admin.security')
        ->middleware(['password.confirm'])
        ->name('security');
    Route::livewire('/appearance', 'pages::admin.appearance')->name('appearance');
});
