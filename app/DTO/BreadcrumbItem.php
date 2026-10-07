<?php

namespace App\DTO;

use App\DTO\Traits\ArrayableDTO;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, string|null>
 */
class BreadcrumbItem implements Arrayable
{
    use ArrayableDTO;

    /**
     * @param  string|null  $url  Absolute URL, null for the current page
     */
    public function __construct(
        public readonly string $label,
        public readonly ?string $url = null,
    ) {}
}
