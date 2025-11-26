<?php

namespace App\Http\Controllers\Traits;

trait HasPageMetadata
{
    protected function pageTitle(string $key): string
    {
        return __($key) . ' | ' . config('app.name');
    }

    protected function pageSeo(string $key, array $overrides = []): array
    {
        return array_merge([
            'title' => __("{$key}.title") . ' | ' . config('app.name'),
            'description' => __("{$key}.description"),
            'og_image' => asset('images/og_image.jpg'),
        ], $overrides);
    }
}
