# SEO

- **Meta tags** - Controllers share an `seo` payload per page through `shareSeo()`, with titles and descriptions from `resources/lang/*/seo.php`. Length limits live in `config/seo.php`.
- **Structured data** - Trips and blog posts expose JSON-LD through their models.
- **Open Graph images** - `OgImageService` provides a default image and 1200x630 JPEG derivatives of hero images. `php artisan og-images:generate` backfills them.
- **Robots** - `/privacy` and `/algemene-voorwaarden` are served with `noindex, follow`. All other public pages are indexable. `public/robots.txt` allows everything.
- **Sitemap** - Served from the `/sitemap.xml` route by `SitemapController`, which renders what `SitemapBuilder` produces. It covers the static pages plus every published trip and blog post, using route names so the URLs stay canonical. The rendered XML is cached for the lifetime set in `config/seo.php`, so newly published content appears within that window.

## Why the Sitemap Is a Route and Not a File

A generated file in `public/` lives inside a single release directory, so it disappears on the next deploy (see [deployment.md](deployment.md)), and it depends on the scheduler to stay current. A route has neither problem.

Never leave a `public/sitemap.xml` on the server: the web server serves that file before the request ever reaches Laravel, which silently shadows the route with stale content.
