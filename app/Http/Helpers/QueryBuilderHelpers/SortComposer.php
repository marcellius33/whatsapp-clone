<?php

namespace App\Http\Helpers\QueryBuilderHelpers;

use ReflectionClass;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\Enums\SortDirection;

class SortComposer
{
    public static function composeSorts(array $options, AllowedSort $default, ?string $value)
    {
        return [
            'options' => self::getNames($options),
            'default' => self::getDefaultName($default),
            'value'   => $value,
        ];
    }

    private static function getNames(array $allowedSorts): array
    {
        $names = [];
        foreach ($allowedSorts as $allowedSort) {
            $names[] = $allowedSort->getName();
        }

        return $names;
    }

    private static function getDefaultName(AllowedSort $allowedSort): string
    {
        $direction = (new ReflectionClass(AllowedSort::class))->getProperty('defaultDirection')->getValue($allowedSort);

        return ($direction === SortDirection::DESCENDING ? '-' : '') . $allowedSort->getName();
    }
}
