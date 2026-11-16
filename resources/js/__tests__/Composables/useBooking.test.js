/**
 * @fileoverview The margins a booking form starts with.
 */

import { describe, it, expect, vi, beforeEach } from "vitest";
import { reactive } from "vue";

const page = { props: {} };

vi.mock("@inertiajs/vue3", () => ({
    usePage: () => page,
    useForm: (data) =>
        reactive({ ...data, errors: {}, clearErrors: () => {} }),
}));

import { useBooking } from "@/Composables/useBooking.js";

describe("useBooking — default margins", () => {
    beforeEach(() => {
        page.props = {
            default_margin_basis_points: 1250,
            default_margin_amount: 25000,
            default_fee_per_person: 7550,
        };
    });

    it("starts a new booking with the defaults from the settings, in euros", () => {
        const { booking } = useBooking({});

        expect(booking.margin_percentage).toBe(12.5);
        expect(booking.margin_amount).toBe(250);
        expect(booking.fee_per_person).toBe(75.5);
    });

    it("leaves the amounts empty when the settings have none", () => {
        page.props = { default_margin_basis_points: 1250 };

        const { booking } = useBooking({});

        expect(booking.margin_amount).toBeNull();
        expect(booking.fee_per_person).toBeNull();
    });

    it("keeps the amounts of an existing booking, also when empty", () => {
        const { booking } = useBooking(
            {},
            { margin_basis_points: 2000, margin_amount: 10000, fee_per_person: null },
        );

        expect(booking.margin_percentage).toBe(20);
        expect(booking.margin_amount).toBe(100);
        expect(booking.fee_per_person).toBeNull();
    });
});
