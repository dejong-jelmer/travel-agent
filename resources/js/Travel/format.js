// Dates arrive as 'Y-m-d' and times as 'Y-m-d\TH:i', without a time zone: they are wall-clock times at the place
// itself. They are read from the string, as a Date would shift them to the time zone of the browser.

const toDate = (value) => {
    const [year, month, day] = value.slice(0, 10).split('-').map(Number);

    return new Date(year, month - 1, day);
};

// 'dinsdag 11 augustus'
export const formatDate = (value) =>
    value ? toDate(value).toLocaleDateString('nl-NL', { weekday: 'long', day: 'numeric', month: 'long' }) : '';

// 'di 11 aug'
export const formatShortDate = (value) =>
    value
        ? toDate(value)
              .toLocaleDateString('nl-NL', { weekday: 'short', day: 'numeric', month: 'short' })
              .replaceAll('.', '')
        : '';

// '12:47'
export const formatTime = (value) => (value ? value.slice(11, 16) : '');

// '54 min', '2 u', '2 u 16 min'
export const formatDuration = (minutes) => {
    if (minutes === null || minutes === undefined) {
        return '';
    }

    const hours = Math.floor(minutes / 60);
    const rest = minutes % 60;

    if (!hours) {
        return `${rest} min`;
    }

    return rest ? `${hours} u ${rest} min` : `${hours} u`;
};
