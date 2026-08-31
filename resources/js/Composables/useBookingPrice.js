// Composables/useBookingPrice.js
import { computed, unref } from "vue";
import {
    calculateBookingPrice,
    feesAndFundsEntries,
} from "@/Support/bookingPrice.js";

/**
 * Converts a euro amount from the booking form to cents.
 */
export const toCents = (euros) => {
    const value = Number(euros);
    return Number.isFinite(value) ? Math.round(value * 100) : 0;
};

/**
 * Live price breakdown for the booking form.
 *
 * The form holds euros for anything the user types; everything returned here is
 * in cents. The server recalculates the same price on save, so treat these as a
 * preview — see resources/js/Support/bookingPrice.js.
 *
 * @param {Object} booking  The Inertia form object (or a ref to it).
 * @param {Object} feesAndFunds  Amounts in cents, keyed by setting key.
 */
export function useBookingPrice(booking, feesAndFunds) {
    const form = computed(() => unref(booking));
    const fees = computed(() => unref(feesAndFunds) ?? {});

    const marginInPercentage = computed(
        () => form.value.margin_in_percentage !== false,
    );

    // The fee is charged per adult; children do not pay it.
    const adultCount = computed(() => {
        const count = Number(form.value.participants?.adults);
        return Number.isFinite(count) ? count : 0;
    });

    const costItemSubtotalCents = (item) =>
        toCents(item.amount_per_person) * (Number(item.quantity) || 0);

    const price = computed(() =>
        calculateBookingPrice({
            costItems: (form.value.cost_items ?? []).map((item) => ({
                amount_per_person: toCents(item.amount_per_person),
                quantity: item.quantity,
            })),
            marginInPercentage: marginInPercentage.value,
            marginPercentage: form.value.margin_percentage,
            marginAmountCents: toCents(form.value.margin_amount),
            feePerPersonCents: toCents(form.value.fee_per_person),
            adults: adultCount.value,
            feesAndFunds: fees.value,
        }),
    );

    const finalPriceCents = computed(() => {
        const fp = form.value.final_price;
        if (fp === null || fp === "" || fp === undefined) return null;
        return Math.round(Number(fp) * 100);
    });

    const hasOverride = computed(() => finalPriceCents.value !== null);

    const calculatedPriceCents = computed(() => price.value.calculatedPriceCents);

    return {
        marginInPercentage,
        adultCount,
        costItemSubtotalCents,
        totalCostCents: computed(() => price.value.totalCostCents),
        // The fixed margin as typed, without the per-adult fee.
        fixedMarginCents: computed(() => toCents(form.value.margin_amount)),
        marginCents: computed(() => price.value.marginCents),
        feeTotalCents: computed(() => price.value.feeTotalCents),
        feesAndFundsRows: computed(() => feesAndFundsEntries(fees.value)),
        feesAndFundsTotalCents: computed(
            () => price.value.feesAndFundsTotalCents,
        ),
        marginedPriceCents: computed(() => price.value.marginedPriceCents),
        calculatedPriceCents,
        finalPriceCents,
        hasOverride,
        displayPriceCents: computed(() =>
            hasOverride.value ? finalPriceCents.value : calculatedPriceCents.value,
        ),
    };
}
