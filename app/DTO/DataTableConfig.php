<?php

namespace App\DTO;

class DataTableConfig
{
    public function __construct(
        public readonly array $searchable,
        public readonly array $searchableRelations,
        public readonly array $filterable,
        public readonly array $sortable,
        public readonly array $sortableBelongsTo,
        public readonly array $sortableBelongsToMany,
        public readonly array $defaultSort
    ) {}
}
