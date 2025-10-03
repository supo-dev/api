<?php

declare(strict_types=1);

use App\Models\Post;
use App\Models\User;
use App\Queries\FollowingFeedQuery;
use Illuminate\Database\Eloquent\Builder;

it('may return a query builder', function (): void {
    $user = User::factory()->create();
    $followingFeed = new FollowingFeedQuery($user);

    $builder = $followingFeed->builder();

    expect($builder)->toBeInstanceOf(Builder::class);
});

it('may return posts from followed users by updated date', function (): void {
    $user = User::factory()->create();
    $followedUser = User::factory()->create();
    $otherUser = User::factory()->create();

    $user->following()->attach($followedUser->id);

    $followedPostOlder = Post::factory()->create([
        'user_id' => $followedUser->id,
        'updated_at' => now()->subMinutes(10),
    ]);
    $followedPostNewer = Post::factory()->create([
        'user_id' => $followedUser->id,
        'updated_at' => now(),
    ]);

    // post from a user not followed
    Post::factory()->create(['user_id' => $otherUser->id]);

    $followingFeed = new FollowingFeedQuery($user);
    $posts = $followingFeed->builder()->get();

    expect($posts)->toHaveCount(2)
        ->and($posts->pluck('id')->all())->toMatchArray([$followedPostNewer->id, $followedPostOlder->id]);
});

it('may return empty collection when user follows no one', function (): void {
    $user = User::factory()->create();
    Post::factory()->create();

    $followingFeed = new FollowingFeedQuery($user);
    $posts = $followingFeed->builder()->get();

    expect($posts)->toHaveCount(0);
});

it('may not return posts from users blocked by the user', function (): void {
    $user = User::factory()->create();
    $blockedUser = User::factory()->create();
    $followedUser = User::factory()->create();

    app(App\Actions\FollowUser::class)->handle($user, $followedUser);
    app(App\Actions\FollowUser::class)->handle($user, $blockedUser);
    app(App\Actions\BlockUser::class)->handle($user, $blockedUser);

    $followedPost = Post::factory()->create([
        'user_id' => $followedUser->id,
    ]);
    $blockedPost = Post::factory()->create([
        'user_id' => $blockedUser->id,
    ]);

    $followingFeed = new FollowingFeedQuery($user);
    $posts = $followingFeed->builder()->get();
    expect($posts)->toHaveCount(1);
    $firstPost = $posts->first();
    expect($firstPost->id)->toBe($followedPost->id);
    expect($firstPost->id)->not()->toBe($blockedPost->id);

});
