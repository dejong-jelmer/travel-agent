<?php

namespace App\Http\Controllers\Traits;

trait HasPageMetadata
{
    /**
     * @param  string  $key  Translation key
     * @return string translated page title with '| {app.name}' concat
     */
    protected function pageTitle(string $key): string
    {
        return __($key).' | '.config('app.name');
    }

    /**
     * @param  string  $key  Translation key
     * @param  array  $overrides  Custom SEO values to override defaults
     * @return array SEO metadata array with title, description, og_image
     */
    protected function pageSeo(string $key, array $overrides = []): array
    {
        return array_merge([
            'title' => __("{$key}.title").' | '.config('app.name'),
            'description' => __("{$key}.description"),
            'og_image' => asset(config('seo.default_og_image', 'images/og_image.jpg')),
        ], $overrides);
    }
}
