# Troubleshooting

**Vite HMR not working:**

```bash
# Clear Vite cache
rm -rf node_modules/.vite

# Rebuild node_modules
npm install
```

**PHP tests failing locally:**

```bash
# The test suite needs a MySQL database named laravel_test, not SQLite
mysql -e "CREATE DATABASE IF NOT EXISTS laravel_test;"
php artisan test
```

**Tests failing in CI:**

```bash
# CI copies .env.ci to .env, so check that file when the environment looks wrong
php artisan migrate --env=testing --force
```

**Queue jobs not processing:**

```bash
# Locally
php artisan queue:work

# In production the queue runs as a Forge daemon.
# Check the daemon in the Forge panel and confirm the deploy script
# ends with php artisan queue:restart
```

**Scheduled task did not run in production:**
Check the Forge Scheduler for the site. Everything in `routes/console.php` depends on `schedule:run` firing every minute. If the scheduler is down, the privacy cleanup commands stop running and personal data is kept longer than intended. The sitemap is not affected, since it is served from a route (see [seo.md](seo.md)).

**A config or environment change has no effect in production:**

```bash
# Cached config survives an env edit until you rebuild it
php artisan config:cache
```

**A page arrives without server-rendered HTML:**

```bash
# Laravel falls back to rendering in the browser without an error, so check the SSR server first
php artisan inertia:check-ssr

# Locally: build both bundles and start the server
npm run build:ssr
php artisan inertia:start-ssr
```

Admin pages are never rendered on the server, see [frontend.md](frontend.md#server-side-rendering). When the server runs but a page still arrives empty, the render failed: the error is in the output of `inertia:start-ssr` (in production, the log of the Forge daemon). The usual cause is a browser API such as `window` or `document` used during setup.

**`Hydration completed but contains mismatches` in the browser console:**
The server and the browser rendered different HTML for the page. After a deploy, check that the SSR server was restarted (`php artisan inertia:stop-ssr`), so it does not render with an old bundle. Otherwise the cause is in the code, see the rules in [frontend.md](frontend.md#server-side-rendering).

**Assets not loading:**

```bash
# Rebuild assets
npm run build

# Clear Laravel caches
php artisan cache:clear
php artisan view:clear
```
