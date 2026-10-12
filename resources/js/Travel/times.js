// The admin enters times of day; these place them on a date. Moments are 'Y-m-d\TH:i' wall-clock strings without a
// time zone, like the props of the plan, so they are handled as strings and never shifted to the browser's zone.

const nextDay = (date) => {
    const day = new Date(`${date}T00:00:00Z`);
    day.setUTCDate(day.getUTCDate() + 1);

    return day.toISOString().slice(0, 10);
};

// The first moment at this time of day that is not before `after`, a date or a moment. A night train that departs
// at 23:10 arrives at 07:05 the next day.
export const placeTime = (after, time) => {
    const date = after.slice(0, 10);
    const clock = after.slice(11, 16);

    return `${clock && time < clock ? nextDay(date) : date}T${time}`;
};

// The departure follows on `after`, the arrival on the departure. Without `after` there is nothing to place the times
// on: the journey has no date, or the leg is part of an arrival route, which has no clock.
export const placeTimes = (after, departureTime, arrivalTime) => {
    if (!after) {
        return { departureAt: null, arrivalAt: null };
    }

    const departureAt = departureTime ? placeTime(after, departureTime) : null;
    const arrivalAt = arrivalTime ? placeTime(departureAt ?? after, arrivalTime) : null;

    return { departureAt, arrivalAt };
};

export const minutesBetween = (from, to) => (Date.parse(`${to}:00Z`) - Date.parse(`${from}:00Z`)) / 60000;

// The moment a new or edited leg follows on: the last time of the legs before it, otherwise the date of the journey.
export const momentBefore = (legs, index, date) => {
    for (let i = index - 1; i >= 0; i--) {
        const moment = legs[i].arrival_at ?? legs[i].departure_at;

        if (moment) {
            return moment;
        }
    }

    return date;
};
