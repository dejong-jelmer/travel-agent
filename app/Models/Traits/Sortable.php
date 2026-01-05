<?php

namespace App\Models\Traits;

use App\DTO\DataTableConfig;
use Illuminate\Database\Eloquent\Builder;

trait Sortable
{
    public static function dataTableConfig(): DataTableConfig
    {
        $instance = new static;

        return new DataTableConfig(
            searchable: $instance->searchable ?? [],
            searchableRelations: $instance->searchableRelations ?? [],
            filterable: $instance->filterable ?? [],
            sortable: $instance->sortable ?? [],
            sortableBelongsTo: $instance->sortableBelongsTo ?? [],
            sortableBelongsToMany: $instance->sortableBelongsToMany ?? [],
            defaultSort: $instance->defaultSort ?? [
                'column' => 'id',
                'direction' => 'asc',
            ]
        );
    }

    public static function applyScopeFilters(Builder $query): void
    {
        $instance = new static;

        if (! isset($instance->scopeFilters)) {
            return;
        }

        foreach ($instance->scopeFilters as $param => $enumClass) {
            if ($filterValue = request($param)) {
                $enum = $enumClass::tryFrom($filterValue);
                if ($enum && method_exists($instance, $enum->value)) {
                    $query->{$enum->value}();
                }
            }
        }
    }
}
