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

**Assets not loading:**

```bash
# Rebuild assets
npm run build

# Clear Laravel caches
php artisan cache:clear
php artisan view:clear
```
