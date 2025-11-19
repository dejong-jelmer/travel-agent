// Composables/useBooking.js
import { watch, reactive, readonly, watchEffect } from "vue";
import { useForm } from "@inertiajs/vue3";

// Constants buiten state
export const BOOKING_CONSTRAINTS = readonly({
    maxFutureYears: 1,

    get maxDate() {
        const date = new Date();
        date.setFullYear(date.getFullYear() + this.maxFutureYears);
        return date;
    },
});

const createTraveler = (data = {}) => {
    return reactive({
        ...(data.id && {id: data.id}),
        first_name: data.first_name || "",
        last_name: data.last_name || "",
        birthdate: data.birthdate_formatted || null,
        nationality: data.nationality || "",
        get full_name() {
            return `${this.first_name} ${this.last_name}`.trim();
        },
    });
};

export function useBooking(trip, db_booking, main_booker_index = 0) {
    if (!trip) {
        console.warn("useBooking: trip parameter is required");
    }

    // Sanitize DB data
    const adults =
        db_booking?.travelers
            ?.filter((tr) => tr.type === "adult")
            ?.map(createTraveler) ?? [];

    const children =
        db_booking?.travelers
            ?.filter((tr) => tr.type === "child")
            ?.map(createTraveler) ?? [];

    const booking = useForm({
        trip: trip || null,
        status: db_booking?.status || null,
        payment_status: db_booking?.payment_status || null,
        participants: {
            adults: adults.length || 2,
            children: children.length || 0,
        },
        departure_date: db_booking?.departure_date
            ? new Date(db_booking.departure_date)
            : null,
        travelers: {
            adults,
            children,
        },
        contact: {
            street: db_booking?.contact?.street || "",
            house_number: db_booking?.contact?.house_number || null,
            addition: db_booking?.contact?.addition || "",
            postal_code: db_booking?.contact?.postal_code || "",
            city: db_booking?.contact?.city || "",
            email: db_booking?.contact?.email || "",
            phone: db_booking?.contact?.phone || "",
        },
        main_booker: Math.max(
            0,
            Math.min(main_booker_index, adults.length - 1)
        ),
        has_confirmed: false,
        has_accepted_conditions: false,
    });

    // Helper: Clear alle errors voor travelers boven een bepaalde index
    function clearErrorsAboveIndex(type, maxIndex) {
        const prefix = `travelers.${type}.`;
        const errorKeys = Object.keys(booking.errors).filter((key) => {
            if (!key.startsWith(prefix)) return false;

            const match = key.match(/^travelers\.(adults|children)\.(\d+)\./);
            if (match) {
                const index = parseInt(match[2], 10);
                return index >= maxIndex;
            }
            return false;
        });

        errorKeys.forEach((key) => booking.clearErrors(key));
    }

    function syncTravelers(type, targetCount) {
        const currentList = booking.travelers[type];
        const currentCount = currentList.length;

        if (targetCount > currentCount) {
            const newTravelers = Array(targetCount - currentCount)
                .fill(null)
                .map(() => createTraveler());
            currentList.push(...newTravelers);
        } else if (targetCount < currentCount) {
            // Clear errors for travelers that are going to be removed
            clearErrorsAboveIndex(type, targetCount);
            // Remove travelers
            currentList.splice(targetCount);
        }
    }

    // Sync travelers when participant counts change
    watch(
        () => [booking.participants.adults, booking.participants.children],
        ([adultsCount, childrenCount]) => {
            try {
                syncTravelers("adults", adultsCount);
                syncTravelers("children", childrenCount);
            } catch (error) {
                console.error("Failed to sync travelers:", error);
            }
        },
        { immediate: true }
    );

    // Adjust main_booker when adults list changes
    watch(
        () => booking.travelers.adults.length,
        (newLength) => {
            if (newLength > 0) {
                booking.main_booker = Math.min(
                    booking.main_booker,
                    newLength - 1
                );
            } else {
                booking.main_booker = 0;
            }
        }
    );

    // Reset confirmation on errors
    watch(
        () => booking.hasErrors,
        (hasErrors) => {
            if (hasErrors) {
                booking.has_confirmed = false;
                booking.has_accepted_conditions = false;
            }
        }
    );

    // Auto-cleanup stale errors (safety net)
    watchEffect(() => {
        const currentErrors = Object.keys(booking.errors);

        currentErrors.forEach(errorKey => {
            const travelerMatch = errorKey.match(/^travelers\.(adults|children)\.(\d+)\./);

            if (travelerMatch) {
                const [, type, indexStr] = travelerMatch;
                const index = parseInt(indexStr, 10);

                // Check if this traveler still exists
                if (!booking.travelers[type] || !booking.travelers[type][index]) {
                    booking.clearErrors(errorKey);
                }
            }
        });
    });

    return {
        booking,
        constraints: BOOKING_CONSTRAINTS,
    };
}
