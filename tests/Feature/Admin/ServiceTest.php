<?php

use App\Models\DutyGroup;
use App\Models\Service;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Ensure Root role exists
    Role::firstOrCreate(['name' => 'Root']);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('Root');
});

it('can render the service index page', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.services.index'))
        ->assertOk();
});

it('can render the service create page', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.services.create'))
        ->assertOk();
});

it('can save a new service as draft', function () {
    Livewire::actingAs($this->admin)
        ->test('pages::admin.services.form')
        ->set('form.service_date', '2026-07-26')
        ->set('form.service_type', 'back_to_the_bible')
        ->set('form.theme', 'Tetap Berdiri Teguh')
        ->set('form.time', '09.30 - selesai')
        ->set('form.place', 'Ruang Remaja Pemuda Lt. 1')
        ->set('form.duties.WL 1', 'Pipin')
        ->call('save', 'draft');

    $this->assertDatabaseHas('services', [
        'theme' => 'Tetap Berdiri Teguh',
        'status' => 'draft',
    ]);

    $service = Service::first();
    expect($service->duties['WL 1'])->toBe('Pipin');
});

it('can load duty group template', function () {
    $group = DutyGroup::create([
        'name' => 'Grup 1',
        'composition' => [
            'WL 1' => 'Pipin',
            'WL 2' => 'Ester',
            'Pianis' => 'Monic',
        ],
    ]);

    Livewire::actingAs($this->admin)
        ->test('pages::admin.services.form')
        ->set('selectedGroup', $group->id)
        ->call('loadGroup')
        ->assertSet('form.duties.WL 1', 'Pipin')
        ->assertSet('form.duties.Pianis', 'Monic');
});
