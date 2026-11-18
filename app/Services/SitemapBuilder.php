<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Trip;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapBuilder
{
    /**
     * Route names of the static pages that belong in the sitemap.
     *
     * Pages that are served with a noindex header, such as the privacy
     * statement and the terms and conditions, are deliberately left out.
     * Listing them here would contradict their own robots meta tag.
     *
     * @var list<string>
     */
    public const STATIC_ROUTES = [
        'home',
        'about',
        'trips.index',
        'blog.index',
        'guarantee',
        'vvkr',
    ];

    /**
     * Build a sitemap containing every publicly indexable page.
     *
     * URLs are built from route names, so they always match the canonical
     * URL the application itself generates.
     */
    public function build(): Sitemap
    {
        $sitemap = Sitemap::create();

        foreach (self::STATIC_ROUTES as $name) {
            $sitemap->add(Url::create(route($name)));
        }

        Trip::published()->withMax('departures', 'updated_at')->get()->each(
            fn (Trip $trip) => $sitemap->add(
                Url::create(route('trips.show', $trip->slug))
                    ->setLastModificationDate($this->tripLastModified($trip))
            )
        );

        BlogPost::published()->get()->each(
            fn (BlogPost $post) => $sitemap->add(
                Url::create(route('blog.show', $post->slug))
                    ->setLastModificationDate($post->updated_at)
            )
        );

        return $sitemap;
    }

    /**
     * When the page of a trip last changed: the trip itself, or one of its departures, which the page of a group
     * trip lists. Needs the latest departure loaded as departures_max_updated_at.
     */
    private function tripLastModified(Trip $trip): CarbonInterface
    {
        $departure = $trip->getAttribute('departures_max_updated_at');

        if ($departure === null) {
            return $trip->updated_at;
        }

        return Carbon::parse($departure)->max($trip->updated_at);
    }
}
