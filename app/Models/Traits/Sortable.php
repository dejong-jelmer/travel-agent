<?php

namespace App\Models\Traits;

use App\DTO\DataTableConfig;
use Illuminate\Database\Eloquent\Builder;

trait Sortable
{
    /**
     * Get the configuration settings for the data tables.
     */
    public static function dataTableConfig(): DataTableConfig
    {
        $instance = new static; // @phpstan-ignore new.static

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

    /**
     * For models that have scope query methods,
     * this method enables the sorting on these scopes.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query  an Eloquent Builder instance of the model
     */
    public static function applyScopeFilters(Builder $query): Builder
    {
        $instance = new static; // @phpstan-ignore new.static

        if (! isset($instance->scopeFilters)) {
            return $query;
        }

        foreach ($instance->scopeFilters as $param => $enumClass) {
            if ($filterValue = request($param)) {
                $enum = $enumClass::tryFrom($filterValue);
                if ($enum && method_exists($instance, $enum->value)) {
                    $query->{$enum->value}();
                }
            }
        }

        return $query;
    }

    /**
     * Get the models filterable fields property.
     *
     * @return array<string>
     */
    public static function filters(): array
    {
        $instance = new static; // @phpstan-ignore new.static

        return $instance->filterable ?? [];
    }
}
