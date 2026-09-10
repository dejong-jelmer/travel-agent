<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Trip;
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

        Trip::published()->get()->each(
            fn (Trip $trip) => $sitemap->add(
                Url::create(route('trips.show', $trip->slug))
                    ->setLastModificationDate($trip->updated_at)
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
}
