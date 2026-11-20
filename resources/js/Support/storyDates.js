/**
 * The date a story was last updated, ready for display, or null when it should not be shown: when the story was not
 * changed after it was published, or only on the day it was published, the publication date already says it.
 *
 * Both dates go through the same formatter (the one of the publication date on the page), so they are compared as
 * the reader sees them, in the reader's time zone.
 *
 * @param {string|null} publishedAt ISO date of the publication
 * @param {string|null} updatedAt ISO date of the last save
 * @param {(date: string) => string|null} format Formats a date for display
 * @returns {string|null}
 */
export function lastUpdatedDate(publishedAt, updatedAt, format) {
    if (!publishedAt || !updatedAt) return null;
    if (new Date(updatedAt) <= new Date(publishedAt)) return null;

    const updated = format(updatedAt);

    return updated && updated !== format(publishedAt) ? updated : null;
}
