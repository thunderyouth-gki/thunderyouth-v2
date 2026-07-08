<?php

use Illuminate\Support\Facades\Route;

it('displays the 401 error page', function () {
    Route::get('/test-401', fn () => abort(401));

    $response = $this->get('/test-401');

    $response->assertStatus(401);
});

it('displays the 403 error page', function () {
    Route::get('/test-403', fn () => abort(403));

    $response = $this->get('/test-403');

    $response->assertStatus(403);
});

it('displays the 404 error page', function () {
    $response = $this->get('/a-route-that-does-not-exist-' . uniqid());

    $response->assertStatus(404);
});

it('displays the 429 error page', function () {
    Route::get('/test-429', fn () => abort(429));

    $response = $this->get('/test-429');

    $response->assertStatus(429);
});

it('displays the 500 error page', function () {
    Route::get('/test-500', fn () => abort(500));

    $response = $this->get('/test-500');

    $response->assertStatus(500);
});

it('displays the 502 error page', function () {
    Route::get('/test-502', fn () => abort(502));

    $response = $this->get('/test-502');

    $response->assertStatus(502);
});

it('displays the 503 error page', function () {
    Route::get('/test-503', fn () => abort(503));

    $response = $this->get('/test-503');

    $response->assertStatus(503);
});
