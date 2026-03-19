<?php

namespace App\Http\Controllers\Traits;

trait HasPageMetadata
{
    /**
     * @param  string  $key  Translation key
     * @return string translated page title with '| {app.name}' concat
     *
     * Supports two key conventions:
     *   - Nested: key points to an array with a 'title' sub-key (e.g. 'home.home_seo' → home.home_seo.title)
     *   - Direct: key points directly to the title string (e.g. 'trip.title_index')
     */
    protected function pageTitle(string $key): string
    {
        $withSuffix = __("{$key}.title");

        // Laravel returns the key itself when no translation is found.
        // If that happens, fall back to the key without the '.title' suffix.
        $title = $withSuffix !== "{$key}.title" ? $withSuffix : __($key);

        return $title.' | '.config('app.name');
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
            'og_image' => asset(config('seo.default_og_image', 'images/contact.webp')),
        ], $overrides);
    }
}
