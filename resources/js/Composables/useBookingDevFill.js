// Composables/useBookingDevFill.js
// DEV-only: pre-fills the booking form with dummy data for local testing.
// Has no effect in production (import.meta.env.DEV guard).
import { nextTick } from 'vue';

const DUMMY_DATA = {
    departure_date: new Date(Date.now() + 45 * 24 * 60 * 60 * 1000),
    participants: { adults: 2, children: 1 },
    contact: {
        street: 'Teststraat',
        house_number: 42,
        addition: '',
        postal_code: '1234 AB',
        city: 'Amsterdam',
        email: 'test@test.nl',
        phone: '+31612345678',
    },
    travelers: {
        adults: [
            { first_name: 'Jan', last_name: 'Jansen', birthdate: '01-01-1985', nationality: 'Nederlands' },
            { first_name: 'Marie', last_name: 'Jansen', birthdate: '15-06-1988', nationality: 'Nederlands' },
        ],
        children: [
            { first_name: 'Pietje', last_name: 'Jansen', birthdate: '20-03-2015', nationality: 'Nederlands' },
        ],
    },
};

export function fillBookingWithDummyData(booking) {
    if (!import.meta.env.DEV) return;

    nextTick(() => {
        booking.departure_date = DUMMY_DATA.departure_date;
        booking.participants.adults = DUMMY_DATA.participants.adults;
        booking.participants.children = DUMMY_DATA.participants.children;
        Object.assign(booking.contact, DUMMY_DATA.contact);

        // Travelers are synced by a watcher in useBooking — wait for that tick first
        nextTick(() => {
            DUMMY_DATA.travelers.adults.forEach((data, i) => {
                if (booking.travelers.adults[i]) Object.assign(booking.travelers.adults[i], data);
            });
            DUMMY_DATA.travelers.children.forEach((data, i) => {
                if (booking.travelers.children[i]) Object.assign(booking.travelers.children[i], data);
            });
        });
    });
}
