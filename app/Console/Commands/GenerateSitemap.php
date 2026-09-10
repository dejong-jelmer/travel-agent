<?php

namespace App\Console\Commands;

use App\Services\SitemapBuilder;
use Illuminate\Console\Command;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate {--path= : Path to write the sitemap to}';

    protected $description = 'Write the sitemap to a file for inspection or manual export';

    /**
     * Write the sitemap to disk.
     *
     * The site serves its sitemap from the /sitemap.xml route, so this command
     * is not part of the deploy or the schedule. It defaults to a path outside
     * the public directory on purpose: a file at public/sitemap.xml would be
     * served by the web server instead of the route, and would go stale the
     * moment a trip or blog post is published.
     */
    public function handle(SitemapBuilder $builder): int
    {
        $path = $this->option('path') ?: storage_path('app/sitemap.xml');

        $builder->build()->writeToFile($path);

        $this->info('Sitemap written to: '.$path);

        return self::SUCCESS;
    }
}
