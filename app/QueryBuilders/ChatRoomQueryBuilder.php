<?php

namespace App\QueryBuilders;

use App\Models\ChatRoom;
use App\Models\ChatRoomMember;
use App\QueryBuilders\Base\BaseQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class ChatRoomQueryBuilder extends BaseQueryBuilder
{
    public function __construct()
    {
        parent::__construct(new ChatRoom());
    }

    public function getBuilder(...$args): Builder
    {
        $user = auth()->user();

        return ChatRoom::with('creator')
            ->whereIn(
                'id',
                ChatRoomMember::where('user_id', $user->id)
                ->pluck('chat_room_id')
            );
    }

    public function getAllowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
        ];
    }

    public function getAllowedSorts(): array
    {
        return [
            AllowedSort::field('created_at'),
        ];
    }

    public function getDefaultSort(): AllowedSort
    {
        return AllowedSort::field('created_at');
    }
}
