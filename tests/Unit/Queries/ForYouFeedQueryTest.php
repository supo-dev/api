<?php

declare(strict_types=1);

use App\Models\Post;
use App\Models\User;
use App\Queries\ForYouFeedQuery;
use Illuminate\Database\Eloquent\Builder;

it('may return a query builder', function (): void {
    $user = User::factory()->create();
    $forYouFeed = new ForYouFeedQuery($user);

    $builder = $forYouFeed->builder();

    expect($builder)->toBeInstanceOf(Builder::class);
});

it('may return all posts ordered by updated date', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $olderPost = Post::factory()->create(['user_id' => $user->id, 'updated_at' => now()->subHour()]);
    $newerPost = Post::factory()->create(['user_id' => $otherUser->id, 'updated_at' => now()]);

    $forYouFeed = new ForYouFeedQuery($user);
    $posts = $forYouFeed->builder()->get();

    expect($posts)->toHaveCount(2)
        ->and($posts->pluck('id')->all())->toMatchArray([$newerPost->id, $olderPost->id]);
});

it('may return empty collection when no posts exist', function (): void {
    $user = User::factory()->create();
    $forYouFeed = new ForYouFeedQuery($user);
    $posts = $forYouFeed->builder()->get();

    expect($posts)->toHaveCount(0);
});

it('may not return posts from users blocked by the user', function (): void {
    $user = User::factory()->create();
    $blockedUser = User::factory()->create();
    $otherUser = User::factory()->create();

    app(App\Actions\BlockUser::class)->handle($user, $blockedUser);
    $post = Post::factory()->create([
        'user_id' => $otherUser->id,
    ]);
    $blockedPost = Post::factory()->create([
        'user_id' => $blockedUser->id,
    ]);
    $forYouFeed = new ForYouFeedQuery($user);
    $posts = $forYouFeed->builder()->get();
    expect($posts)->toHaveCount(1);
    $firstPost = $posts->first();
    expect($firstPost->id)->toBe($post->id);
    expect($firstPost->id)->not()->toBe($blockedPost->id);
});
