<?php

namespace App\QueryBuilders\Base;

use App\Http\Helpers\QueryBuilderHelpers\FilterComposer;
use App\Http\Helpers\QueryBuilderHelpers\SortComposer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

abstract class BaseQueryBuilder
{
    public function __construct(protected Model|string $model)
    {
    }

    public function getQueryBuilder(...$args): QueryBuilder
    {
        $queryBuilder =  QueryBuilder::for($this->getBuilder(...$args))
            ->allowedFilters($this->getAllowedFilters())
            ->allowedSorts($this->getAllowedSorts())
            ->defaultSort($this->getDefaultSort());

        if (empty($queryBuilder->getQuery()->columns)) {
            $table = $this->getTable($this->model);
            $queryBuilder->select("$table.*");
        }

        return $queryBuilder;
    }

    public function getResource(Request $request): array
    {
        return [
            'filters'   => FilterComposer::composeFilters($this->getAllowedFilters(), $request->input('filter', [])),
            'sorts'     => SortComposer::composeSorts($this->getAllowedSorts(), $this->getDefaultSort(), $request->input('sort')),
        ];
    }

    protected function getTable(Model|string $model): string
    {
        if (is_string($model)) {
            return $model;
        }

        return $model->getTable();
    }

    abstract public function getBuilder(...$args): Builder;

    abstract public function getAllowedFilters(): array;

    abstract public function getAllowedSorts(): array;

    abstract public function getDefaultSort(): AllowedSort;
}
