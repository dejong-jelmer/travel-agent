# Architecture

Backend structure of the application. For the Vue side, see [frontend.md](frontend.md).

## Request Flow

```
HTTP Request
  → FormRequest (validation)
  → DTO (type-safe data)
  → Service (business logic)
  → Model (persistence)
  → ResponseMacro (formatted response)
```

**Example flow:**

```php
CreateBookingRequest
  → CreateBookingData::fromRequest()
  → PriceCalculatorService::forTrip()
  → BookingService::create()
  → Booking::create()
  → response()->booking($booking, ModelAction::Created)
```

## Data Transfer Objects (DTOs)

Located in `app/DTO/`:

- `CreateBookingData` / `UpdateBookingData` - Main booking operations
- `BookingContactData` / `BookingTravelerData` / `BookingCostItemData` - Nested structures
- `TripPriceData` - Price calculation results
- `ContactFormData` / `ContactDetails` - Contact form and contact information
- `DataTableConfig` - Admin datatable configuration
- Uses the `ArrayableDTO` trait for serialization
- Uses the `BookingDataParser` trait for parsing validated data

## Service Layer

Located in `app/Services/`:

- `BookingService` - Create and update bookings with travelers
- `TripRequestService` - Handle incoming trip requests
- `PriceCalculatorService` - Calculate trip prices per date and traveler count
- `FeesAndFundsService` - Statutory fees and guarantee fund amounts
- `ContactDetailsService` - Format contact information
- `PhoneNumberService` - Phone validation and formatting
- `AntiSpamEmailService` - Obfuscated rendering of the contact email address
- `CountryService` / `DestinationService` - Geographic data
- `NewsletterCampaignService` - Newsletter campaign management
- `TripItemService` - Trip item management
- `SlugService` - Slug generation for trips and blog posts
- `ImageVariantService` - Responsive WebP variants of uploaded images
- `OgImageService` - Open Graph images and derivatives of hero images
- `SitemapBuilder` - Builds the sitemap served by `SitemapController`
- `TermsPdfService` / `SustainabilityPdfService` - PDF document generation
- `DataTableService` - Admin datatable queries
- `SystemHealthService` - Database, email and queue status for the admin dashboard
- `Services/Validation/` - Shared validation rule sets per domain

## Event-Driven Email System

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

## Response Macros

Custom response macro in `AppServiceProvider`:

```php
Response::macro('booking', function (Booking $booking, ModelAction $action) {
    return BookingResponse::make($booking)->toResponse($action);
});

// Usage
return response()->booking($booking, ModelAction::Created);
```

## Inertia Shared Data

Configured in `AppServiceProvider`:

- Flash messages (`success`, `error`)
- `adminStats` - new bookings and new trip requests count (only on admin routes, only when authenticated)
- `settings` - all `Setting` model key-value pairs
- `breadcrumbs` - auto-generated via `Breadcrumbs::generate()`

## Routing

`routes/web.php` holds the public site and the `admin/` group behind the `auth` and `admin` middleware. Public pages are Dutch-only URLs: `/`, `/over-mij`, `/reizen`, `/reizen/{slug}`, `/mijn-verhalen`, `/mijn-verhalen/{slug}`, `/garantie`, `/over-vvkr`, `/privacy` and `/algemene-voorwaarden`.

`routes/api.php` exposes a small Sanctum-protected surface (`/login`, `/logout`, `/user`). `routes/api_testing.php` only registers routes in the `local` and `testing` environments. `/up` is the health check endpoint.

Public form endpoints share the `frontend-form-actions` rate limiter, registered in `bootstrap/app.php`.

## Configuration

Application config lives in dedicated files per domain: `config/admin.php`, `config/blog.php`, `config/booking.php`, `config/contact.php`, `config/health.php`, `config/images.php`, `config/locales.php`, `config/newsletter.php`, `config/privacy.php`, `config/seo.php`, `config/socials.php`, `config/terms.php` and `config/trip-default-items.php`.
