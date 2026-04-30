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
    $response->assertSee('Admin Users');
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
    $response->assertSee('Admin Users');
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
        'image_url' => 'https://images.unsplash.com/photo-1637665662134-db459c1bbb46?w=400',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.rooms.show', $room->id));

    $response->assertOk();
    $response->assertSee('Executive Suite Admin View', false);
    $response->assertSee('photo-1637665662134-db459c1bbb46', false);
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

test('admins can switch nav layout preference', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->put(route('preferences.nav-layout'), [
        'layout' => 'vertical',
    ])->assertRedirect();

    expect($admin->fresh()->nav_layout)->toBe('vertical');
});

test('normal users cannot update nav layout', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)->put(route('preferences.nav-layout'), [
        'layout' => 'vertical',
    ])->assertForbidden();
});

test('nav layout update rejects invalid layout values', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->put(route('preferences.nav-layout'), [
        'layout' => 'diagonal',
    ])->assertSessionHasErrors('layout');
});
