<?php

use App\Models\User;
use Inertia\Inertia;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertSee('Home', false);
    $response->assertDontSee('Quick links', false);
    $response->assertSee('name="search"', false);
});

test('admin users see the dashboard hub not home', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertSee('Dashboard', false);
    $response->assertSee('Quick links', false);
});

test('inertia visits to the blade dashboard trigger a full page load', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'), [
        'X-Inertia' => 'true',
        'X-Inertia-Version' => Inertia::getVersion(),
    ]);

    $response->assertStatus(409);
    $response->assertHeader('X-Inertia-Location', route('dashboard', absolute: true));
});
