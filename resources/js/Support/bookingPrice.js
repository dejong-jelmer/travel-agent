/**
 * Booking pricing rules. Every amount in and out is in cents.
 *
 * This mirrors Booking::computeCalculatedPrice() in PHP. The two cannot share
 * code, so they share a specification instead: the vectors in
 * tests/fixtures/booking-price-vectors.json are run against both sides. Change
 * the formula here without changing it there, and the PHP test fails (and the
 * other way around).
 */

const MAX_MARGIN_PERCENTAGE = 95;

const toNumber = (value) => {
    const number = Number(value);
    return Number.isFinite(number) ? number : 0;
};

/**
 * Sum of all cost items in cents.
 *
 * @param {Array<{amount_per_person: number, quantity: number}>} items
 */
export function sumCostItems(items = []) {
    return (items ?? []).reduce(
        (acc, item) => acc + toNumber(item.amount_per_person) * toNumber(item.quantity),
        0,
    );
}

/**
 * Sum of the fees and funds in cents.
 *
 * @param {Object.<string, number>} feesAndFunds
 */
export function sumFeesAndFunds(feesAndFunds = {}) {
    return Object.values(feesAndFunds ?? {}).reduce(
        (acc, cents) => acc + toNumber(cents),
        0,
    );
}

/**
 * The fees and funds worth showing, as [key, cents] pairs.
 *
 * @param {Object.<string, number>} feesAndFunds
 */
export function feesAndFundsEntries(feesAndFunds = {}) {
    return Object.entries(feesAndFunds ?? {}).filter(
        ([, cents]) => toNumber(cents) > 0,
    );
}

/**
 * Break the sales price down into its parts.
 *
 * Percentage margin works on the sales side: 35% margin means cost is 65% of
 * the sales price. A fixed margin is added on top of the cost instead, together
 * with a fee for every adult. The fees and funds are charged once per booking,
 * on top of either margin, so no margin is made on them.
 */
export function calculateBookingPrice({
    costItems = [],
    marginInPercentage = true,
    marginPercentage = 0,
    marginAmountCents = 0,
    feePerPersonCents = 0,
    adults = 0,
    feesAndFunds = {},
} = {}) {
    const totalCostCents = sumCostItems(costItems);
    const feeTotalCents = toNumber(feePerPersonCents) * toNumber(adults);
    const feesAndFundsTotalCents = sumFeesAndFunds(feesAndFunds);

    const marginedPriceCents = marginInPercentage
        ? percentagePrice(totalCostCents, marginPercentage)
        : totalCostCents + toNumber(marginAmountCents) + feeTotalCents;

    return {
        totalCostCents,
        feeTotalCents,
        feesAndFundsTotalCents,
        marginedPriceCents,
        marginCents: marginedPriceCents - totalCostCents,
        calculatedPriceCents: marginedPriceCents + feesAndFundsTotalCents,
    };
}

function percentagePrice(totalCostCents, marginPercentage) {
    const clamped = Math.min(
        Math.max(toNumber(marginPercentage), 0),
        MAX_MARGIN_PERCENTAGE,
    );

    return Math.round(totalCostCents / (1 - clamped / 100));
}
