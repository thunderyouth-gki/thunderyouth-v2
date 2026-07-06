<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\artisan;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

beforeEach(function () {
    app()[PermissionRegistrar::class]->forgetCachedPermissions();
    Role::findOrCreate('Root');
    Role::findOrCreate('Pengurus');
    Role::findOrCreate('Jemaat');
});

it('can securely setup root account via command', function () {
    artisan('app:setup-root')
        ->expectsQuestion('What is the name of the Root user?', 'Admin')
        ->expectsQuestion('What is the email address for the Root user?', 'root@test.com')
        ->expectsQuestion('Enter a strong password for the Root user', 'password123')
        ->assertExitCode(0);

    $user = User::where('email', 'root@test.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->hasRole('Root'))->toBeTrue();
});

it('assigns Jemaat role on public registration', function () {
    $response = post('/register', [
        'name' => 'John Doe',
        'email' => 'john@test.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect('/dashboard');

    $user = User::where('email', 'john@test.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->hasRole('Jemaat'))->toBeTrue()
        ->and($user->hasRole('Root'))->toBeFalse()
        ->and($user->hasRole('Pengurus'))->toBeFalse();
});

it('prevents root user from being deleted', function () {
    $user = User::factory()->create();
    $user->assignRole('Root');

    expect($user->isRoot())->toBeTrue();
    expect(fn () => $user->delete())->toThrow(Exception::class, 'The Root account cannot be deleted.');

    expect(User::find($user->id))->not->toBeNull();
});

it('allows normal users to be deleted', function () {
    $user = User::factory()->create();
    $user->assignRole('Jemaat');

    $user->delete();

    expect(User::find($user->id))->toBeNull();
});

it('restricts admin access to Root and Pengurus only', function () {
    $jemaat = User::factory()->create()->assignRole('Jemaat');
    $pengurus = User::factory()->create()->assignRole('Pengurus');
    $root = User::factory()->create()->assignRole('Root');

    actingAs($jemaat)->get('/admin')->assertForbidden();
    actingAs($pengurus)->get('/admin')->assertSuccessful();
    actingAs($root)->get('/admin')->assertSuccessful();
});
