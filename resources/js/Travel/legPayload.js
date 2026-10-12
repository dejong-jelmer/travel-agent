import { formatTime } from '@/Travel/format.js';
import { placeTimes } from '@/Travel/times.js';

// Only a train has a number, carriage and seat on the ticket.
export const hasTicket = (type) => type === 'train';

// The form values of a leg: times of day instead of moments, and empty strings instead of null for the inputs.
// A new leg starts from the defaults, such as the station the leg before it arrived at.
export const legValues = (leg, defaults = {}) => ({
    type: leg?.type ?? defaults.type ?? 'train',
    from_station_id: leg ? (leg.from_station?.id ?? null) : (defaults.from_station_id ?? null),
    to_station_id: leg?.to_station?.id ?? null,
    departure_time: formatTime(leg?.departure_at),
    arrival_time: formatTime(leg?.arrival_at),
    duration_minutes: leg?.duration_minutes ?? '',
    number: leg?.number ?? '',
    carriage: leg?.carriage ?? '',
    seat: leg?.seat ?? '',
    note: leg?.note ?? '',
    transit_lines: (leg?.transit_lines ?? []).map((line) => ({ id: line.id, direction: line.direction ?? '' })),
});

// What the leg request expects. The fields the form hides for this type are cleared, so they cannot break an
// invariant unseen: a transfer stays at its one station, only a leg with a transit mode keeps its lines, and with both
// times set the duration follows from them.
export const legPayload = (values, { after, hasLines }) => {
    const { departureAt, arrivalAt } = placeTimes(after, values.departure_time, values.arrival_time);
    const ticket = hasTicket(values.type);

    return {
        type: values.type,
        from_station_id: values.from_station_id,
        to_station_id: values.type === 'transfer' ? values.from_station_id : values.to_station_id,
        departure_at: departureAt,
        arrival_at: arrivalAt,
        duration_minutes: (departureAt && arrivalAt) || values.duration_minutes === '' ? null : Number(values.duration_minutes),
        number: (ticket && values.number) || null,
        carriage: (ticket && values.carriage) || null,
        seat: (ticket && values.seat) || null,
        note: values.note || null,
        transit_lines: hasLines ? values.transit_lines.map((line) => ({ id: line.id, direction: line.direction || null })) : [],
    };
};
