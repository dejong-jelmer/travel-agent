/**
 * @fileoverview Runs the shared price vectors against the JS implementation.
 *
 * The same vectors run against Booking::computeCalculatedPrice() in
 * tests/Unit/BookingPriceVectorsTest.php. If you change the formula here
 * without changing it there, that test fails — and the other way around.
 */

import { describe, it, expect } from "vitest";
import { calculateBookingPrice } from "@/Support/bookingPrice.js";
import fixture from "../../../../tests/fixtures/booking-price-vectors.json";

describe("calculateBookingPrice — shared vectors", () => {
    it.each(fixture.vectors.map((vector) => [vector.name, vector]))(
        "%s",
        (_name, vector) => {
            const result = calculateBookingPrice({
                costItems: vector.cost_items,
                marginInPercentage: vector.margin_in_percentage,
                marginPercentage: vector.margin_percentage,
                marginAmountCents: vector.margin_amount ?? 0,
                feePerPersonCents: vector.fee_per_person ?? 0,
                adults: vector.adults,
                feesAndFunds: vector.fees_and_funds,
            });

            expect(result.totalCostCents).toBe(vector.expected.total_cost);
            expect(result.feesAndFundsTotalCents).toBe(
                vector.expected.fees_and_funds_total,
            );
            expect(result.marginedPriceCents).toBe(
                vector.expected.margined_price,
            );
            expect(result.calculatedPriceCents).toBe(
                vector.expected.calculated_price,
            );
        },
    );

    it("covers both margin modes", () => {
        const modes = new Set(
            fixture.vectors.map((vector) => vector.margin_in_percentage),
        );

        expect(modes).toEqual(new Set([true, false]));
    });
});

describe("calculateBookingPrice — defaults", () => {
    it("returns zeroes for an empty booking", () => {
        const result = calculateBookingPrice();

        expect(result.totalCostCents).toBe(0);
        expect(result.calculatedPriceCents).toBe(0);
    });

    it("ignores cost items with unusable numbers", () => {
        const result = calculateBookingPrice({
            costItems: [
                { amount_per_person: 1000, quantity: 2 },
                { amount_per_person: null, quantity: 2 },
                { amount_per_person: 500, quantity: undefined },
            ],
            marginPercentage: 0,
        });

        expect(result.totalCostCents).toBe(2000);
    });

    it("clamps the margin percentage to the allowed range", () => {
        const above = calculateBookingPrice({
            costItems: [{ amount_per_person: 10000, quantity: 1 }],
            marginPercentage: 130,
        });
        const capped = calculateBookingPrice({
            costItems: [{ amount_per_person: 10000, quantity: 1 }],
            marginPercentage: 95,
        });

        expect(above.calculatedPriceCents).toBe(capped.calculatedPriceCents);
    });
});
