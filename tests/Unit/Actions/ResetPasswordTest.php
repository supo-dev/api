<?php

declare(strict_types=1);

use App\Actions\ResetPassword;
use App\Models\User;
use Illuminate\Support\Facades\Password;

it('may reset password', function (): void {
    $user = User::factory()->create();
    $action = app(ResetPassword::class);

    Password::shouldReceive('reset')
        ->once()
        ->andReturn(Password::PASSWORD_RESET);

    $result = $action->handle('token123', $user->email, 'newpassword123', 'newpassword123');

    expect($result)->toBe(Password::PASSWORD_RESET);
});
