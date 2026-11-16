/**
 * @fileoverview The price and the payload of a booking on a departure of a group trip.
 */

import { describe, it, expect } from "vitest";
import { reactive } from "vue";
import { calculateDeparturePrice, defaultSingleRooms } from "@/Support/bookingPrice.js";
import {
    isGroupBooking,
    effectiveSingleRooms,
    departureBookingPayload,
    useDeparturePrice,
} from "@/Composables/useDeparturePrice.js";

const fees = { booking_fee: 2500, guarantee_fund: 1000, emergency_fund: 250 };

const form = (attributes = {}) => reactive({
    trip: { id: 1, format: "group" },
    departure_id: 7,
    departure: { id: 7, departs_on: "2027-06-20", returns_on: "2027-06-26", available_seats: 4, price_per_person: 120000, single_supplement: 15000 },
    participants: { adults: 2, children: 1 },
    single_rooms: null,
    final_price: null,
    cost_items: [{ category: "train", label: "", amount_per_person: null, quantity: 2 }],
    margin_percentage: 15,
    margin_amount: 250,
    fee_per_person: 75,
    ...attributes,
});

describe("calculateDeparturePrice", () => {
    // The same numbers as GroupBookingTest::test_booking_on_a_departure_is_sold_at_the_price_of_the_departure in PHP
    it("adds up the travelers, the single rooms and the fees and funds", () => {
        expect(calculateDeparturePrice({
            pricePerPersonCents: 120000,
            singleSupplementCents: 15000,
            travelers: 3,
            singleRooms: 0,
            feesAndFunds: fees,
        })).toEqual({
            baseTotalCents: 360000,
            supplementTotalCents: 0,
            feesAndFundsTotalCents: 3750,
            calculatedPriceCents: 363750,
        });
    });

    it("charges the supplement for every single room", () => {
        const price = calculateDeparturePrice({ pricePerPersonCents: 120000, singleSupplementCents: 15000, travelers: 2, singleRooms: 2, feesAndFunds: fees });

        expect(price.supplementTotalCents).toBe(30000);
        expect(price.calculatedPriceCents).toBe(273750);
    });

    it("gives a traveler on their own a single room by default", () => {
        expect(defaultSingleRooms(1)).toBe(1);
        expect(defaultSingleRooms(2)).toBe(0);
        expect(defaultSingleRooms(0)).toBe(0);
    });
});

describe("isGroupBooking", () => {
    it("is a booking of a group trip or a booking that is on a departure", () => {
        expect(isGroupBooking({ trip: { format: "group" } })).toBe(true);
        expect(isGroupBooking({ trip: { format: "individual" }, departure_id: 7 })).toBe(true);
        expect(isGroupBooking({ trip: { format: "individual" }, departure_id: null })).toBe(false);
        expect(isGroupBooking({ trip: {} })).toBe(false);
    });
});

describe("effectiveSingleRooms", () => {
    it("uses the default until a number is chosen", () => {
        expect(effectiveSingleRooms(form({ participants: { adults: 1, children: 0 } }))).toBe(1);
        expect(effectiveSingleRooms(form())).toBe(0);
        expect(effectiveSingleRooms(form({ participants: { adults: 1, children: 0 }, single_rooms: 0 }))).toBe(0);
        expect(effectiveSingleRooms(form({ single_rooms: "2" }))).toBe(2);
    });
});

describe("useDeparturePrice", () => {
    it("follows the form", () => {
        const booking = form();
        const price = useDeparturePrice(booking, fees);

        expect(price.hasPrice.value).toBe(true);
        expect(price.calculatedPriceCents.value).toBe(363750);

        booking.single_rooms = 1;
        booking.participants.children = 0;
        expect(price.calculatedPriceCents.value).toBe(258750);
        expect(price.displayPriceCents.value).toBe(258750);

        booking.final_price = 2400;
        expect(price.hasOverride.value).toBe(true);
        expect(price.displayPriceCents.value).toBe(240000);
    });

    it("has no price until a departure is picked", () => {
        expect(useDeparturePrice(form({ departure: null, departure_id: null }), fees).hasPrice.value).toBe(false);
    });
});

describe("departureBookingPayload", () => {
    it("leaves the cost items, the margin and the form's own departure out", () => {
        const payload = departureBookingPayload(form({ participants: { adults: 1, children: 0 }, final_price: "1999.50" }));

        expect(payload).not.toHaveProperty("cost_items");
        expect(payload).not.toHaveProperty("margin_percentage");
        expect(payload).not.toHaveProperty("margin_amount");
        expect(payload).not.toHaveProperty("fee_per_person");
        expect(payload).not.toHaveProperty("departure");
        expect(payload.departure_id).toBe(7);
        expect(payload.single_rooms).toBe(1);
        expect(payload.final_price).toBe(199950);
    });

    it("sends no final price when none was typed", () => {
        expect(departureBookingPayload(form({ final_price: "" })).final_price).toBeNull();
    });
});
