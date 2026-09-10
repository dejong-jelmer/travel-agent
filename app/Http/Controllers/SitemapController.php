<?php

namespace App\Http\Controllers;

use App\Services\SitemapBuilder;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Cache key holding the rendered sitemap XML.
     */
    public const CACHE_KEY = 'sitemap.xml';

    /**
     * Serve the sitemap for every publicly indexable page.
     *
     * The rendered XML is cached so a crawler cannot make the application
     * query all published trips and blog posts on every request. The cache
     * expires by itself, which means newly published content shows up after
     * at most the lifetime configured in config/seo.php.
     */
    public function __invoke(SitemapBuilder $builder): Response
    {
        $xml = Cache::remember(
            self::CACHE_KEY,
            (int) config('seo.sitemap_cache_ttl'),
            fn (): string => $builder->build()->render()
        );

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }
}
