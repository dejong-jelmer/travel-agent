<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasDataTableFilters
{
    /**
     * Apply DataTable filters (search, filters, sort) to a query
     *
     * @param  Builder  $query  The Eloquent query builder
     * @param  array  $config  Configuration array with keys:
     *                         - searchable: array of searchable fields (e.g., ['reference', 'name'])
     *                         - searchableRelations: array of relation.field (e.g., ['trip.name', 'contact.email'])
     *                         - filterable: array of filterable field names (e.g., ['status', 'payment_status'])
     *                         - sortable: array of sortable field names (e.g., ['id', 'reference', 'status'])
     *                         - belongsToSorts: array of belongsTo relation sort configs (e.g., ['trip' => ['table' => 'trips', 'foreign_key' => 'trip_id', 'column' => 'name']])
     *                         - belongsToManySorts: array of belongsToMany relation sort configs (e.g., ['countries' => ['relation' => 'countries', 'column' => 'name', 'pivot_table' => 'country_trip', 'pivot_foreign_key' => 'trip_id', 'pivot_related_key' => 'country_id']])
     *                         - defaultSort: array with [field, direction] (e.g., ['id', 'desc'])
     * @return Builder The modified query builder
     */
    protected function applyDataTableFilters(Builder $query, array $config): Builder
    {
        // Extract config
        $searchable = $config['searchable'] ?? [];
        $searchableRelations = $config['searchableRelations'] ?? [];
        $filterable = $config['filterable'] ?? [];
        $sortable = $config['sortable'] ?? [];
        $belongsToSorts = $config['belongsToSorts'] ?? [];
        $belongsToManySorts = $config['belongsToManySorts'] ?? [];
        $defaultSort = $config['defaultSort'] ?? ['id', 'desc'];

        // Apply search
        if ($search = request('search')) {
            $query->where(function ($q) use ($search, $searchable, $searchableRelations) {
                // Search in direct fields
                foreach ($searchable as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }

                // Search in relations
                foreach ($searchableRelations as $relationField) {
                    [$relation, $field] = explode('.', $relationField, 2);
                    $q->orWhereHas($relation, fn ($q) => $q->where($field, 'like', "%{$search}%"));
                }
            });
        }

        // Apply filters
        foreach ($filterable as $field) {
            if ($value = request($field)) {
                $query->where($field, $value);
            }
        }

        // Apply sorting
        $sortField = request('sort');
        $sortDirection = request('direction', 'desc');

        if ($sortField && in_array($sortField, $sortable)) {
            // Check if it's a belongsToMany relation sort
            if (isset($belongsToManySorts[$sortField])) {
                $sortConfig = $belongsToManySorts[$sortField];
                $modelTable = $query->getModel()->getTable();
                $relatedTableName = $sortConfig['relation'];

                // Use subquery with MIN to get the first related record alphabetically
                $query->leftJoinSub(
                    \DB::table($sortConfig['pivot_table'])
                        ->join($relatedTableName, $sortConfig['pivot_table'].'.'.$sortConfig['pivot_related_key'], '=', $relatedTableName.'.id')
                        ->select(
                            $sortConfig['pivot_table'].'.'.$sortConfig['pivot_foreign_key'],
                            \DB::raw('MIN('.$relatedTableName.'.'.$sortConfig['column'].') as first_related')
                        )
                        ->groupBy($sortConfig['pivot_table'].'.'.$sortConfig['pivot_foreign_key']),
                    'related_agg',
                    $modelTable.'.id',
                    '=',
                    'related_agg.'.$sortConfig['pivot_foreign_key']
                )
                    ->select($modelTable.'.*')
                    ->orderBy('related_agg.first_related', $sortDirection);
            }
            // Check if it's a belongsTo relation sort
            elseif (isset($belongsToSorts[$sortField])) {
                $sortConfig = $belongsToSorts[$sortField];
                $query->leftJoin(
                    $sortConfig['table'],
                    $query->getModel()->getTable().'.'.$sortConfig['foreign_key'],
                    '=',
                    $sortConfig['table'].'.id'
                )
                    ->select($query->getModel()->getTable().'.*')
                    ->orderBy($sortConfig['table'].'.'.$sortConfig['column'], $sortDirection);
            } else {
                // Regular field sort
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            // Default sort when no sort or reset
            $query->orderBy($defaultSort[0], $defaultSort[1]);
        }

        return $query;
    }

    /**
     * Get current filter values for passing to frontend
     *
     * @param  array  $filterableFields  Array of filterable field names
     * @return array Array of current filter values
     */
    protected function getCurrentFilters(array $filterableFields): array
    {
        $filters = [
            'search' => request('search', ''),
            'sort' => request('sort'),
            'direction' => request('direction', 'desc'),
        ];

        foreach ($filterableFields as $field) {
            $filters[$field] = request($field);
        }

        return $filters;
    }
}
