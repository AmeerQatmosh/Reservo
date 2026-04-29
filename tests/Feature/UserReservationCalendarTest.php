<?php

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;

test('guests are redirected away from legacy calendar url', function () {
    $this->get(route('reservations.calendar'))
        ->assertRedirect();
});

test('legacy calendar path redirects to my reservations with calendar view', function () {
    $user = User::factory()->create();
    $slot = now();

    $response = $this->actingAs($user)->get(route('reservations.calendar', [
        'year' => $slot->year,
        'month' => $slot->month,
    ]));

    $response->assertRedirect(route('reservations.my', [
        'view' => 'calendar',
        'year' => $slot->year,
        'month' => $slot->month,
    ]));
});

test('authenticated user sees own reservation on calendar tab for that month', function () {
    $user = User::factory()->create();
    $room = Room::factory()->create([
        'name' => 'CalendarPeekRoom Meridian',
    ]);

    $slot = now();

    Reservation::factory()->for($user)->for($room)->create([
        'date' => $slot->toDateString(),
        'start_time' => '09:00:00',
        'end_time' => '10:00:00',
    ]);

    $response = $this->actingAs($user)->get(route('reservations.my', [
        'view' => 'calendar',
        'year' => $slot->year,
        'month' => $slot->month,
    ]));

    $response->assertOk()
        ->assertSee('CalendarPeekRoom Meridian');
});

test('calendar tab does not show another users reservations', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $room = Room::factory()->create();

    $slot = now();

    $foreign = Reservation::factory()->for($owner)->for($room)->create([
        'date' => $slot->toDateString(),
        'start_time' => '09:00:00',
        'end_time' => '10:00:00',
    ]);

    $response = $this->actingAs($other)->get(route('reservations.my', [
        'view' => 'calendar',
        'year' => $slot->year,
        'month' => $slot->month,
    ]));

    $response->assertOk()
        ->assertDontSee('/reservations/'.$foreign->id.'/edit', false);
});
