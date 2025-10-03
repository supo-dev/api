<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Queries\ForYouFeedQuery;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ForYouFeedController
{
    /**
     * Get the for-you feed.
     */
    public function __invoke(Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        $forYouFeed = new ForYouFeedQuery($user);

        $posts = $forYouFeed->builder()
            ->paginate(
                perPage: $request->integer('per_page', 15),
                page: $request->integer('page', 1)
            );

        return response()->json($posts);
    }
}
