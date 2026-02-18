# Sustainable Travel Agent
---
[![run-tests](https://github.com/dejong-jelmer/travel-agent/actions/workflows/run-tests.yml/badge.svg)](https://github.com/dejong-jelmer/travel-agent/actions/workflows/run-tests.yml)
[![GitHub Code Style Action Status](https://github.com/dejong-jelmer/travel-agent/actions/workflows/lint.yml/badge.svg)](https://github.com/dejong-jelmer/travel-agent/actions/workflows/lint.yml)

A Laravel 12 + Inertia.js + Vue 3 application promoting sustainable European train travel. Built with modern development practices, comprehensive testing, and automated CI/CD deployment.

> **Mission**: Making slow travel the standard for European trips through curated, culturally rich train-based journeys with minimal carbon footprint.

---

## Table of Contents

- [Tech Stack](#tech-stack)
- [Features](#features)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Development](#development)
- [Testing](#testing)
- [Architecture](#architecture)
- [Security](#security)
- [Deployment](#deployment)
- [License](#license)

---

## Tech Stack

| Layer | Technology | Version |
|-------|------------|---------|
| **Backend** | Laravel | 12 |
| **Language** | PHP | 8.4 |
| **Frontend Framework** | Vue.js | 3 |
| **SPA Bridge** | Inertia.js | Latest |
| **Styling** | Tailwind CSS | 3.4 |
| **Build Tool** | Vite | Latest |
| **Testing** | PHPUnit / Vitest | Latest |
| **Email Service** | Mailjet API | Latest |
| **CI/CD** | GitHub Actions | - |

**Key Dependencies:**
- `propaganistas/laravel-phone` - Phone number validation
- `@vuepic/vue-datepicker` - Date picker component
- `@headlessui/vue` - Accessible UI components
- `laravel-vite-plugin` - Vite integration
- `happy-dom` - DOM environment for testing

---

## Features

### Core Functionality
- **Travel Package Browsing** - Curated European train journeys with detailed itineraries
- **Multi-step Booking System** - Dynamic form with real-time validation
- **Newsletter Management** - Token-based subscriptions with 48h expiry links
- **Email Notifications** - Event-driven transactional emails via Mailjet
- **Responsive Design** - Mobile-first UI with custom breakpoints

### Technical Features
- Server-side and client-side validation with DTOs
- Event-driven architecture for async operations
- Rate limiting (25 requests/min on public forms)
- Honeypot spam protection
- CSRF protection on all forms
- Signed URLs for sensitive actions

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
- PHPStan (static analysis, level 5)
- Laravel IDE Helper

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

Configure your `.env` file with:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

MAILJET_APIKEY=your_api_key
MAILJET_APISECRET=your_api_secret
```

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
./vendor/bin/phpstan analyse   # Static analysis (PHPStan level 5)

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
# Run all tests
npx vitest run

# Watch mode
npx vitest

# With coverage
npx vitest run --coverage

# UI mode
npx vitest --ui
```

### Test Configuration
- **PHP**: Uses in-memory SQLite database for fast tests
- **JavaScript**: Uses `happy-dom` environment with `vmThreads` pool
- **CI**: Automated tests run on pull requests and pushes to `main`

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
  → StoreBookingData::fromRequest()
  → BookingService::store()
  → Booking::create()
  → response()->booking($booking, ModelAction::Created)
```

### Key Architectural Patterns

#### Data Transfer Objects (DTOs)
Located in `app/DTO/`:
- `StoreBookingData` / `UpdateBookingData` - Main booking operations
- `BookingContactData` / `BookingTravelerData` - Nested structures
- Uses `ArrayableDTO` trait for serialization
- Uses `BookingDataParser` trait for parsing validated data

#### Service Layer
Located in `app/Services/`:
- `BookingService` - Create/update bookings with travelers
- `ContactDetailsService` - Format contact information
- `PhoneNumberService` - Phone validation/formatting
- `AntiSpamEmailService` - Spam detection logic

#### Event-Driven Email System
```
BookingCreated Event
  → SendBookingConfirmationEmail Listener
  → NotifyAdminOfNewBooking Listener

NewsletterSubscriptionRequested Event
  → SendNewsletterConfirmationEmail Listener
```

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
├── Pages/              # Inertia page components
├── Templates/          # Page layouts
└── Composables/        # Reusable Vue composition functions
    ├── useBookingValidation.js  # Multi-step form validation
    └── useAntiSpamLinks.js      # Email/phone obfuscation
```

### Inertia.js Integration

**Global Shared Data** (configured in `AppServiceProvider`):
- Flash messages (`success`, `error`)
- Auto-generated breadcrumbs via `Breadcrumbs::generate()`

**Auto-registered Components:**
All components in `Components/`, `Templates/`, and `Icons/` are globally available.

### Custom Configuration

**Tailwind Breakpoints** (`resources/js/screens.js`):
```js
{ phone: '0px', tablet: '600px', laptop: '900px', desktop: '1200px', wide: '1800px' }
```

**Custom Color Palette** (see `tailwind.config.js`):

*Semantic color system for sustainable travel aesthetic:*

- **Brand Identity**
  - `brand.primary` (#2f3e46) - Primary dark blue-gray for text and headings
  - `brand.secondary` (#f0f4f7) - Off-white/cream for backgrounds
  - `brand.tertiary` (#ccf6ff) - Light cyan accent
  - `brand.light` (#a3bccb) - Soft blue for subtle text and borders

- **Accent Colors** (Nature/Sustainability focus)
  - `accent.primary` (#f0972d) - Warm orange for primary CTAs and highlights
  - `accent.sage` (#afcb98) - Sage green for success and eco-friendly elements
  - `accent.earth` (#dcc7aa) - Earth/sand tone for warmth
  - `accent.terracotta` (#b17c65) - Terracotta for contrast and emphasis
  - `accent.link` (#82b2ca) - Soft blue for links

- **Status Feedback**
  - `status.error` (#dc3545) - Error states and validation failures
  - `status.success` (#198754) - Success states and confirmations
  - `status.warning` (#ffc107) - Warning states and alerts
  - `status.info` (#0d6efd) - Informatieve states

**Rate Limiting:**
Custom `frontend-form-actions` limiter: 25 requests/min per IP
Applied to: booking forms, contact forms, newsletter subscriptions

---

## Security

### Authentication & Authorization
- CSRF protection on all state-changing requests
- Signed URLs for newsletter actions (48h expiry)
- Token-based newsletter confirmation system

### Input Validation
- Server-side validation via Form Requests
- Client-side validation via Vue composables
- Phone number validation with country-specific rules
- Email validation with domain checks

### Rate Limiting
- Public form endpoints: 25 requests/minute per IP
- Configured via `frontend-form-actions` rate limiter
- Protects booking, contact, and newsletter endpoints

### Anti-Spam Measures
- **Vue Honeypot** - Invisible form field traps for bot detection
- **AntiSpamEmailService** - Server-side suspicious pattern detection
- **Email/Phone Obfuscation** - Contact links protected via `useAntiSpamLinks` composable
- **Rate Limiting** - 25 requests/min on all public forms

**How Email/Phone Obfuscation Works:**
```js
// useAntiSpamLinks.js
emailLinks(encodedEmail, selector)   // Decodes on user interaction
phoneLinks(encodedPhone, selector)   // Decodes on user interaction
```

Contact information is:
1. **Encoded** - Hex-encoded and reversed in HTML source
2. **Decoded on interaction** - Only decoded when user hovers/clicks/touches
3. **Bot-proof** - Prevents email/phone harvesting by scrapers

Example encoding:
- `info@example.com` → stored as hex-reversed string → decoded on `mouseover/focus/touchstart/click`
- Removes event listeners after first decode for performance

### Data Protection
- Environment variables for sensitive credentials
- No secrets committed to repository
- GitHub Secrets for CI/CD credentials
- Secure password hashing (bcrypt)

---

## Deployment

### CI/CD Pipeline (GitHub Actions)

**Automated Testing** (`.github/workflows/run-tests.yml`)
- Runs on pull requests and pushes to `main`
- PHP tests with MySQL service container
- JavaScript/Vue tests with Vitest
- Automated builds to verify assets compile

**Automated Deployment** (`.github/workflows/deploy.yml`)
- Triggers on push to `production` branch
- Builds assets with `npm run build`
- Deploys via SFTP (all dependencies bundled)
- Zero-downtime deployment strategy

**Important Notes:**
- Production server has no Composer/NPM installed
- All dependencies must be bundled in deployment
- Use `composer install --no-dev --optimize-autoloader` for production

### Manual Deployment
```bash
# Build production assets
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Deploy via SFTP/rsync
# (ensure vendor/ and node_modules/ are excluded, use built assets)
```

---

## Troubleshooting

### Common Issues

**Vite HMR not working:**
```bash
# Clear Vite cache
rm -rf node_modules/.vite

# Rebuild node_modules
npm install
```

**Tests failing in CI:**
```bash
# Ensure .env.ci exists with correct test database credentials
# Check that migrations run successfully
php artisan migrate --env=testing --force
```

**Queue jobs not processing:**
```bash
# Start queue worker
php artisan queue:work

# Or use supervisor in production
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

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Run tests: `php artisan test && npx vitest run`
4. Run code quality checks: `./vendor/bin/pint && ./vendor/bin/phpstan analyse`
5. Commit changes using conventional commits
6. Push to your branch and open a Pull Request

**Code Standards:**
- PSR-12 coding standard (enforced via Laravel Pint)
- PHPStan level 5 static analysis
- Vue 3 Composition API preferred
- Comprehensive test coverage for new features

---

## License

This project is licensed under the MIT License. See [LICENSE](https://mit-license.org/) for details.

---

**Built with dedication to sustainable travel and modern web development practices.**
