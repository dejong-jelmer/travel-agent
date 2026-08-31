const DEFAULT_LOCALE = "nl-NL";

/**
 * Format an amount in cents as a euro amount.
 *
 * Defaults to Dutch formatting, like useDateFormatter does for dates; pass a
 * locale to follow the admin's language instead.
 */
export function formatCents(cents, locale = DEFAULT_LOCALE) {
    return new Intl.NumberFormat(locale, {
        style: "currency",
        currency: "EUR",
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format((cents ?? 0) / 100);
}
