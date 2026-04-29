<?php

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;

test('authenticated user can toggle a room favorite', function () {
    $user = User::factory()->create();
    $room = Room::factory()->create();

    $this->actingAs($user)
        ->from(route('rooms.index'))
        ->post(route('rooms.favorite.toggle', $room))
        ->assertRedirect();

    expect($user->favoriteRooms()->whereKey($room->id)->exists())->toBeTrue();

    $this->actingAs($user)
        ->from(route('rooms.index'))
        ->post(route('rooms.favorite.toggle', $room))
        ->assertRedirect();

    expect($user->favoriteRooms()->whereKey($room->id)->exists())->toBeFalse();
});

test('quick book redirects to my reservations when the day has a free slot', function () {
    $user = User::factory()->create();
    $room = Room::factory()->create();
    $date = now()->addDay()->toDateString();

    $this->actingAs($user)
        ->get(route('rooms.quickBook', ['room' => $room, 'date' => $date]))
        ->assertRedirect(route('reservations.my', [
            'room_id' => $room->id,
            'date' => $date,
        ]));
});

test('quick book warns and returns to rooms when the day is fully booked', function () {
    $user = User::factory()->create();
    $room = Room::factory()->create();
    $date = now()->addDay()->toDateString();

    Reservation::factory()->create([
        'user_id' => $user->id,
        'room_id' => $room->id,
        'date' => $date,
        'start_time' => '08:00:00',
        'end_time' => '18:00:00',
    ]);

    $this->actingAs($user)
        ->from(route('rooms.index'))
        ->get(route('rooms.quickBook', ['room' => $room, 'date' => $date]))
        ->assertRedirect(route('rooms.index'))
        ->assertSessionHas('warning');
});

test('favourite rooms page lists saved rooms', function () {
    $user = User::factory()->create();
    $rooms = Room::factory()->count(2)->create();
    $user->favoriteRooms()->attach($rooms->modelKeys());

    $this->actingAs($user)
        ->get(route('favorite-rooms.index'))
        ->assertOk()
        ->assertSee((string) $rooms->first()->name, false);
});
