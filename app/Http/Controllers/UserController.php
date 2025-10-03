<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateUser;
use App\Actions\DeleteUser;
use App\Actions\UpdateUser;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final readonly class UserController
{
    public function show(User $user): JsonResponse
    {
        $user->loadCount(['posts', 'followers', 'following']);

        return response()->json([
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'email_verified' => $user->email_verified_at !== null,
            'created_at' => $user->created_at->toISOString(),
            'posts_count' => $user->posts_count,
            'followers_count' => $user->followers_count,
            'following_count' => $user->following_count,
        ]);
    }

    public function store(CreateUserRequest $request, CreateUser $action): Response
    {
        $username = $request->string('username')->toString();
        $email = $request->string('email')->toString();
        $password = $request->string('password')->toString();

        $action->handle($username, $email, $password);

        return response(status: 201);
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUser $action): JsonResponse
    {
        $name = $request->has('name') ? $request->string('name')->toString() : null;
        $email = $request->has('email') ? $request->string('email')->toString() : null;

        $updatedUser = $action->handle($user, $name, $email);

        return response()->json([
            'id' => $updatedUser->id,
            'username' => $updatedUser->username,
            'email' => $updatedUser->email,
            'created_at' => $updatedUser->created_at->toISOString(),
        ]);
    }

    public function destroy(
        #[CurrentUser] User $user,
        DeleteUser $action
    ): Response {
        $action->handle($user);

        return response(status: 204);
    }
}
