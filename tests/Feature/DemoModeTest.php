<?php

use function Pest\Laravel\get;

test('demo index returns 404 when demo is disabled', function () {
    config(['reservo.demo_enabled' => false]);

    get('/demo')->assertNotFound();
});

test('demo index returns 200 when demo is enabled', function () {
    config(['reservo.demo_enabled' => true]);

    get('/demo')->assertOk();
});

test('demo my reservations page returns 200 with an active sandbox session', function () {
    config(['reservo.demo_enabled' => true]);

    $this->post(route('demo.start'), ['role' => 'user'])
        ->assertRedirect(route('demo.hub'));

    $this->get(route('demo.reservations.my'))->assertOk();
});

test('demo rooms browse applies search filter', function () {
    config(['reservo.demo_enabled' => true]);

    $this->post(route('demo.start'), ['role' => 'user']);

    $this->get(route('demo.rooms', ['search' => 'Focus Pod']))
        ->assertOk()
        ->assertSee('Focus Pod', false);
});

test('demo admin users page returns 200 for admin role', function () {
    config(['reservo.demo_enabled' => true]);

    $this->post(route('demo.start'), ['role' => 'admin']);

    $this->get(route('demo.admin.users'))->assertOk()->assertSee('Sample directory', false);
});

test('demo admin create room page returns 200 for admin role', function () {
    config(['reservo.demo_enabled' => true]);

    $this->post(route('demo.start'), ['role' => 'admin']);

    $this->get(route('demo.admin.rooms.create'))->assertOk()->assertSee('Create room', false);
});
