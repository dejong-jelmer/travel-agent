// Composables/useDeparturePrice.js
import { computed, unref } from "vue";
import {
    calculateDeparturePrice,
    defaultSingleRooms,
    feesAndFundsEntries,
} from "@/Support/bookingPrice.js";

/**
 * Whether the booking form is for a group trip, which is booked on a departure
 * and sold at the price of that departure.
 */
export const isGroupBooking = (booking) =>
    booking?.trip?.format === "group" || !!booking?.departure_id;

/**
 * The single rooms the form will submit: what was chosen, or the default for
 * the number of travelers while nothing was.
 */
export function effectiveSingleRooms(booking) {
    const chosen = booking?.single_rooms;

    if (chosen === null || chosen === undefined || chosen === "") {
        const participants = booking?.participants ?? {};

        return defaultSingleRooms((participants.adults || 0) + (participants.children || 0));
    }

    return Number(chosen) || 0;
}

/**
 * Live price breakdown of a booking on a departure. Everything returned is in
 * cents. The server calculates the same price on save, so this is a preview —
 * see calculateDeparturePrice() in resources/js/Support/bookingPrice.js.
 *
 * The price per person and the supplement come from form.departure: the
 * departure picked in the wizard, or the snapshot of an existing booking.
 *
 * @param {Object} booking  The Inertia form object (or a ref to it).
 * @param {Object} feesAndFunds  Amounts in cents, keyed by setting key.
 */
export function useDeparturePrice(booking, feesAndFunds) {
    const form = computed(() => unref(booking));
    const fees = computed(() => unref(feesAndFunds) ?? {});

    const adults = computed(() => Number(form.value.participants?.adults) || 0);
    const travelers = computed(() => adults.value + (Number(form.value.participants?.children) || 0));
    const singleRooms = computed(() => effectiveSingleRooms(form.value));

    const pricePerPersonCents = computed(() => form.value.departure?.price_per_person ?? null);
    const singleSupplementCents = computed(() => form.value.departure?.single_supplement ?? 0);

    const price = computed(() =>
        calculateDeparturePrice({
            pricePerPersonCents: pricePerPersonCents.value,
            singleSupplementCents: singleSupplementCents.value,
            travelers: travelers.value,
            singleRooms: singleRooms.value,
            feesAndFunds: fees.value,
        }),
    );

    const finalPriceCents = computed(() => {
        const finalPrice = form.value.final_price;
        if (finalPrice === null || finalPrice === "" || finalPrice === undefined) return null;
        return Math.round(Number(finalPrice) * 100);
    });

    const hasOverride = computed(() => finalPriceCents.value !== null);
    const calculatedPriceCents = computed(() => price.value.calculatedPriceCents);

    return {
        adults,
        travelers,
        singleRooms,
        // False until a departure with a price is picked
        hasPrice: computed(() => pricePerPersonCents.value !== null),
        pricePerPersonCents,
        singleSupplementCents,
        baseTotalCents: computed(() => price.value.baseTotalCents),
        supplementTotalCents: computed(() => price.value.supplementTotalCents),
        feesAndFundsRows: computed(() => feesAndFundsEntries(fees.value)),
        calculatedPriceCents,
        finalPriceCents,
        hasOverride,
        displayPriceCents: computed(() =>
            hasOverride.value ? finalPriceCents.value : calculatedPriceCents.value,
        ),
    };
}

/**
 * What a booking on a departure submits: no cost items or margin, the single
 * rooms as a number, and the final price in cents. `departure` only serves the
 * form and stays behind.
 */
export function departureBookingPayload(data) {
    const { departure, cost_items, margin_percentage, margin_amount, fee_per_person, ...rest } = data;

    return {
        ...rest,
        single_rooms: effectiveSingleRooms(data),
        final_price:
            data.final_price === null || data.final_price === "" || data.final_price === undefined
                ? null
                : Math.round(Number(data.final_price) * 100),
    };
}
