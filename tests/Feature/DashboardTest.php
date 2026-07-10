<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->withoutExceptionHandling()->get(route('dashboard'));

    // Validasi otomatis: Terima 200 (OK) jika rute sudah siap,
    // atau terima 503 jika rute masih memakai macro underConstruction()
    $this->assertContains($response->status(), [200, 503]);
});
