# Omdat We Reizen: Sustainable Travel Agent

---

[![run-tests](https://github.com/dejong-jelmer/travel-agent/actions/workflows/run-tests.yml/badge.svg)](https://github.com/dejong-jelmer/travel-agent/actions/workflows/run-tests.yml)
[![GitHub Code Style Action Status](https://github.com/dejong-jelmer/travel-agent/actions/workflows/lint.yml/badge.svg)](https://github.com/dejong-jelmer/travel-agent/actions/workflows/lint.yml)

A Laravel 12 + Inertia.js + Vue 3 application promoting sustainable European train travel. It combines a public Dutch website with a bilingual admin panel, and runs in production on Laravel Forge.

> **Mission**: Making slow travel the standard for European trips through curated, culturally rich train-based journeys with minimal carbon footprint.

**Live site:** https://omdatwereizen.nl · **Built by:** Jelmer de Jong, solo, from first commit (February 2025) to production

<p align="center">
  <img src="docs/screenshots/public_home.jpg" alt="Homepage" width="49%">
  <img src="docs/screenshots/public_trip_show.jpg" alt="Trip show" width="49%">
</p>
<p align="center">
  <img src="docs/screenshots/admin_dashboard.png" alt="Admin Dashboard" width="49%">
  <img src="docs/screenshots/admin_trips_edit.png" alt="Admin trips edit" width="49%">
</p>
<p align="center">
  <img src="docs/screenshots/admin_trips_edit_2.jpg" alt="Admin trips edit 2" width="49%">
  <img src="docs/screenshots/admin_bookings_create.png" alt="Admin booking create" width="49%">
</p>

---

## Project Highlights

This is a real business application in daily use, not a demo. It covers the full path from content management to customer requests, bookings and transactional email. I designed, built, tested and deployed it on my own. These are the parts most worth a reviewer's time:

- **A shared price specification for PHP and JavaScript.** The booking price is calculated on the server and live in the browser. The two implementations cannot share code, so they share [test vectors](tests/fixtures/booking-price-vectors.json) instead. If the formula changes on one side only, the other side's test fails. Amounts are handled as integer cents with `moneyphp/money`.
- **A layered backend.** Form Request → DTO → Service → Model, with response macros and events driving the email flows. Controllers stay thin and the business rules can be unit tested in isolation.
- **Privacy by design (GDPR).** Scheduled commands anonymize bookings and special requests and purge stale trip requests and unsubscribed subscribers, each with a `--dry-run` mode. Retention periods are configured in one place.
- **Performance and SEO work.** Responsive WebP variants served through `srcset`, generated Open Graph images, JSON-LD structured data and a sitemap served from a route so it cannot go stale between deploys.
- **Spam and abuse protection without CAPTCHAs.** Rate limiting, honeypot fields and obfuscated contact details.
- **Quality gates on every pull request.** Over 200 PHPUnit tests against MySQL, Vitest for the frontend, Pint, PHPStan (Larastan level 5) and an automated AI review. Deploys to production go through a separate `production` branch and Laravel Forge.

---

## Table of Contents

- [Project Highlights](#project-highlights)
- [Tech Stack](#tech-stack)
- [Features](#features)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Testing](#testing)
- [Architecture](#architecture)
- [Security](#security)
- [Development Workflow](#development-workflow)
- [Further Documentation](#further-documentation)
- [License](#license)

---

## Tech Stack

| Layer                  | Technology     | Version |
| ---------------------- | -------------- | ------- |
| **Backend**            | Laravel        | 12      |
| **Language**           | PHP            | 8.4     |
| **Database**           | MySQL          | 8.0+    |
| **Frontend Framework** | Vue.js         | 3.5     |
| **SPA Bridge**         | Inertia.js     | 2.0     |
| **Styling**            | Tailwind CSS   | 3.4     |
| **Build Tool**         | Vite           | 6       |
| **PHP Testing**        | PHPUnit        | 11      |
| **JS Testing**         | Vitest         | 4       |
| **Email Service**      | Mailjet API    | 3.1     |
| **CI**                 | GitHub Actions | -       |
| **Hosting**            | Laravel Forge  | -       |

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

- **Dashboard** - Counters for new bookings and new trip requests, plus a system status overview
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
- Rate limiting on public forms
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
- **Node.js** 20+ (with npm)
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
git clone https://github.com/dejong-jelmer/travel-agent.git
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

### 5. Start the Development Environment

```bash
composer dev
```

This runs concurrently:

- `php artisan serve` - Laravel development server
- `php artisan queue:listen --tries=1` - Queue worker
- `npm run dev` - Vite HMR server

For a production build of the assets, run `npm run build`.

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
- **CI**: `run-tests.yml` runs on pull requests to `main` and pushes to `main`, with a MySQL 8 service container, `.env.ci` as environment, a Vite build to verify assets compile, and both test suites.
- **Lint CI**: `lint.yml` runs Pint in `--test` mode and PHPStan on the same triggers.
- **AI review**: `ai-code-review.yml` posts an automated review on pull requests, except on the `production` branch.

---

## Architecture

Requests follow a fixed path through the backend:

```
HTTP Request
  → FormRequest (validation)
  → DTO (type-safe data)
  → Service (business logic)
  → Model (persistence)
  → ResponseMacro (formatted response)
```

- **Backend**: Form Requests validate input, DTOs in `app/DTO/` carry it, and services in `app/Services/` hold the business rules. Transactional email is driven by events and queued listeners.
- **Frontend**: Inertia pages in `resources/js/Pages/`, built from components organized by atomic design (atoms, molecules, organisms). Shared state and logic live in composables.
- **Routing**: The public site uses Dutch URLs. The admin panel lives under `admin/` behind authentication and an admin role check.

See [docs/architecture.md](docs/architecture.md) for the backend in detail and [docs/frontend.md](docs/frontend.md) for components, composables and the Tailwind theme.

---

## Security

- **Authentication**: Session authentication for the admin panel, restricted to admin users. Sanctum token authentication for the API routes.
- **CSRF protection** on all state-changing requests.
- **Validation** on both server and client, with shared rule sets per domain and country-specific phone validation.
- **Rate limiting** on public forms, login, locale switching and document downloads.
- **Spam protection**: Honeypot fields on public forms and obfuscated email addresses and phone numbers.
- **Token-based links** for newsletter confirmation and unsubscribe.
- **Secrets** live in environment variables, managed in Forge and GitHub Secrets, never in the repository.
- **Data retention**: Scheduled anonymization of old bookings and special requests.

---

## Development Workflow

I build and maintain this project on my own. Every change follows the same routine:

1. I start a feature branch off `main`, for example `feature/trip-filters` or `fix/image-sizes`.
2. Before committing I run the full set of checks locally:
    ```bash
    php artisan test && npm run test:run
    ./vendor/bin/pint && ./vendor/bin/phpstan analyse
    ```
3. I write commit messages in the conventional commits style (`feat:`, `fix:`, `refactor:`, ...).
4. I push the branch and open a pull request against `main`. CI runs the tests, Pint and PHPStan, and an AI review comments on the diff.
5. After merging, a release is a pull request from `main` to `production`, which Forge deploys. See [docs/deployment.md](docs/deployment.md).

**Code standards:**

- PSR-12 coding standard (enforced via Laravel Pint)
- PHPStan level 5 static analysis via Larastan
- Vue 3 Composition API preferred
- Code, comments and docblocks in English
- User-facing strings in translation files, never hardcoded
- Tests for new features

---

## Further Documentation

- [Architecture](docs/architecture.md) - DTOs, services, events, response macros, routing and configuration
- [Frontend](docs/frontend.md) - Component structure, composables, breakpoints, fonts and color palette
- [Deployment](docs/deployment.md) - Release pipeline, Forge deploy script, server processes, console commands and scheduled tasks
- [SEO](docs/seo.md) - Meta tags, structured data, Open Graph images and the sitemap
- [Localization](docs/localization.md) - Dutch public site and bilingual admin panel
- [Troubleshooting](docs/troubleshooting.md) - Common local, CI and production issues

---

## License

Copyright © 2025 Jelmer de Jong. All rights reserved.

This repository is public so the code can be read and reviewed. No license is granted to copy, modify, distribute or use it, in whole or in part, without prior written permission.
