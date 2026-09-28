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

All components in `Components/`, `Templates/` and `Icons/` are registered globally in `app.js`, so pages use them without imports.

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
