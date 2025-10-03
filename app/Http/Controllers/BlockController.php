<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\BlockUser;
use App\Actions\UnBlockUser;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Response;

final class BlockController
{
    public function store(
        #[CurrentUser] User $loggedInUser,
        User $user,
        BlockUser $action
    ): Response {
        $action->handle($loggedInUser, $user);

        return response(status: 201);

    }

    public function destroy(
        #[CurrentUser] User $loggedInUser,
        User $user,
        UnBlockUser $action
    ): Response {
        $action->handle($loggedInUser, $user);

        return response(status: 204);

    }
}
