// Display helpers for the departure pages of the admin.

/**
 * Format a 'yyyy-mm-dd' date or an ISO timestamp as a short date, like "20 jun 2027".
 */
export function formatDate(value, locale = 'nl') {
    if (!value) return '';

    // A date without a time is a calendar day: parse it as local time, or it shifts a day west of UTC
    const date = value.length === 10 ? new Date(`${value}T00:00:00`) : new Date(value);

    return new Intl.DateTimeFormat(locale, { day: 'numeric', month: 'short', year: 'numeric' }).format(date);
}

/**
 * The parts of the occupancy bar as percentages of the maximum: the confirmed participants, the places that are
 * held but not confirmed, and where the minimum lies.
 */
export function progressParts({ occupied, confirmed, min, max }) {
    const share = (count) => (max > 0 ? Math.min(100, Math.max(0, (count / max) * 100)) : 0);
    const held = Math.max(0, occupied - confirmed);

    return {
        held,
        confirmedWidth: share(confirmed),
        heldWidth: Math.min(share(held), 100 - share(confirmed)),
        minimumAt: share(min),
    };
}
