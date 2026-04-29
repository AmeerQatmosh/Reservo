<?php

use App\Models\User;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('profile.edit'));

    $response
        ->assertOk()
        ->assertSee('Profile information', false);
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    $user->refresh();

    expect($user->name)->toBe('Test User');
    expect($user->email)->toBe('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete(route('profile.destroy'), [
            'identity' => $user->email,
            'password' => 'password',
            'delete_confirmation' => 'delete my account',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('home'));

    $this->assertGuest();
    expect($user->fresh())->toBeNull();
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from(route('profile.edit'))
        ->delete(route('profile.destroy'), [
            'identity' => $user->email,
            'password' => 'wrong-password',
            'delete_confirmation' => 'delete my account',
        ]);

    $response
        ->assertSessionHasErrors('password')
        ->assertRedirect(route('profile.edit'));

    expect($user->fresh())->not->toBeNull();
});

test('identity must match email or name to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from(route('profile.edit'))
        ->delete(route('profile.destroy'), [
            'identity' => 'not-this-user@example.com',
            'password' => 'password',
            'delete_confirmation' => 'delete my account',
        ]);

    $response->assertSessionHasErrors('identity')->assertRedirect(route('profile.edit'));

    expect($user->fresh())->not->toBeNull();
});

test('admin cannot delete account via profile destroy', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $this
        ->actingAs($user)
        ->delete(route('profile.destroy'), [
            'identity' => $user->email,
            'password' => 'password',
            'delete_confirmation' => 'delete my account',
        ])
        ->assertForbidden();

    expect($user->fresh())->not->toBeNull();
});

test('super admin cannot delete account via profile destroy', function () {
    $user = User::factory()->create(['role' => 'super_admin']);

    $this
        ->actingAs($user)
        ->delete(route('profile.destroy'), [
            'identity' => $user->email,
            'password' => 'password',
            'delete_confirmation' => 'delete my account',
        ])
        ->assertForbidden();

    expect($user->fresh())->not->toBeNull();
});

test('profile page hides self-delete action for administrators', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this
        ->actingAs($admin)
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertDontSee('data-test="delete-user-button"', false)
        ->assertSee('cannot be deleted from profile settings', false);

    $member = User::factory()->create(['role' => 'user']);

    $this
        ->actingAs($member)
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertSee('data-test="delete-user-button"', false);
});

test('delete confirmation phrase must be exact', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from(route('profile.edit'))
        ->delete(route('profile.destroy'), [
            'identity' => $user->email,
            'password' => 'password',
            'delete_confirmation' => 'please delete my account',
        ]);

    $response->assertSessionHasErrors('delete_confirmation')->assertRedirect(route('profile.edit'));

    expect($user->fresh())->not->toBeNull();
});

test('user can delete account by matching display name', function () {
    $user = User::factory()->create(['name' => 'Alex Example']);

    $response = $this
        ->actingAs($user)
        ->delete(route('profile.destroy'), [
            'identity' => 'alex example',
            'password' => 'password',
            'delete_confirmation' => 'delete my account',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('home'));

    expect($user->fresh())->toBeNull();
});
