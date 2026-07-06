<?php

use Illuminate\Support\Facades\Route;

Route::underConstruction('/', 'home')->name('home');
Route::underConstruction('/services', 'services')->name('services');
Route::underConstruction('/events', 'events')->name('events');
Route::underConstruction('/prayer-tree', 'prayer')->name('prayer');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::underConstruction('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';

Route::middleware(['auth', 'role:Root|Pengurus'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/', 'admin.dashboard')->name('dashboard');
    Route::get('/roles', \App\Livewire\Admin\RoleManager::class)->name('roles')->middleware('permission:roles.view');
    Route::get('/permissions', \App\Livewire\Admin\PermissionManager::class)->name('permissions')->middleware('permission:permissions.view');
    Route::get('/users', \App\Livewire\Admin\UserAccessManager::class)->name('users')->middleware('permission:users.view');
    
    Route::livewire('/profile', 'pages::admin.profile')->name('profile');
    Route::livewire('/security', 'pages::admin.security')
        ->middleware(['password.confirm'])
        ->name('security');
    Route::livewire('/appearance', 'pages::admin.appearance')->name('appearance');
});
