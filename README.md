# Omdat We Reizen: Sustainable Travel Agent
---
[![run-tests](https://github.com/dejong-jelmer/travel-agent/actions/workflows/run-tests.yml/badge.svg)](https://github.com/dejong-jelmer/travel-agent/actions/workflows/run-tests.yml)
[![GitHub Code Style Action Status](https://github.com/dejong-jelmer/travel-agent/actions/workflows/lint.yml/badge.svg)](https://github.com/dejong-jelmer/travel-agent/actions/workflows/lint.yml)

A Laravel 12 + Inertia.js + Vue 3 application promoting sustainable European train travel. It combines a public Dutch website with a bilingual admin panel, and runs in production on Laravel Forge.

> **Mission**: Making slow travel the standard for European trips through curated, culturally rich train-based journeys with minimal carbon footprint.

**Live site:** <!-- TODO: add the production URL --> · **Built by:** Jelmer de Jong, solo, from first commit (February 2025) to production

<!-- TODO: add 2-3 screenshots, e.g. docs/screenshots/home.png, trip-detail.png, admin-booking.png -->

---

## Project Highlights

This is a real business application in daily use, not a demo. It covers the full path from content management to customer requests, bookings and transactional email. I designed, built, tested and deployed it on my own. These are the parts most worth a reviewer's time:

- **A shared price specification for PHP and JavaScript.** The booking price is calculated on the server and live in the browser. The two implementations cannot share code, so they share [test vectors](tests/fixtures/booking-price-vectors.json) instead. If the formula changes on one side only, the other side's test fails. Amounts are handled as integer cents with `moneyphp/money`.
- **A layered backend.** Form Request → DTO → Service → Model, with response macros and events driving the email flows. Controllers stay thin and the business rules can be unit tested in isolation.
- **Privacy by design (GDPR).** Scheduled commands anonymize bookings and special requests and purge stale trip requests and unsubscribed subscribers, each with a `--dry-run` mode. Retention periods are configured in one place.
- **Performance and SEO work.** Responsive WebP variants served through `srcset`, generated Open Graph images, JSON-LD structured data and a sitemap served from a route so it cannot go stale between deploys.
- **Spam and abuse protection without CAPTCHAs.** Rate limiting, honeypots, server-side spam detection and obfuscated contact details.
- **Quality gates on every pull request.** About 220 PHPUnit tests against MySQL, Vitest for the frontend, Pint, PHPStan (Larastan level 5) and an automated AI review. Deploys to production go through a separate `production` branch and Laravel Forge.

---

## Table of Contents

- [Project Highlights](#project-highlights)
- [Tech Stack](#tech-stack)
- [Features](#features)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Development](#development)
- [Testing](#testing)
- [Architecture](#architecture)
- [Localization](#localization)
- [Console Commands and Scheduled Tasks](#console-commands-and-scheduled-tasks)
- [SEO](#seo)
- [Security](#security)
- [Deployment](#deployment)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)

---

## Tech Stack

| Layer | Technology | Version |
|-------|------------|---------|
| **Backend** | Laravel | 12 |
| **Language** | PHP | 8.4 |
| **Database** | MySQL | 8.0+ |
| **Frontend Framework** | Vue.js | 3.5 |
| **SPA Bridge** | Inertia.js | 2.0 |
| **Styling** | Tailwind CSS | 3.4 |
| **Build Tool** | Vite | 6 |
| **PHP Testing** | PHPUnit | 11 |
| **JS Testing** | Vitest | 4 |
| **Email Service** | Mailjet API | 3.1 |
| **CI** | GitHub Actions | - |
| **Hosting** | Laravel Forge | - |

**Key backend dependencies:**
- `inertiajs/inertia-laravel` - Server-side Inertia adapter
- `tightenco/ziggy` - Laravel routes available in JavaScript
- `mailjet/laravel-mailjet` - Transactional email delivery
- `moneyphp/money` - Money value objects for price calculation
- `propaganistas/laravel-phone` - Phone number validation
- `barryvdh/laravel-dompdf` - PDF generation for terms and sustainability documents
- `spatie/laravel-sitemap` - Sitemap generation
- `spatie/laravel-cookie-consent` - Cookie consent banner
- `staudenmeir/eloquent-has-many-deep` - Deep relations on trip and booking models
- `laravel/sanctum` - Token authentication for the API routes

**Key frontend dependencies:**
- `@inertiajs/vue3` - Client-side Inertia adapter
- `vue-i18n` - Admin panel translations
- `@tiptap/vue-3` - Rich text editing for blog posts and trip content
- `@vuepic/vue-datepicker` - Date picker component
- `@headlessui/vue` and `@heroicons/vue` - Accessible UI components and icons
- `vue-honeypot` - Honeypot spam protection
- `sortablejs` - Drag and drop ordering for images and itineraries
- `vue-toastification` - Flash message toasts
- `happy-dom` - DOM environment for testing

---

## Features

### Public website
- **Trip browsing** - Curated European train journeys with itineraries, images and practical information
- **Trip requests** - Multi-step request form with real-time validation and price indication
- **Blog** - Travel stories with rich text content and published or draft states
- **Newsletter subscriptions** - Double opt-in with token-based confirmation links
- **Informational pages** - About, guarantee, VvKR membership, privacy and terms
- **PDF downloads** - Terms and sustainability documents generated on the server
- **Responsive design** - Mobile-first UI with custom breakpoints

### Admin panel
- **Dashboard** - Counters for new bookings and new trip requests
- **Trip management** - Trips, prices, itineraries, destinations, trip items and image ordering
- **Booking management** - Bookings, travelers, contacts, cost items and change history
- **Trip request handling** - Follow up on incoming requests with status tracking
- **Blog post management** - Create, edit and publish posts
- **Newsletter campaigns** - Compose, test-send and dispatch campaigns to subscribers
- **Settings** - Key-value application settings shared with the frontend

### Technical features
- Server-side and client-side validation with DTOs
- Event-driven architecture for transactional email
- Queued newsletter campaign dispatch
- Rate limiting (25 requests/min on public forms)
- Honeypot spam protection and email or phone obfuscation
- CSRF protection on all forms
- Token-based links for newsletter confirmation and unsubscribe
- Structured data (JSON-LD) and per-page meta tags for SEO
- Automatic privacy cleanup of old bookings and requests

---

## Prerequisites

### Required
- **PHP** 8.4 or higher
- **Composer** 2.x
- **Node.js** 18+ (with npm)
- **MySQL** 8.0+ (or compatible database)

### PHP Extensions
```
bcmath, ctype, curl, dom, fileinfo, json, mbstring,
openssl, pcre, pdo, pdo_mysql, tokenizer, xml
```

### Development Tools (Optional)
- Laravel Pint (code formatting)
- PHPStan via Larastan (static analysis, level 5)
- Laravel IDE Helper
- Laravel Pail (log tailing)

---

## Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd travel-agent
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Configure your `.env` file with at least:
```env
APP_URL=http://travel-agent.test
APP_LOCALE=nl
APP_FALLBACK_LOCALE=en

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

MAILJET_API_KEY=your_api_key
MAILJET_API_SECRET=your_api_secret
```

`APP_URL` matters more than it looks. Ziggy, the sitemap and every absolute URL in outgoing email are built from it.

### 4. Database Setup
```bash
php artisan migrate
php artisan db:seed
```

### 5. Build Assets
```bash
npm run build
```

---

## Development

### Start Development Environment
```bash
composer dev
```

This runs concurrently:
- `php artisan serve` - Laravel development server
- `php artisan queue:listen --tries=1` - Queue worker
- `npm run dev` - Vite HMR server

### Available Commands

#### Build Commands
```bash
npm run dev          # Start Vite dev server with HMR
npm run build        # Build production assets
```

#### Code Quality
```bash
./vendor/bin/pint              # Format code (Laravel Pint)
./vendor/bin/phpstan analyse   # Static analysis (PHPStan level 5 via Larastan)

php artisan ide-helper:generate  # Generate IDE helper files
php artisan ide-helper:meta      # Generate PhpStorm meta file
```

#### Database
```bash
php artisan migrate           # Run migrations
php artisan migrate:fresh     # Drop all tables and re-migrate
php artisan db:seed           # Seed database
```

---

## Testing

### PHP Tests (PHPUnit)
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run single test file
php artisan test tests/Feature/BookingTest.php

# With coverage
php artisan test --coverage
```

### JavaScript/Vue Tests (Vitest)
```bash
npm test              # Watch mode
npm run test:run      # Single run, no file parallelism
npm run test:ui       # Vitest UI
npm run test:coverage # With coverage
```

### Test Configuration
- **PHP**: Runs against MySQL, not SQLite. `phpunit.xml` sets `DB_CONNECTION=mysql` and `DB_DATABASE=laravel_test`, so that database has to exist locally. Feature tests use `RefreshDatabase`.
- **JavaScript**: Uses the `happy-dom` environment with the `vmThreads` pool, configured in `vite.config.js` under the `test` key. Setup lives in `resources/js/__tests__/setup.js`.
- **CI**: `run-tests.yml` runs on pull requests and pushes to `main`, with a MySQL 8 service container, `.env.ci` as environment, a Vite build to verify assets compile, and both test suites.
- **Lint CI**: `lint.yml` runs Pint in `--test` mode and PHPStan on the same triggers.
- **AI review**: `ai-code-review.yml` posts an automated review on pull requests, except on the `production` branch.

---

## Architecture

### Request Flow Pattern
```
HTTP Request
  → FormRequest (validation)
  → DTO (type-safe data)
  → Service (business logic)
  → Model (persistence)
  → ResponseMacro (formatted response)
```

**Example Flow:**
```php
CreateBookingRequest
  → CreateBookingData::fromRequest()
  → PriceCalculatorService::forTrip()
  → BookingService::create()
  → Booking::create()
  → response()->booking($booking, ModelAction::Created)
```

### Key Architectural Patterns

#### Data Transfer Objects (DTOs)
Located in `app/DTO/`:
- `CreateBookingData` / `UpdateBookingData` - Main booking operations
- `BookingContactData` / `BookingTravelerData` / `BookingCostItemData` - Nested structures
- `TripPriceData` - Price calculation results
- `ContactFormData` / `ContactDetails` - Contact form and contact information
- `DataTableConfig` - Admin datatable configuration
- Uses the `ArrayableDTO` trait for serialization
- Uses the `BookingDataParser` trait for parsing validated data

#### Service Layer
Located in `app/Services/`:
- `BookingService` - Create and update bookings with travelers
- `TripRequestService` - Handle incoming trip requests
- `PriceCalculatorService` - Calculate trip prices per date and traveler count
- `FeesAndFundsService` - Statutory fees and guarantee fund amounts
- `ContactDetailsService` - Format contact information
- `PhoneNumberService` - Phone validation and formatting
- `AntiSpamEmailService` - Spam detection logic
- `CountryService` / `DestinationService` - Geographic data
- `NewsletterCampaignService` - Newsletter campaign management
- `TripItemService` - Trip item management
- `SlugService` - Slug generation for trips and blog posts
- `TermsPdfService` / `SustainabilityPdfService` - PDF document generation
- `DataTableService` - Admin datatable queries
- `SystemHealthService` - System health checks
- `Services/Validation/` - Shared validation rule sets per domain

#### Event-Driven Email System
```
BookingCreated Event
  → SendBookingConfirmationEmail Listener
  → NotifyAdminOfNewBooking Listener

BookingFailed Event
  → NotifyAdminOfFailedBooking Listener

TripRequestCreated Event
  → SendTripRequestConfirmationEmail Listener
  → NotifyAdminOfTripRequest Listener

NewsletterSubscriptionRequested Event
  → SendNewsletterConfirmationEmail Listener
  → NotifyAdminOfNewsletterSubscription Listener
```

Newsletter campaigns are dispatched through the `SendNewsletterCampaign` queued job, so a queue worker has to be running.

#### Response Macros
Custom response macro in `AppServiceProvider`:
```php
Response::macro('booking', function (Booking $booking, ModelAction $action) {
    return BookingResponse::make($booking)->toResponse($action);
});

// Usage
return response()->booking($booking, ModelAction::Created);
```

### Component Architecture (Atomic Design)

```
resources/js/
├── Components/
│   ├── Atoms/          # Button, Label, Icon
│   ├── Molecules/      # Input, Select, DatePicker, Newsletter
│   └── Organisms/      # BookingForm, Header, Footer, TripItinerary
├── Icons/              # Icon components
├── Pages/              # Inertia page components (public and Admin/)
├── Templates/          # Page layouts
├── Validators/         # Client-side validation rules
├── Support/            # Frontend helpers
└── Composables/        # Reusable Vue composition functions
    ├── useBooking.js            # Booking form state
    ├── useBookingSteps.js       # Multi-step navigation
    ├── useBookingValidation.js  # Multi-step form validation
    ├── useBookingPrice.js       # Live price calculation
    ├── useContactForm.js        # Contact form handling
    ├── useAntiSpamLinks.js      # Email and phone obfuscation
    ├── useLocale.js             # Admin locale switching
    ├── useDateFormatter.js      # Date formatting
    ├── useCharacterCounter.js   # Input counters
    ├── useRevealEffect.js       # Scroll reveal animations
    └── useToastWatcher.js       # Flash message toasts
```

### Inertia.js Integration

**Global Shared Data** (configured in `AppServiceProvider`):
- Flash messages (`success`, `error`)
- `adminStats` - new bookings and new trip requests count (only on admin routes, only when authenticated)
- `settings` - all `Setting` model key-value pairs
- `breadcrumbs` - auto-generated via `Breadcrumbs::generate()`

**Auto-registered Components:**
All components in `Components/`, `Templates/`, and `Icons/` are globally available.

### Routing

`routes/web.php` holds the public site and the `admin/` group behind the `auth` middleware. Public pages are Dutch-only URLs: `/`, `/over-mij`, `/reizen`, `/reizen/{slug}`, `/mijn-verhalen`, `/mijn-verhalen/{slug}`, `/garantie`, `/over-vvkr`, `/privacy` and `/algemene-voorwaarden`.

`routes/api.php` exposes a small Sanctum-protected surface (`/login`, `/logout`, `/user`). `routes/api_testing.php` is only loaded for testing purposes. `/up` and `/health` are health check endpoints.

### Custom Configuration

**Tailwind Breakpoints** (`resources/js/screens.js`):
```js
{ phone: '0px', tablet: '600px', laptop: '900px', desktop: '1350px', wide: '1600px' }
```

**Custom Fonts** (see `tailwind.config.js`):
- `font-poppins` - Primary UI font
- `font-caveat` - Handwritten accent font

**Custom Color Palette** (see `tailwind.config.js`):

*Semantic color system for sustainable travel aesthetic:*

- **Brand Identity**
  - `brand.primary` (#2d5f6e) - Main brand color (teal)
  - `brand.accent` (#f59e0b) - Accent/highlight color (amber)
  - `brand.secondary` (#f5f0e8) - Light background
  - `brand.text` (#1e2d3d) - Primary text
  - `brand.light` (#4d6f80) - Light brand tint
  - `brand.subtle` (#afcb98) - Subtle green
  - `brand.earth` (#dcc7aa) - Warm earth tone
  - `brand.link` (#82b2ca) - Link color

- **Status Feedback**
  - `status.error` (#dc3545) - Error states and validation failures
  - `status.success` (#198754) - Success states and confirmations
  - `status.warning` (#ffc107) - Warning states and alerts
  - `status.info` (#0d6efd) - Informational states

**Application config** lives in dedicated files per domain: `config/admin.php`, `config/blog.php`, `config/booking.php`, `config/contact.php`, `config/health.php`, `config/images.php`, `config/locales.php`, `config/newsletter.php`, `config/privacy.php`, `config/seo.php`, `config/socials.php`, `config/terms.php` and `config/trip-default-items.php`.

**Rate Limiting:**
Custom `frontend-form-actions` limiter: 25 requests/min per IP, registered in `bootstrap/app.php`.
Applied to: trip request forms, contact forms and newsletter subscriptions.

---

## Localization

The public website is Dutch only. The `SetLocale` middleware forces `nl` on every non-admin route, so visitors never switch language.

The admin panel is bilingual. There the middleware picks a locale from the session, falls back to the browser `Accept-Language` header, and then to `config('app.locale')`. The `LocaleSwitcher` component posts to `/locale/switch` to change it.

- Server-side translations live in `resources/lang/nl/` and `resources/lang/en/`, one file per domain.
- Client-side translations use `vue-i18n`, seeded from `resources/lang/nl.json` and `resources/lang/en.json`, and kept in sync with the server locale on every Inertia page load.
- `config/locales.php` maps country codes to full locale strings. This is used for formatting, not for the interface language.

All user-facing strings belong in translation files. Code, comments and docblocks are written in English.

---

## Console Commands and Scheduled Tasks

Commands live in `app/Console/Commands/` and are auto-discovered. The schedule is defined in `routes/console.php` and runs on the server through the Forge scheduler.

| Command | Schedule | Purpose |
|---------|----------|---------|
| `bookings:anonymize` | Yearly | Anonymize bookings whose retention period has expired |
| `bookings:anonymize-special-requests` | Daily | Anonymize special requests after the return date has passed |
| `trip-requests:purge` | Monthly | Delete trip requests without a booking that exceeded retention |
| `newsletter:purge-unsubscribed` | Monthly | Remove unsubscribed newsletter subscribers |

These commands accept `--dry-run` to preview without changing anything, plus `--years` or `--days` to override the retention period from `config/privacy.php`.

Three commands are not scheduled and are run manually:
- `sitemap:generate` - Write the sitemap to a file, for inspection or a manual export. The site itself serves `/sitemap.xml` from a route, so this is not needed in production.
- `newsletter:send-scheduled-campaigns` - Dispatch campaigns that are queued for sending
- `terms:generate-pdf` - Regenerate the terms and conditions PDF

The anonymize and purge commands carry privacy weight. If the scheduler stops running, personal data is retained longer than intended, so treat a broken scheduler as urgent rather than cosmetic.

---

## SEO

- **Meta tags** - Controllers share an `seo` payload per page through `shareSeo()`, with titles and descriptions from `resources/lang/*/seo.php`. Length limits live in `config/seo.php`.
- **Structured data** - Trips and blog posts expose JSON-LD through their models.
- **Robots** - `/privacy` and `/algemene-voorwaarden` are served with `noindex, follow`. All other public pages are indexable. `public/robots.txt` allows everything.
- **Sitemap** - Served from the `/sitemap.xml` route by `SitemapController`, which renders what `SitemapBuilder` produces. It covers the static pages plus every published trip and blog post, using route names so the URLs stay canonical. The rendered XML is cached for the lifetime set in `config/seo.php`, so newly published content appears within that window.
- **Why a route and not a file** - A generated file in `public/` lives inside a single release directory, so it disappears on the next deploy, and it depends on the scheduler to stay current. A route has neither problem. Never leave a `public/sitemap.xml` on the server: the web server serves that file before the request ever reaches Laravel, which silently shadows the route with stale content.

---

## Security

### Authentication & Authorization
- Session authentication for the admin panel, guests are redirected to `/admin/login`
- Sanctum token authentication for the API routes
- CSRF protection on all state-changing requests
- Token-based newsletter confirmation and unsubscribe links

### Input Validation
- Server-side validation via Form Requests and shared rule sets in `app/Services/Validation/`
- Client-side validation via Vue composables and `resources/js/Validators/`
- Phone number validation with country-specific rules
- Email validation with domain checks

### Rate Limiting
- Public form endpoints: 25 requests/minute per IP via the `frontend-form-actions` limiter
- Locale switching and document downloads: 10 requests/minute per IP
- Protects trip request, contact and newsletter endpoints

### Anti-Spam Measures
- **Vue Honeypot** - Invisible form field traps for bot detection
- **AntiSpamEmailService** - Server-side suspicious pattern detection
- **Email/Phone Obfuscation** - Contact links protected via the `useAntiSpamLinks` composable
- **Rate Limiting** - 25 requests/min on all public forms

**How Email/Phone Obfuscation Works:**
```js
// useAntiSpamLinks.js
emailLinks(encodedEmail, selector)   // Decodes on user interaction
phoneLinks(encodedPhone, selector)   // Decodes on user interaction
```

Contact information is:
1. **Encoded** - Hex-encoded and reversed in HTML source
2. **Decoded on interaction** - Only decoded when the user hovers, clicks or touches
3. **Bot-proof** - Prevents email and phone harvesting by scrapers

Example encoding:
- `info@example.com` is stored as a hex-reversed string and decoded on `mouseover/focus/touchstart/click`
- Event listeners are removed after the first decode for performance

### Data Protection
- Environment variables for sensitive credentials, managed in the Forge environment editor
- No secrets committed to the repository
- GitHub Secrets for CI credentials
- Secure password hashing (bcrypt)
- Scheduled anonymization of old bookings and special requests

---

## Deployment

Production runs on **Laravel Forge**. The server has PHP, Composer, a queue worker and the Laravel scheduler available, so nothing needs to be bundled into the repository.

### Pipeline

1. Work happens on feature branches and is merged into `main` through a pull request.
2. CI runs on every pull request and every push to `main`: tests, Pint, PHPStan and the AI review.
3. Merging `main` into the **`production`** branch and pushing triggers a deploy.
4. Forge **Quick Deploy** picks up the push automatically and runs the deploy script on the server.

There is no deployment workflow in GitHub Actions. GitHub Actions only runs checks, Forge does the deploying.

### What the deploy script does

The script is managed in the Forge panel, not in this repository. It performs the usual Forge sequence:

```bash
git pull origin production
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
php artisan migrate --force
npm ci && npm run build
php artisan config:cache
php artisan view:cache
php artisan queue:restart
```

Three things are worth remembering when you change the deploy script:
- `queue:restart` is required after every deploy, otherwise the queue daemon keeps running the old code.
- Re-running `config:cache` is what makes a changed environment variable take effect.
- Do not add `php artisan route:cache` or `php artisan optimize`. `routes/web.php` registers a few routes as closures, and Laravel cannot serialize a closure, so route caching fails the whole deploy.

Deploys use the zero downtime layout, where each release gets its own directory and `current` is a symlink to the active one. Anything written into the release directory at runtime is gone after the next deploy, which is why generated files do not belong in `public/`.

### Server processes

| Process | Managed in | Notes |
|---------|-----------|-------|
| Scheduler | Forge Scheduler | Runs `php artisan schedule:run` every minute, which drives `routes/console.php` |
| Queue worker | Forge Daemon | Runs `php artisan queue:work` on the `database` queue connection, needed for newsletter campaigns and mail listeners |

### Environment

Production environment variables are edited in the Forge panel, not in a committed file. `APP_URL` has to match the live domain exactly, since Ziggy, sitemap URLs and links in outgoing email are all derived from it. Run a deploy or `php artisan config:cache` after changing anything, otherwise the cached config keeps the old value.

---

## Troubleshooting

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
Check the Forge Scheduler for the site. Everything in `routes/console.php` depends on `schedule:run` firing every minute. If the scheduler is down, the sitemap goes stale and the privacy cleanup commands stop running.

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

---

## Contributing

1. Create a feature branch off `main` (`git checkout -b feature/amazing-feature`)
2. Run tests: `php artisan test && npm run test:run`
3. Run code quality checks: `./vendor/bin/pint && ./vendor/bin/phpstan analyse`
4. Commit changes using conventional commits
5. Push your branch and open a pull request against `main`

**Code Standards:**
- PSR-12 coding standard (enforced via Laravel Pint)
- PHPStan level 5 static analysis via Larastan
- Vue 3 Composition API preferred
- Code, comments and docblocks in English
- User-facing strings in translation files, never hardcoded
- Test coverage for new features

---

## License

Copyright © 2025 Jelmer de Jong. All rights reserved.

This repository is public so the code can be read and reviewed. No license is granted to copy, modify, distribute or use it, in whole or in part, without prior written permission.

---

**Built with dedication to sustainable travel and modern web development practices.**
