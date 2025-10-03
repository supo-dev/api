<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\BlockedUser;
use App\Models\User;

final class UnBlockUser
{
    public function handle(User $user, User $toBlock): void
    {

        BlockedUser::query()
            ->where([
                'user_id' => $user->id,
                'blocked_user_id' => $toBlock->id,
            ])
            ->delete();
    }
}
