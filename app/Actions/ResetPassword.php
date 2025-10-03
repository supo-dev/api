<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

final readonly class ResetPassword
{
    public function handle(string $token, string $email, string $password, string $password_confirmation): string
    {
        $status = Password::reset(
            [
                'token' => $token,
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $password_confirmation,
            ],
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET ? Password::PASSWORD_RESET : Password::INVALID_TOKEN;
    }
}
