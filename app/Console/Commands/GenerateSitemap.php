<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\Trip;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate {--path= : Optioneel pad om de sitemap weg te schrijven}';

    protected $description = 'Genereer sitemap.xml met alle publieke pagina\'s';

    public function handle(): int
    {
        $sitemap = Sitemap::create();

        // Static pages, via route name so that the URL is always canonical
        $staticRoutes = [
            'home',
            'about',
            'trips.index',
            'blog.index',
            'guarantee',
            'vvkr',
            'terms',
            'privacy',
        ];

        foreach ($staticRoutes as $name) {
            $sitemap->add(Url::create(route($name)));
        }

        // Published trips
        Trip::published()->get()->each(function (Trip $trip) use ($sitemap) {
            $sitemap->add(
                Url::create(route('trips.show', $trip->slug))
                    ->setLastModificationDate($trip->updated_at)
            );
        });

        // Published blogposts
        BlogPost::published()->get()->each(function (BlogPost $post) use ($sitemap) {
            $sitemap->add(
                Url::create(route('blog.show', $post->slug))
                    ->setLastModificationDate($post->updated_at)
            );
        });

        $path = $this->option('path') ?: public_path('sitemap.xml');

        $sitemap->writeToFile($path);

        $this->info('Sitemap generated: '.$path);

        return self::SUCCESS;
    }
}
