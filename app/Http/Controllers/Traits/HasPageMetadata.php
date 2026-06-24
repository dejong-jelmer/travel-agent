<?php

namespace App\Http\Controllers\Traits;

trait HasPageMetadata
{
    /**
     * @param  string  $key  Translation key
     * @return string translated page title with '| {app.name}' concat
     *
     * Supports two key conventions:
     *   - Nested: key points to an array with a 'title' sub-key (e.g. 'seo.home' → seo.home.title)
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
     * Build the SEO metadata array for a page, including its JSON-LD schema.
     *
     * The returned array is passed straight to the page as the `seo` Inertia prop;
     * the root Blade view reads it from `$page['props']['seo']` to render the head
     * tags. No global View state is mutated.
     *
     * @param  array  $overrides  Custom SEO values to override defaults
     * @param  string|null  $key  Translation key
     * @param  array|null  $jsonLd  Custom JSON-LD schema: null → generic TravelAgency
     *                              fallback, [] → no schema, array → that exact schema
     * @return array SEO metadata array with title, description, og_image, jsonLd
     */
    public function shareSeo(?string $key = null, array $overrides = [], ?array $jsonLd = null): array
    {
        $seo = $this->pageSeo($key, $overrides);
        $seo['jsonLd'] = $jsonLd ?? $this->getJsonLd($key);

        return $seo;
    }

    /**
     * @param  string|null  $key  Translation key
     * @param  array  $overrides  Custom SEO values to override defaults
     * @return array SEO metadata array with title, description, og_image
     */
    private function pageSeo(?string $key = null, array $overrides = []): array
    {
        $defaults = ['og_image' => asset(config('seo.default_og_image'))];

        if ($key !== null && $key !== '') {
            $defaults['title'] = __("{$key}.title").' | '.config('app.name');
            $defaults['description'] = __("{$key}.description");
        }

        return array_merge($defaults, $overrides);
    }

    private function getJsonLd(?string $key): array
    {
        return array_merge([
            '@context' => 'https://schema.org',
        ], $this->travelAgencySchema(), [
            'description' => $key ? __("{$key}.description") : __('seo.home.description'),
            'image' => asset(config('seo.default_og_image')),
            'logo' => asset(config('seo.logo')),
            'sameAs' => [
                array_filter([
                    config('socials.instagram'),
                    config('socials.linkedin'),
                ]),
            ],
        ]);
    }

    /**
     * The organization's schema.org node (name/url from a single source).
     *
     * Used standalone as the site-wide TravelAgency in getJsonLd(), and nested
     * as the `provider` of per-page schemas such as the TouristTrip in TripController.
     *
     * @return array<string, string>
     */
    protected function travelAgencySchema(): array
    {
        return [
            '@type' => 'TravelAgency',
            'name' => config('app.name'),
            'url' => config('app.url'),
            'logo' => asset(config('seo.logo')),
        ];
    }
}
