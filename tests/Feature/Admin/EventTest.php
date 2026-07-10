<?php

use App\Models\DutyGroup;
use App\Models\Event;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Ensure Root role exists
    Role::firstOrCreate(['name' => 'Root']);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('Root');
});

it('can render the event index page', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.events.index'))
        ->assertOk();
});

it('can render the event create page', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.events.create'))
        ->assertOk();
});

it('can save a new event as draft', function () {
    Livewire::actingAs($this->admin)
        ->test('pages::admin.events.form')
        ->set('form.event_date', '2026-07-26')
        ->set('form.event_type_id', 1)
        ->set('form.service_type', 'back_to_the_bible')
        ->set('form.theme', 'Tetap Berdiri Teguh')
        ->set('form.start_time', '09:30:00')
        ->set('form.end_time', '11:00:00')
        ->set('form.place', 'Ruang Remaja Pemuda Lt. 1')
        ->set('form.duties.WL 1', 'Pipin')
        ->call('save', 'draft')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('events', [
        'theme' => 'Tetap Berdiri Teguh',
        'status' => 'draft',
    ]);

    $event = Event::first();
    expect($event->duties['WL 1'])->toBe('Pipin');
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
        ->test('pages::admin.events.form')
        ->set('selectedGroup', $group->id)
        ->call('loadGroup')
        ->assertSet('form.duties.WL 1', 'Pipin')
        ->assertSet('form.duties.Pianis', 'Monic');
});
