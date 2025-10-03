<?php

declare(strict_types=1);

use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Support\Facades\Notification;

beforeEach(function (): void {
    Notification::fake();
});

it('can request a reset password link', function (): void {
    $user = User::factory()->create();

    $response = $this->postJson(route('password.email'), ['email' => $user->email]);

    $response->assertStatus(200);
    Notification::assertSentTo($user, PasswordResetNotification::class);
});

it('can reset password using a valid token', function (): void {
    $user = User::factory()->create();

    $this->postJson(route('password.email'), ['email' => $user->email]);

    Notification::assertSentTo($user, PasswordResetNotification::class, function (PasswordResetNotification $notification) use ($user) {
        $notificationData = $notification->toArray($user);

        $this->putJson(route('password.reset'), [
            'token' => $notificationData['token'] ?? '',
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
            ->assertSessionHasNoErrors()
            ->assertStatus(200);

        return true;
    });
});

it('throws validation exception when invalid token is used', function (): void {
    $user = User::factory()->create();

    $response = $this->putJson(route('password.reset'), [
        'email' => $user->email,
        'token' => 'invalid-token',
        'password' => 'NewSecurePassword123!',
        'password_confirmation' => 'NewSecurePassword123!',
    ]);

    $response->assertStatus(400)
        ->assertJson(['message' => __('passwords.token')]);
});

it('throws validation exception when passwords do not match', function (): void {
    $user = User::factory()->create();

    $this->postJson(route('password.email'), ['email' => $user->email]);

    Notification::assertSentTo($user, PasswordResetNotification::class, function (PasswordResetNotification $notification) use ($user) {
        $notificationData = $notification->toArray($user);

        $response = $this->putJson(route('password.reset'), [
            'token' => $notificationData['token'] ?? '',
            'email' => $user->email,
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'DifferentPassword123!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('password');

        return true;
    });
});
