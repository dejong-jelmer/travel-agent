<?php

namespace App\Services;

use App\DTO\DataTableConfig;
use App\Http\Requests\DataTableRequest;
use Illuminate\Database\Eloquent\Builder;

class DataTableService
{
    private array $validatedData = [];

    /**
     * Set validated request data for filtering/sorting
     *
     * @param  array  $data  Validated request data
     */
    public function withValidatedData(array $data): self
    {
        $this->validatedData = $data;

        return $this;
    }

    /**
     * Apply DataTable filters (search, filters, sort) to a query
     *
     * @param  Builder  $query  The Eloquent query builder
     * @param  DataTableConfig  $config  Configuration DTO containing filter settings
     * @return Builder The modified query builder
     */
    public function applySortFilters(Builder $query, DataTableConfig $config): Builder
    {
        // Apply search
        $query = $this->applySearch($query, $config);

        // Apply filters
        $query = $this->applyQueryFilters($query, $config->filterable);

        // Apply sorting
        $query = $this->applySorting($query, $config);

        return $query;
    }

    /**
     * Get current filter values for passing to frontend
     *
     * @param  array  $filterableFields  Array of filterable field names
     * @return array<string, mixed> Array of current filter values
     */
    public function getSortFilters(array $filterableFields): array
    {
        $filters = [
            'search' => $this->validatedData['search'] ?? '',
            'sort' => $this->validatedData['sort'] ?? null,
            'direction' => $this->validatedData['direction'] ?? 'desc',
        ];

        foreach ($filterableFields as $field) {
            $filters[$field] = $this->validatedData[$field] ?? null;
        }

        return $filters;
    }

    public function applyFilters(Builder $query, DataTableRequest $request, array $filterFields = []): Builder
    {
        $validatedData = array_merge(
            $request->validated(),
            $request->getValidatedFilters($filterFields)
        );

        $this->withValidatedData($validatedData)->applySortFilters($query, $query->getModel()::dataTableConfig()); // @phpstan-ignore staticMethod.notFound

        return $query;
    }

    /**
     * Apply search filters to the query
     *
     * Searches across direct model fields and relationship fields using LIKE queries.
     * User input is sanitized to prevent LIKE wildcard injection.
     *
     * @param  Builder  $query  The Eloquent query builder
     * @param  DataTableConfig  $config  Configuration DTO with searchable fields
     * @return Builder The modified query builder
     */
    private function applySearch(Builder $query, DataTableConfig $config): Builder
    {
        $searchable = $config->searchable;
        $searchableRelations = $config->searchableRelations;

        if ($search = $this->validatedData['search'] ?? null) {
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

        return $query;
    }

    /**
     * Apply exact match filters to the query
     *
     * Filters query results based on request parameters that exactly match field values.
     * Only applies filters for fields present in the filterable array.
     *
     * @param  Builder  $query  The Eloquent query builder
     * @param  array  $filterable  Array of field names that can be filtered
     * @return Builder The modified query builder
     */
    private function applyQueryFilters(Builder $query, array $filterable): Builder
    {
        foreach ($filterable as $field) {
            if ($value = $this->validatedData[$field] ?? null) {
                $query->where($field, $value);
            }
        }

        return $query;
    }

    /**
     * Apply sorting to the query
     *
     * Handles three types of sorting:
     * - Regular field sorting (orderBy on model fields)
     * - BelongsTo relation sorting (via LEFT JOIN)
     * - BelongsToMany relation sorting (via LEFT JOIN subquery with MIN aggregation)
     *
     * All database identifiers in sort configs are validated to prevent SQL injection.
     * Falls back to default sort if no valid sort field is provided.
     *
     * @param  Builder  $query  The Eloquent query builder
     * @param  DataTableConfig  $config  Configuration DTO with sortable fields and relation configs
     * @return Builder The modified query builder
     * */
    private function applySorting(Builder $query, DataTableConfig $config): Builder
    {
        $sortable = $config->sortable;
        $sortableBelongsTo = $config->sortableBelongsTo;
        $sortableBelongsToMany = $config->sortableBelongsToMany;
        $defaultSort = $config->defaultSort;

        $requestedSort = $this->validatedData['sort'] ?? null;
        $sortField = in_array($requestedSort, $sortable) ? $requestedSort : null;
        $sortDirection = $this->validatedData['direction'] ?? 'desc';
        $sortDirection = in_array(strtolower($sortDirection), ['asc', 'desc'])
            ? $sortDirection
            : 'desc';

        if ($sortField) {
            // Check if it's a belongsToMany relation sort
            if (isset($sortableBelongsToMany[$sortField])) {
                $sortConfig = $sortableBelongsToMany[$sortField];
                $modelTable = $query->getModel()->getTable();
                $relatedTableName = $sortConfig['relation'];

                // Use custom join_key if provided, otherwise default to 'id'
                $joinKey = $sortConfig['join_key'] ?? 'id';

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
                    $modelTable.'.'.$joinKey,
                    '=',
                    'related_agg.'.$sortConfig['pivot_foreign_key']
                )
                    ->select($modelTable.'.*')
                    ->orderBy('related_agg.first_related', $sortDirection);
            }
            // Check if it's a belongsTo relation sort
            elseif (isset($sortableBelongsTo[$sortField])) {
                $sortConfig = $sortableBelongsTo[$sortField];

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
            $query->orderBy($defaultSort['column'], $defaultSort['direction']);
        }

        return $query;
    }
}
