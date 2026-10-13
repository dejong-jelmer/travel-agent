# Frontend

Vue 3 with Inertia.js and Tailwind CSS. For the backend, see [architecture.md](architecture.md).

## Component Architecture (Atomic Design)

```
resources/js/
├── Components/
│   ├── Atoms/          # Button, Label, ResponsiveImage
│   ├── Molecules/      # Input, Select, DatePicker, Newsletter
│   └── Organisms/      # Nav, Footer, TripItinerary
│       ├── BookingSteps/   # Steps of the multi-step booking form
│       └── Forms/          # BookingForm, TripForm, TripRequestForm, ...
├── Icons/              # Icon components
├── Pages/              # Inertia page components (public and Admin/)
├── Templates/          # Page layouts
├── Validators/         # Client-side validation rules
├── Support/            # Frontend helpers
└── Composables/        # Reusable Vue composition functions
    ├── useBooking.js            # Booking form state
    ├── useBookingDevFill.js     # Dummy form data, local development only
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

All components in `Components/`, `Templates/` and `Icons/` are registered globally in `setupApp.js`, so pages use them without imports.

## Server-Side Rendering

The first visit of a public page is rendered to HTML on a Node server (`resources/js/ssr.js`), so visitors and search engines get the content before any JavaScript runs. The browser then hydrates that HTML (`resources/js/app.js`): Vue takes over the existing markup instead of building it again. Both entry points build the app through `setupApp.js`, so the plugins and global components are always the same on either side. Admin pages are not rendered on the server (`HandleInertiaRequests::ssrEnabled`), and neither is anything when the SSR server is down: those pages render in the browser as before.

```bash
npm run build:ssr                # Builds the browser bundle and the SSR bundle (bootstrap/ssr/ssr.js)
php artisan inertia:start-ssr    # Starts the SSR server on port 13714
```

With `npm run dev` there is no need for the SSR server. Leave it off, or it keeps serving the bundle of the last `build:ssr`.

Hydration only works when the server and the browser produce exactly the same HTML. Code that runs during rendering therefore follows a few rules:

- **No browser APIs during setup.** `window`, `document`, `localStorage` and `matchMedia` do not exist on the server. Use them in `onMounted` or in event handlers, which only run in the browser. Module level code runs on the server too, so the same goes there.
- **DOM side effects in a `watchEffect` get `{ flush: 'post' }`.** A plain `watchEffect` runs once on the server as well; with `flush: 'post'` it only runs after mounting (see `document.title` in `Layout.vue`).
- **No markup that depends on the screen.** The server does not know the viewport, so use Tailwind breakpoints (`hidden tablet:flex`) instead of `vue3-mq` to decide what to render (see the trips on `Home.vue`).
- **The url comes from Inertia.** Use `usePage().url` rather than `window.location` (see `useContactForm.js`).
- **`route()` works on both sides.** The browser gets the route list from `@routes`, the SSR server from the `ziggy` prop. Use the global `route()` rather than importing it from `ziggy-js`.
- **Module level state is shared between visitors.** One Node process renders the pages of every visitor, so a module level variable that changes per request leaks into other requests. The shared `i18n` instance is safe only because the public site always renders in Dutch.

To spot a mismatch, run `npm run build:ssr` with `php artisan inertia:start-ssr` and open the page: the browser console reports `Hydration completed but contains mismatches`.

## Theme

**Breakpoints** (`resources/js/screens.js`, used by Tailwind and `vue3-mq`):

```js
{ phone: '0px', tablet: '600px', laptop: '900px', desktop: '1350px', wide: '1600px' }
```

**Fonts** (see `tailwind.config.js`):

- `font-poppins` - Primary UI font
- `font-caveat` - Handwritten accent font

**Color palette** (see `tailwind.config.js`):

- **Brand identity**
    - `brand.primary` (#2d5f6e) - Main brand color (teal)
    - `brand.accent` (#f59e0b) - Accent/highlight color (amber)
    - `brand.secondary` (#f5f0e8) - Light background
    - `brand.text` (#1e2d3d) - Primary text
    - `brand.light` (#4d6f80) - Light brand tint
    - `brand.subtle` (#afcb98) - Subtle green
    - `brand.earth` (#dcc7aa) - Warm earth tone
    - `brand.link` (#82b2ca) - Link color

- **Status feedback**
    - `status.error` (#dc3545) - Error states and validation failures
    - `status.success` (#198754) - Success states and confirmations
    - `status.warning` (#ffc107) - Warning states and alerts
    - `status.info` (#0d6efd) - Informational states
