<?php

namespace App\Http\Helpers\QueryBuilderHelpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use ReflectionClass;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Filters\FiltersPartial;

class FilterComposer
{
    public const TypeText = 'text';

    public const TextBehaviourPartial = 'partial';
    public const TextBehaviourExact = 'exact';
    public const Event = 'event';

    public static function composeFilters(array $allowedFilters, array $input): array
    {
        $filters = [];
        foreach ($allowedFilters as $allowedFilter) {
            $methodName = self::getMethodName($allowedFilter);
            $filters[] = self::callMethodName($methodName, $allowedFilter, $input);
        }

        return $filters;
    }

    private static function exactText(string $name, ?string $value, ?string $default): array
    {
        return [
            'name'      => $name,
            'label'     => self::getLabel($name),
            'type'      => self::TypeText,
            'behaviour' => self::TextBehaviourExact,
            'default'   => $default,
            'value'     => $value,
        ];
    }

    private static function partialText(string $name, ?string $value, ?string $default): array
    {
        return [
            'name'      => $name,
            'label'     => self::getLabel($name),
            'type'      => self::TypeText,
            'behaviour' => self::TextBehaviourPartial,
            'default'   => $default,
            'value'     => $value,
        ];
    }

    private static function callMethodName(string $methodName, AllowedFilter $allowedFilter, array $input): array
    {
        $name = $allowedFilter->getName();
        $default = $allowedFilter->getDefault();
        $value = Arr::get($input, $name);

        if ($methodName === 'singleOption') {
            $filterClass = (new ReflectionClass(AllowedFilter::class))->getProperty('filterClass')->getValue($allowedFilter);
            $options = $filterClass->getOptions();

            return self::$methodName($name, $value, $default, $options);
        }

        return self::$methodName($name, $value, $default);
    }

    private static function getLabel(string $name): string
    {
        return Str::title(str_replace('_', ' ', $name));
    }

    private static function getMethodName(AllowedFilter $allowedFilter): string
    {
        $filterClass = (new ReflectionClass(AllowedFilter::class))->getProperty('filterClass')->getValue($allowedFilter);

        if (($filterClass instanceof FiltersPartial)) {
            return 'partialText';
        } else {
            return 'exactText';
        }
    }
}
