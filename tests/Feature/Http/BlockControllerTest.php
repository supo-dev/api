<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('may block a user', function () {
    $user = User::factory()->create();
    $targetUser = User::factory()->create();

    Sanctum::actingAs($user, ['*']);

    $response = $this->postJson(route('blocks.store', $targetUser));

    $response->assertStatus(201);

    expect($user->blockedUsers()->count())->toBe(1);
});

it('may unblock a user', function () {
    $user = User::factory()->create();
    $targetUser = User::factory()->create();

    App\Models\BlockedUser::factory()->create([
        'user_id' => $user->id,
        'blocked_user_id' => $targetUser->id,
    ]);
    expect($user->blockedUsers()->count())->toBe(1);

    Sanctum::actingAs($user, ['*']);

    $response = $this->deleteJson(route('blocks.destroy', $targetUser));

    $response->assertStatus(204);

    expect($user->refresh()->blockedUsers()->count())->toBe(0);
});

it('cannot block a user twice', function () {
    $user = User::factory()->create();
    $targetUser = User::factory()->create();

    App\Models\BlockedUser::factory()->create([
        'user_id' => $user->id,
        'blocked_user_id' => $targetUser->id,
    ]);
    Sanctum::actingAs($user, ['*']);

    $response = $this->postJson(route('blocks.store', $targetUser));

    $response->assertStatus(201);

    expect($user->blockedUsers()->count())->toBe(1);
});
