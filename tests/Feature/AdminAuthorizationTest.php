<?php

use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('admins can access the user management screen in read only mode', function () {
    $admin = User::query()->create([
        'name' => 'Admin User',
        'email' => 'admin-test@example.com',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => 'admin',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.users.index'));

    $response->assertOk();
    $response->assertSee('Admin · Users');
    $response->assertSee('View only');
    $response->assertDontSee('Update role');
});

test('super admins can access the user management screen', function () {
    $superAdmin = User::query()->create([
        'name' => 'Super Admin User',
        'email' => 'super-admin-test@example.com',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => 'super_admin',
    ]);

    $response = $this->actingAs($superAdmin)->get(route('admin.users.index'));

    $response->assertOk();
    $response->assertSee('Admin · Users');
});

test('guests are redirected when accessing admin room detail', function () {
    $room = Room::factory()->create();

    $this->get(route('admin.rooms.show', $room->id))->assertRedirect();
});

test('non admins can not access admin room detail', function () {
    $user = User::factory()->create(['role' => 'user']);
    $room = Room::factory()->create();

    $this->actingAs($user)->get(route('admin.rooms.show', $room->id))->assertForbidden();
});

test('admins can view admin room detail with photo and metadata', function () {
    $admin = User::query()->create([
        'name' => 'Admin Room Detail',
        'email' => 'admin-room-detail@example.com',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => 'admin',
    ]);

    $room = Room::factory()->create([
        'name' => 'Executive Suite Admin View',
        'image_url' => 'https://images.unsplash.com/photo-1544984243-ec57ea16fe25?w=400',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.rooms.show', $room->id));

    $response->assertOk();
    $response->assertSee('Executive Suite Admin View', false);
    $response->assertSee('photo-1544984243-ec57ea16fe25', false);
});

test('admins can not update user roles', function () {
    $admin = User::query()->create([
        'name' => 'Admin User',
        'email' => 'admin-update-test@example.com',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => 'admin',
    ]);

    $targetUser = User::query()->create([
        'name' => 'Regular User',
        'email' => 'regular-user@example.com',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => 'user',
    ]);

    $response = $this->actingAs($admin)->put(route('admin.users.update-role', $targetUser->id), [
        'role' => 'admin',
    ]);

    $response->assertForbidden();
    expect($targetUser->fresh()->role)->toBe('user');
});
