<?php

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('users can create a reservation when the slot is available', function () {
    $user = makeUser();
    $room = makeRoom();
    $bookingDate = now()->addDays(21)->toDateString();

    $response = $this->actingAs($user)->post(route('reservations.store'), [
        'room_id' => $room->id,
        'date' => $bookingDate,
        'start_time' => '09:00',
        'end_time' => '10:00',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertSessionHas('success', 'Reservation created.');

    expect(Reservation::query()->count())->toBe(1);
});

test('users can not create overlapping reservations for the same room and date', function () {
    $user = makeUser();
    $room = makeRoom();
    $bookingDate = now()->addDays(21)->toDateString();

    makeReservation($user, $room, [
        'date' => $bookingDate,
        'start_time' => '09:00:00',
        'end_time' => '10:00:00',
    ]);

    $response = $this->actingAs($user)
        ->from(route('reservations.my'))
        ->post(route('reservations.store'), [
            'room_id' => $room->id,
            'date' => $bookingDate,
            'start_time' => '09:30',
            'end_time' => '10:30',
        ]);

    $response->assertRedirect(route('reservations.my'));
    $response->assertSessionHasErrors(['overlap']);

    expect(Reservation::query()->count())->toBe(1);
});

test('adjacent reservations are allowed for the same room and date', function () {
    $user = makeUser();
    $room = makeRoom();
    $bookingDate = now()->addDays(21)->toDateString();

    makeReservation($user, $room, [
        'date' => $bookingDate,
        'start_time' => '09:00:00',
        'end_time' => '10:00:00',
    ]);

    $response = $this->actingAs($user)->post(route('reservations.store'), [
        'room_id' => $room->id,
        'date' => $bookingDate,
        'start_time' => '10:00',
        'end_time' => '11:00',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertSessionHas('success', 'Reservation created.');

    expect(Reservation::query()->count())->toBe(2);
});

test('users cannot create reservations outside operating hours', function () {
    $user = makeUser();
    $room = makeRoom();
    $bookingDate = now()->addDays(21)->toDateString();

    $response = $this->actingAs($user)->post(route('reservations.store'), [
        'room_id' => $room->id,
        'date' => $bookingDate,
        'start_time' => '07:00',
        'end_time' => '08:00',
    ]);

    $response->assertSessionHasErrors(['start_time']);

    expect(Reservation::query()->count())->toBe(0);
});

test('users cannot create reservations on a time that does not align to slot steps', function () {
    $user = makeUser();
    $room = makeRoom();
    $bookingDate = now()->addDays(21)->toDateString();

    $response = $this->actingAs($user)->post(route('reservations.store'), [
        'room_id' => $room->id,
        'date' => $bookingDate,
        'start_time' => '09:15',
        'end_time' => '10:15',
    ]);

    $response->assertSessionHasErrors(['start_time']);

    expect(Reservation::query()->count())->toBe(0);
});

test('users can create reservations on half-hour boundaries inside operating hours', function () {
    $user = makeUser();
    $room = makeRoom();
    $bookingDate = now()->addDays(21)->toDateString();

    $response = $this->actingAs($user)->post(route('reservations.store'), [
        'room_id' => $room->id,
        'date' => $bookingDate,
        'start_time' => '08:30',
        'end_time' => '09:30',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertSessionHas('success', 'Reservation created.');
});

test('users can keep legacy off-grid times when updating an existing reservation', function () {
    $user = makeUser();
    $room = makeRoom();
    $bookingDate = now()->addDays(21)->toDateString();

    $reservation = Reservation::query()->create([
        'user_id' => $user->id,
        'room_id' => $room->id,
        'date' => $bookingDate,
        'start_time' => '09:15:00',
        'end_time' => '10:15:00',
    ]);

    $response = $this->actingAs($user)->from(route('reservations.edit', $reservation->id))->put(route('reservations.update', $reservation->id), [
        'room_id' => $room->id,
        'date' => $bookingDate,
        'start_time' => '09:15',
        'end_time' => '10:15',
    ]);

    $response->assertSessionHasNoErrors();
});

test('users can not create reservations in the past', function () {
    $user = makeUser();
    $room = makeRoom();

    $response = $this->actingAs($user)->post(route('reservations.store'), [
        'room_id' => $room->id,
        'date' => now()->subDay()->toDateString(),
        'start_time' => '09:00',
        'end_time' => '10:00',
    ]);

    $response->assertSessionHasErrors(['date']);

    expect(Reservation::query()->count())->toBe(0);
});

test('users can not edit another users reservation', function () {
    $owner = makeUser('owner@example.com');
    $otherUser = makeUser('other@example.com');
    $room = makeRoom();
    $reservation = makeReservation($owner, $room);

    $response = $this->actingAs($otherUser)->get(route('reservations.edit', $reservation->id));

    $response->assertNotFound();
});

test('users can not cancel another users reservation', function () {
    $owner = makeUser('owner@example.com');
    $otherUser = makeUser('other@example.com');
    $room = makeRoom();
    $reservation = makeReservation($owner, $room);

    $response = $this->actingAs($otherUser)->delete(route('reservations.destroy', $reservation->id));

    $response->assertForbidden();
    expect(Reservation::query()->whereKey($reservation->id)->exists())->toBeTrue();
});

test('admins can cancel another users reservation', function () {
    $owner = makeUser('owner@example.com');
    $admin = makeUser('admin@example.com', 'admin');
    $room = makeRoom();
    $reservation = makeReservation($owner, $room);

    $response = $this->actingAs($admin)->delete(route('reservations.destroy', $reservation->id));

    $response->assertSessionHas('success', 'Reservation canceled.');
    expect(Reservation::query()->whereKey($reservation->id)->exists())->toBeFalse();
});

function makeUser(?string $email = null, string $role = 'user'): User
{
    static $counter = 1;

    $email ??= "user{$counter}@example.com";
    $counter++;

    return User::query()->create([
        'name' => ucfirst(strtok($email, '@')),
        'email' => $email,
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => $role,
    ]);
}

function makeRoom(): Room
{
    static $counter = 1;

    $room = Room::query()->create([
        'name' => 'Room '.$counter,
        'capacity' => 8 + $counter,
        'description' => 'Test room '.$counter,
    ]);

    $counter++;

    return $room;
}

function makeReservation(User $user, Room $room, array $overrides = []): Reservation
{
    return Reservation::query()->create(array_merge([
        'user_id' => $user->id,
        'room_id' => $room->id,
        'date' => now()->addDays(21)->toDateString(),
        'start_time' => '09:00:00',
        'end_time' => '10:00:00',
    ], $overrides));
}
