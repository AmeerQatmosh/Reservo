<?php

use App\Models\User;

test('returns a successful response', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
});

test('authenticated users are redirected away from the landing page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertRedirect(route('dashboard', absolute: false));
});

test('portfolio mode serves the landing page without auth or rooms', function () {
    config(['reservo.portfolio_mode' => true]);

    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('Create an account', false)
        ->assertDontSee('Join Reservo', false);

    $this->get(route('rooms.index'))->assertRedirect(route('home', absolute: false));
});