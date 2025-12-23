<?php

namespace App\Http\Controllers\Traits;

use App\DTO\DataTableConfigData;
use Illuminate\Database\Eloquent\Builder;

trait HasDataTableFilters
{
    /**
     * Apply DataTable filters (search, filters, sort) to a query
     *
     * @param  Builder  $query  The Eloquent query builder
     * @param  DataTableConfigData  $config  Configuration DTO containing filter settings
     * @return Builder The modified query builder
     */
    protected function applyDataTableFilters(Builder $query, DataTableConfigData $config): Builder
    {
        // Apply search
        $query = $this->applySearch($query, $config);

        // Apply filters
        $query = $this->applyFilters($query, $config->filterable);

        // Apply sorting
        $query = $this->applySorting($query, $config);

        return $query;
    }

    /**
     * Apply search filters to the query
     *
     * Searches across direct model fields and relationship fields using LIKE queries.
     * User input is sanitized to prevent LIKE wildcard injection.
     *
     * @param  Builder  $query  The Eloquent query builder
     * @param  DataTableConfigData  $config  Configuration DTO with searchable fields
     * @return Builder The modified query builder
     */
    protected function applySearch(Builder $query, DataTableConfigData $config): Builder
    {
        $searchable = $config->searchable;
        $searchableRelations = $config->searchableRelations;

        if ($search = request('search')) {
            // Sanitize and validate search input
            $search = $this->sanitizeSearchInput($search);

            if ($search !== '') {
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
    protected function applyFilters(Builder $query, array $filterable): Builder
    {
        foreach ($filterable as $field) {
            if ($value = request($field)) {
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
     * @param  DataTableConfigData  $config  Configuration DTO with sortable fields and relation configs
     * @return Builder The modified query builder
     *
     * @throws \InvalidArgumentException If invalid database identifiers are detected
     */
    protected function applySorting(Builder $query, DataTableConfigData $config): Builder
    {
        $sortable = $config->sortable;
        $belongsToSorts = $config->belongsToSorts;
        $belongsToManySorts = $config->belongsToManySorts;
        $defaultSort = $config->defaultSort;

        $sortField = in_array(request('sort'), $sortable) ? request('sort') : null;
        $sortDirection = request('direction', 'desc');
        $sortDirection = in_array(strtolower($sortDirection), ['asc', 'desc'])
            ? $sortDirection
            : 'desc';

        if ($sortField) {
            // Check if it's a belongsToMany relation sort
            if (isset($belongsToManySorts[$sortField])) {
                $sortConfig = $belongsToManySorts[$sortField];
                $modelTable = $query->getModel()->getTable();
                $relatedTableName = $sortConfig['relation'];

                // Validate all identifiers to prevent SQL injection in raw queries
                if (! $this->isValidIdentifier($relatedTableName) ||
                    ! $this->isValidIdentifier($sortConfig['column']) ||
                    ! $this->isValidIdentifier($sortConfig['pivot_table']) ||
                    ! $this->isValidIdentifier($sortConfig['pivot_foreign_key']) ||
                    ! $this->isValidIdentifier($sortConfig['pivot_related_key'])) {
                    throw new \InvalidArgumentException('Invalid database identifier in belongsToManySorts configuration');
                }

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

                // Validate all identifiers to prevent SQL injection
                if (! $this->isValidIdentifier($sortConfig['table']) ||
                    ! $this->isValidIdentifier($sortConfig['column']) ||
                    ! $this->isValidIdentifier($sortConfig['foreign_key'])) {
                    throw new \InvalidArgumentException('Invalid database identifier in belongsToSorts configuration');
                }

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
     * Sanitize search input to prevent LIKE wildcard injection
     *
     * @param  string  $search  Raw search input
     * @return string Sanitized search string
     */
    protected function sanitizeSearchInput(string $search): string
    {
        // Trim whitespace
        $search = trim($search);

        // Limit length to prevent DoS
        $search = substr($search, 0, 255);

        // Escape LIKE wildcards (%, _) and backslash
        $search = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search);

        return $search;
    }

    /**
     * Validate that a database identifier (table/column name) is safe
     * Protects against SQL injection in DB::raw() statements
     *
     * @param  string  $identifier  Table or column name
     * @return bool True if valid, false if potentially dangerous
     */
    protected function isValidIdentifier(string $identifier): bool
    {
        // Only allow alphanumeric characters and underscores
        // No spaces, quotes, semicolons, or other SQL special characters
        return preg_match('/^[a-zA-Z0-9_]+$/', $identifier) === 1;
    }

    /**
     * Get current filter values for passing to frontend
     *
     * @param  array  $filterableFields  Array of filterable field names
     * @return array<string, mixed> Array of current filter values
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
