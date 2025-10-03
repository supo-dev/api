<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

final readonly class ForYouFeedQuery
{
    public function __construct(
        private ?User $user
    ) {}

    /**
     * @return Builder<Post>
     */
    public function builder(): Builder
    {
        return Post::query()
            ->with(['user', 'likes'])
            ->when($this->user, fn (Builder $query): Builder => $query->whereNotIn('user_id', fn (QueryBuilder $query): QueryBuilder => $query->select('blocked_user_id')
                ->from('blocked_users')
                ->where('user_id', $this->user?->id)))

            ->latest('updated_at');
    }
}
