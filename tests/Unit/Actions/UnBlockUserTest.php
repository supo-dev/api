<?php

declare(strict_types=1);

use App\Models\User;

it('may unblock a user', function (): void {
    $user = User::factory()->create();
    $targetUser = User::factory()->create();
    app(App\Actions\BlockUser::class)->handle($user, $targetUser);
    $action = app(App\Actions\UnBlockUser::class);

    expect($user->blockedUsers()->count())->toBe(1)
        ->and($targetUser->blockedByUsers()->count())->toBe(1);
    $action->handle($user, $targetUser);

    expect($user->blockedUsers()->count())->toBe(0)
        ->and($targetUser->blockedByUsers()->count())->toBe(0);

});

test('re-unblock does nothing', function (): void {
    $user = User::factory()->create();
    $targetUser = User::factory()->create();
    $action = app(App\Actions\UnBlockUser::class);

    expect($user->blockedUsers()->count())->toBe(0);

    $action->handle($user, $targetUser);

    expect($user->blockedUsers()->count())->toBe(0);
});
