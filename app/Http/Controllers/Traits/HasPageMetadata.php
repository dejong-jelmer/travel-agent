<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Facades\View;

trait HasPageMetadata
{
    /**
     * @param  string  $key  Translation key
     * @return string translated page title with '| {app.name}' concat
     *
     * Supports two key conventions:
     *   - Nested: key points to an array with a 'title' sub-key (e.g. 'home.home_seo' → home.home_seo.title)
     *   - Direct: key points directly to the title string (e.g. 'trip.title_index')
     *
     * Falls back to $key as literal string if no valid translation is found.
     */
    protected function pageTitle(string $key): string
    {
        $translation = __("{$key}.title");

        // __() returns an array when the key resolves to a nested group,
        // or the key string itself when no translation is found.
        if (is_array($translation) || $translation === "{$key}.title") {
            $translation = __($key);
        }

        return (is_string($translation) ? $translation : $key).' | '.config('app.name');
    }

    /**
     * @param  string  $key  Translation key
     * @param  array  $overrides  Custom SEO values to override defaults
     * @param  array  $jsonLd  Custom jsonLd values
     * @return array SEO metadata array with title, description, og_image (shared with view)
     */
    public function shareSeo(string $key, array $overrides = [], ?array $jsonLd = null): array
    {
        $seo = $this->pageSeo($key, $overrides);
        $jsonLd = $jsonLd ?? $this->getJsonLd($key);

        View::share('seo', $seo);
        View::share('jsonLd', $jsonLd);

        return $seo;
    }

    /**
     * @param  string  $key  Translation key
     * @param  array  $overrides  Custom SEO values to override defaults
     * @return array SEO metadata array with title, description, og_image
     */
    private function pageSeo(string $key, array $overrides = []): array
    {
        return array_merge([
            'title' => __("{$key}.title").' | '.config('app.name'),
            'description' => __("{$key}.description"),
            'og_image' => asset(config('seo.default_og_image')),
        ], $overrides);
    }

    private function getJsonLd(string $key): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'TravelAgency',
            'name' => config('app.name'),
            'url' => config('app.url'),
            'description' => __("{$key}.description"),
            'image' => asset(config('seo.default_og_image')),
            'logo' => asset('images/logos/logo-text.png'),
            'sameAs' => [
                // Instagram, LinkedIn, etc.
            ],
        ];
    }
}
