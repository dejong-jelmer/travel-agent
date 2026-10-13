# Deployment

Production runs on **Laravel Forge**. The server has PHP, Composer, a queue worker and the Laravel scheduler available, so nothing needs to be bundled into the repository.

## Pipeline

1. Work happens on feature branches and is merged into `main` through a pull request.
2. Tests, Pint and PHPStan run on pull requests to `main` and on pushes to `main`. The AI review runs on every pull request except those targeting `production`.
3. Merging `main` into the **`production`** branch and pushing triggers a deploy.
4. Forge **Quick Deploy** picks up the push automatically and runs the deploy script on the server.

There is no deployment workflow in GitHub Actions. GitHub Actions only runs checks, Forge does the deploying. Checks do not run on the `production` branch itself, so `main` has to be green before it is merged.

## What the Deploy Script Does

The script is managed in the Forge panel, not in this repository. It performs the usual Forge sequence:

```bash
git pull origin production
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
php artisan migrate --force
npm ci && npm run build:ssr
php artisan config:cache
php artisan view:cache
php artisan terms:generate-pdf
php artisan queue:restart
php artisan inertia:stop-ssr
```

Four things are worth remembering when you change the deploy script:

- `queue:restart` is required after every deploy, otherwise the queue daemon keeps running the old code.
- `npm run build:ssr` builds the browser bundle and the SSR bundle in `bootstrap/ssr/`. `inertia:stop-ssr` makes the SSR server exit, after which its daemon starts it again with the new bundle; without it, the server keeps rendering the old pages, which then no longer match the new browser bundle. Run it after the new release is active. It fails when no SSR server is running, which marks the deploy as failed, so it only belongs in the script once the daemon runs (see [Server Processes](#server-processes)).
- Re-running `config:cache` is what makes a changed environment variable take effect.
- `terms:generate-pdf` rewrites the stored terms PDF. The app only generates it when the file is missing, and `storage/` is shared between releases, so without this step a changed terms version, template or PDF setting never reaches the download or the booking confirmation mail.

Deploys use the zero downtime layout, where each release gets its own directory and `current` is a symlink to the active one. Anything written into the release directory at runtime is gone after the next deploy, which is why generated files do not belong in `public/`.

## Server Processes

| Process      | Managed in      | Notes                                                                                                                |
| ------------ | --------------- | -------------------------------------------------------------------------------------------------------------------- |
| Scheduler    | Forge Scheduler | Runs `php artisan schedule:run` every minute, which drives `routes/console.php`                                      |
| Queue worker | Forge Daemon    | Runs `php artisan queue:work` on the `database` queue connection, needed for newsletter campaigns and mail listeners |
| SSR server   | Forge Daemon    | Runs `php artisan inertia:start-ssr` in the `current` directory, renders the first visit of public pages to HTML      |

The SSR daemon is set up through the Inertia integration in the site's application panel in Forge, which creates the daemon and can add `inertia:stop-ssr` to the deploy script. A plain Forge daemon running `php artisan inertia:start-ssr` in `/home/forge/<site>/current` does the same. The daemon needs the SSR bundle to start, so the first time goes in this order:

1. Change `npm run build` to `npm run build:ssr` in the deploy script, without `inertia:stop-ssr` yet, and deploy. The site keeps rendering in the browser until the SSR server runs.
2. Switch on the Inertia integration (or add the daemon), which starts the SSR server with the bundle from that deploy.
3. Add `php artisan inertia:stop-ssr` to the end of the deploy script, unless the integration already did.

The site keeps working when the SSR server is down: Laravel then sends the page without HTML and the browser renders it, as before server-side rendering. Visitors only see the content a little later, and search engines get an empty page, so treat a stopped SSR daemon as a problem for SEO rather than an outage. `php artisan inertia:check-ssr` tells whether it is running. See [frontend.md](frontend.md#server-side-rendering) for how it works.

## Environment

Production environment variables are edited in the Forge panel, not in a committed file. `APP_URL` has to match the live domain exactly, since Ziggy, sitemap URLs and links in outgoing email are all derived from it. Run a deploy or `php artisan config:cache` after changing anything, otherwise the cached config keeps the old value.

## Console Commands and Scheduled Tasks

Commands live in `app/Console/Commands/` and are auto-discovered. The schedule is defined in `routes/console.php` and runs on the server through the Forge scheduler.

| Command                               | Schedule | Purpose                                                        |
| ------------------------------------- | -------- | -------------------------------------------------------------- |
| `bookings:anonymize`                  | Yearly   | Anonymize bookings whose retention period has expired          |
| `bookings:anonymize-special-requests` | Daily    | Anonymize special requests after the return date has passed    |
| `trip-requests:purge`                 | Monthly  | Delete trip requests without a booking that exceeded retention |
| `newsletter:purge-unsubscribed`       | Monthly  | Remove unsubscribed newsletter subscribers                     |

These commands accept `--dry-run` to preview without changing anything, plus `--years` or `--days` to override the retention period from `config/privacy.php`.

Five commands are not scheduled. They are run manually, except that the deploy script also runs `terms:generate-pdf`:

- `sitemap:generate` - Write the sitemap to a file, for inspection or a manual export. The site itself serves `/sitemap.xml` from a route, so this is not needed in production.
- `newsletter:send-scheduled-campaigns` - Dispatch campaigns that are queued for sending
- `terms:generate-pdf` - Regenerate the terms and conditions PDF. Production runs it on every deploy, locally you run it after changing the terms view or version.
- `image-variants:generate` - Generate the responsive WebP variants of all uploaded images
- `og-images:generate` - Generate the Open Graph derivatives of all hero images (`--force` regenerates existing ones)

The anonymize and purge commands carry privacy weight. If the scheduler stops running, personal data is retained longer than intended, so treat a broken scheduler as urgent rather than cosmetic.
