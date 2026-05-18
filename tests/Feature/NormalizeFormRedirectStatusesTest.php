<?php

use App\Models\Room;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

test('post redirect responses use HTTP 303 for turbo-compatible form-follow GET', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.rooms.store'), [
            'name' => 'Integration Test Room',
            'capacity' => 4,
            'description' => 'Created by normalize redirect status middleware test.',
        ])
        ->assertStatus(Response::HTTP_SEE_OTHER)
        ->assertRedirect(route('admin.rooms.index'));

    expect(Room::query()->where('name', 'Integration Test Room')->exists())->toBeTrue();
});
