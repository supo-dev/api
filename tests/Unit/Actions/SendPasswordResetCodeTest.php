<?php

declare(strict_types=1);

use App\Actions\SendPasswordResetCode;
use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Support\Facades\Notification;

beforeEach(function (): void {
    Notification::fake();
});

it('may send notification with correct code', function (): void {
    $user = User::factory()->create();
    $action = app(SendPasswordResetCode::class);

    $action->handle($user->email);

    Notification::assertSentTo($user, PasswordResetNotification::class, function (PasswordResetNotification $notification) use ($user) {
        $notificationData = $notification->toArray($user);
        $mailable = $notification->toMail($user);

        return isset($notificationData['token']) &&
               mb_strlen($notificationData['token']) === 64 &&
               str_contains($mailable->greeting, $user->username);
    });
});

it('does not send notification if user does not exist', function (): void {
    $action = app(SendPasswordResetCode::class);

    $action->handle('nonexistent@example.com');

    Notification::assertNothingSent();
});
