<?php

namespace App\QueryBuilders;

use App\Models\Message;
use App\QueryBuilders\Base\BaseQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedSort;

class MessageQueryBuilder extends BaseQueryBuilder
{
    public function __construct()
    {
        parent::__construct(new Message());
    }

    public function getBuilder(...$args): Builder
    {
        $chatRoom = $args[0];

        return Message::where('chat_room_id', $chatRoom->id);
    }

    public function getAllowedFilters(): array
    {
        return [
        ];
    }

    public function getAllowedSorts(): array
    {
        return [
            AllowedSort::field('sent_at'),
        ];
    }

    public function getDefaultSort(): AllowedSort
    {
        return AllowedSort::field('sent_at');
    }
}
