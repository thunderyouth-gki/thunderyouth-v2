<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::view('/services', 'services')->name('services');
Route::view('/events', 'events')->name('events');
Route::view('/prayer-tree', 'prayer')->name('prayer');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';

Route::middleware(['auth', 'role:Root|Pengurus'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/', 'admin.dashboard')->name('dashboard');
    Route::get('/roles', \App\Livewire\Admin\RoleManager::class)->name('roles');
    Route::get('/permissions', \App\Livewire\Admin\PermissionManager::class)->name('permissions');
    Route::get('/users', \App\Livewire\Admin\UserAccessManager::class)->name('users');
    
    Route::livewire('/profile', 'pages::admin.profile')->name('profile');
    Route::livewire('/security', 'pages::admin.security')
        ->middleware(['password.confirm'])
        ->name('security');
    Route::livewire('/appearance', 'pages::admin.appearance')->name('appearance');
});
