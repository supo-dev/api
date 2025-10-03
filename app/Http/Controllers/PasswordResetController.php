<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\ResetPassword;
use App\Actions\SendPasswordResetCode;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendPasswordResetCodeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Password;

final readonly class PasswordResetController
{
    public function store(
        SendPasswordResetCodeRequest $request,
        SendPasswordResetCode $action
    ): Response {
        $email = $request->string('email')->toString();

        $action->handle($email);

        return response(status: 200);
    }

    public function update(
        ResetPasswordRequest $request,
        ResetPassword $action
    ): JsonResponse {
        $token = $request->string('token')->toString();
        $email = $request->string('email')->toString();
        $password = $request->string('password')->toString();
        $password_confirmation = $request->string('password_confirmation')->toString();

        $status = $action->handle($token, $email, $password, $password_confirmation);

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password reset successfully.',
            ]);
        }

        return response()->json(['message' => __($status)], 400);
    }
}
