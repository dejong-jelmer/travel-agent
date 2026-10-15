// Whether any of the values contains the search, ignoring case. An empty search matches everything.
export const matches = (query, ...values) => {
    const search = query.trim().toLowerCase();

    return !search || values.some((value) => value?.toLowerCase().includes(search));
};

// A pair of coordinates as a map copies them, '44.8249878, -0.5602157', split into latitude and longitude. Both need a
// decimal point, so a single value with a Dutch decimal comma, '44,82', stays one value.
export const coordinatePair = (value) => {
    const pair = String(value ?? '').match(/^\s*(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)\s*$/);

    return pair ? { latitude: pair[1], longitude: pair[2] } : null;
};
