<?php

declare(strict_types=1);

use App\Models\User;

it('may block a user', function (): void {
    $user = User::factory()->create();
    $targetUser = User::factory()->create();
    $action = app(App\Actions\BlockUser::class);

    $action->handle($user, $targetUser);

    expect($user->blockedUsers()->count())->toBe(1)
        ->and($targetUser->blockedByUsers()->count())->toBe(1);
});

test('re-block a user does not duplicate the block', function (): void {
    $user = User::factory()->create();
    $targetUser = User::factory()->create();
    $action = app(App\Actions\BlockUser::class);

    $action->handle($user, $targetUser);

    expect($user->blockedUsers()->count())->toBe(1)
        ->and($targetUser->blockedByUsers()->count())->toBe(1);

    $action->handle($user, $targetUser);

    expect($user->blockedUsers()->count())->toBe(1)
        ->and($targetUser->blockedByUsers()->count())->toBe(1);
});

test('block users models returns correct data', function (): void {
    $user = User::factory()->create();
    $targetUser = User::factory()->create();
    $action = app(App\Actions\BlockUser::class);
    $action->handle($user, $targetUser);
    $blockedUser = App\Models\BlockedUser::first();
    expect($blockedUser->user->id)->toEqual($user->id);
    expect($blockedUser->blockedUser->id)->toEqual($targetUser->id);
});
