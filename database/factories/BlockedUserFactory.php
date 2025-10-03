<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BlockedUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<BlockedUser>
 */
final class BlockedUserFactory extends Factory
{
    protected $model = BlockedUser::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'user_id' => User::factory(),
            'blocked_user_id' => User::factory(),
        ];
    }
}
