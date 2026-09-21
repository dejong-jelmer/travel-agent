# Localization

The public website is Dutch only. The `SetLocale` middleware forces `nl` on every non-admin route, so visitors never switch language.

The admin panel is bilingual. There the middleware picks a locale from the session, falls back to the browser `Accept-Language` header, and then to `config('app.locale')`. The `LocaleSwitcher` component posts to `/locale/switch` to change it.

- Server-side translations live in `resources/lang/nl/` and `resources/lang/en/`, one file per domain.
- Client-side translations use `vue-i18n`, seeded from `resources/lang/nl.json` and `resources/lang/en.json`, and kept in sync with the server locale on every Inertia page load.
- `config/locales.php` maps country codes to full locale strings. This is used for formatting, not for the interface language.

All user-facing strings belong in translation files. Code, comments and docblocks are written in English.
