<?php

namespace App\DTO;

class DataTableConfigData
{
    public function __construct(
        public readonly array $searchable = [],
        public readonly array $searchableRelations = [],
        public readonly array $filterable = [],
        public readonly array $sortable = [],
        public readonly array $belongsToSorts = [],
        public readonly array $belongsToManySorts = [],
        public readonly array $defaultSort = ['id', 'desc']
    ) {}
}
