<?php

namespace App\Http\Controllers\Traits;

use App\DTO\BreadcrumbTrail;
use App\DTO\OgImageData;
use App\Services\OgImageService;

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
     * @param  BreadcrumbTrail|null  $breadcrumbs  Trail added to the schema as a BreadcrumbList
     * @return array SEO metadata array with title, description, og_image, jsonLd
     */
    public function shareSeo(?string $key = null, array $overrides = [], ?array $jsonLd = null, ?BreadcrumbTrail $breadcrumbs = null): array
    {
        $seo = $this->pageSeo($key, $overrides);
        $schema = $jsonLd ?? $this->getJsonLd($key);

        // The BreadcrumbList sits next to the page schema in a @graph, so one script tag holds both.
        if ($breadcrumbs) {
            $schema = ['@graph' => array_values(array_filter([$schema, $breadcrumbs->toSchema()]))];
        }

        // Every schema gets the context, regardless of where it was built.
        $seo['jsonLd'] = $schema === []
            ? []
            : array_merge(['@context' => 'https://schema.org'], $schema);

        return $seo;
    }

    /**
     * Map an Open Graph image to the SEO keys the head partial renders.
     *
     * @return array{og_image: string, og_image_width: int, og_image_height: int, og_image_type: string, og_image_alt: string}
     */
    protected function ogImageSeo(OgImageData $image): array
    {
        return [
            'og_image' => $image->url,
            'og_image_width' => $image->width,
            'og_image_height' => $image->height,
            'og_image_type' => $image->type,
            'og_image_alt' => $image->alt,
        ];
    }

    /**
     * @param  string|null  $key  Translation key
     * @param  array  $overrides  Custom SEO values to override defaults
     * @return array SEO metadata array with title, description, robots and the og image keys
     */
    private function pageSeo(?string $key = null, array $overrides = []): array
    {
        $defaults = [
            'robots' => 'index, follow',
            ...$this->ogImageSeo(app(OgImageService::class)->default()),
        ];

        if ($key) {
            $defaults['title'] = __("{$key}.title").' | '.config('app.name');
            $defaults['description'] = __("{$key}.description");
        }

        return array_merge($defaults, $overrides);
    }

    private function getJsonLd(?string $key): array
    {
        return array_merge($this->travelAgencySchema(), [
            'description' => $key ? __("{$key}.description") : __('seo.home.description'),
            'image' => app(OgImageService::class)->default()->url,
            'logo' => asset(config('seo.logo')),
            'sameAs' => array_filter([
                config('socials.instagram'),
                config('socials.linkedin'),
            ]),
        ]);
    }

    /**
     * The organization's schema.org node (name/url from a single source).
     *
     * Used standalone as the site-wide TravelAgency in getJsonLd(), and nested
     * as the `provider` of per-page schemas such as the TouristTrip in TripController.
     *
     * @return array<string, mixed>
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
