<?php

namespace App\QueryBuilders;

use App\Models\Contact;
use App\QueryBuilders\Base\BaseQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class ContactQueryBuilder extends BaseQueryBuilder
{
    public function __construct()
    {
        parent::__construct(new Contact());
    }

    public function getBuilder(...$args): Builder
    {
        $user = auth()->user();

        return Contact::with('contactUser')
            ->where('user_id', $user->id);
    }

    public function getAllowedFilters(): array
    {
        return [
            AllowedFilter::partial('username', 'contactUser.username'),
            AllowedFilter::partial('phone_number', 'contactUser.phone_number'),
            AllowedFilter::partial('name', 'contactUser.name'),
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
