<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Support\Facades\Password;

final readonly class SendPasswordResetCode
{
    public function handle(string $email): void
    {
        $user = User::query()->where('email', $email)->first();
        if (! $user) {
            return;
        }

        $token = Password::createToken($user);
        $user->notify(new PasswordResetNotification($token));
    }
}
