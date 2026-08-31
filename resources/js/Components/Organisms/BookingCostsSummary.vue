<script setup>
import { computed } from 'vue';
import { Euro, Info } from 'lucide-vue-next';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    booking: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const costCategories = computed(() => page.props.cost_categories ?? []);

const categoryLabel = (id) =>
    costCategories.value.find((c) => c.id === id)?.name ?? id;

const fmt = (cents) =>
    new Intl.NumberFormat('nl-NL', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 2,
    }).format((cents ?? 0) / 100);

const itemSubtotalCents = (item) => {
    const amount = Number(item.amount_per_person);
    const quantity = Number(item.quantity);
    if (!Number.isFinite(amount) || !Number.isFinite(quantity)) return 0;
    return Math.round(amount * 100) * quantity;
};

const totalCostCents = computed(() =>
    (props.booking.cost_items ?? []).reduce(
        (acc, item) => acc + itemSubtotalCents(item),
        0,
    ),
);

const marginFraction = computed(() => {
    const m = Number(props.booking.margin_percentage);
    if (!Number.isFinite(m)) return 0;
    return Math.min(Math.max(m, 0), 95) / 100;
});

const toCents = (euros) => {
    const value = Number(euros);
    return Number.isFinite(value) ? Math.round(value * 100) : 0;
};

// The fee is charged per adult; children do not pay it.
const adultCount = computed(() => {
    const count = Number(props.booking.participants?.adults);
    return Number.isFinite(count) ? count : 0;
});

const feeTotalCents = computed(
    () => toCents(props.booking.fee_per_person) * adultCount.value,
);

// Snapshot for an existing booking, current settings for a new one.
const feesAndFunds = computed(() => page.props.fees_and_funds ?? {});

const feesAndFundsRows = computed(() =>
    Object.entries(feesAndFunds.value).filter(([, cents]) => Number(cents) > 0),
);

const feesAndFundsTotalCents = computed(() =>
    Object.values(feesAndFunds.value).reduce(
        (acc, cents) => acc + (Number(cents) || 0),
        0,
    ),
);

// The sales price of the trip itself, before fees and funds.
const marginedPriceCents = computed(() => {
    if (!props.booking.margin_in_percentage) {
        return (
            totalCostCents.value +
            toCents(props.booking.margin_amount) +
            feeTotalCents.value
        );
    }
    if (marginFraction.value >= 1) return 0;
    return Math.round(totalCostCents.value / (1 - marginFraction.value));
});

const calculatedPriceCents = computed(
    () => marginedPriceCents.value + feesAndFundsTotalCents.value,
);

const finalPriceCents = computed(() => {
    const fp = props.booking.final_price;
    if (fp === null || fp === '' || fp === undefined) return null;
    return Math.round(Number(fp) * 100);
});

const hasOverride = computed(() => finalPriceCents.value !== null);

const displayPriceCents = computed(() =>
    hasOverride.value ? finalPriceCents.value : calculatedPriceCents.value,
);
</script>

<template>
    <section class="w-full">
        <h2 class="text-xl font-semibold text-brand-primary mb-3 flex items-center gap-2">
            {{ $t('booking_steps.overview.costs_heading') }}
        </h2>
        <div class="grid gap-1 ml-4">
            <div v-for="(item, index) in booking.cost_items ?? []" :key="index" class="flex items-center">
                <Euro class="inline w-4 h-4 mr-2 text-brand-light" />
                <span class="flex-1 flex items-center gap-2">
                    <span>
                        <span class="font-medium">{{ categoryLabel(item.category) }}</span>
                        <span v-if="item.label" class="text-brand-text/70"> – {{ item.label }}</span>
                    </span>
                    <span class="text-sm text-brand-light/70">{{ fmt(Math.round(Number(item.amount_per_person || 0) * 100)) }} × {{ item.quantity }}</span>
                    <span class="flex-1 border-b border-dotted border-brand-light/60"></span>
                    <span class="font-bold">{{ fmt(itemSubtotalCents(item)) }}</span>
                </span>
            </div>

            <div class="flex items-center pt-2 mt-1 border-t border-brand-light/30">
                <Euro class="inline w-4 h-4 mr-2 text-brand-light" />
                <span class="flex-1 flex items-center gap-2">
                    <span>{{ $t('booking_steps.price.total_cost') }}</span>
                    <span class="flex-1 border-b border-dotted border-brand-light/60"></span>
                    <span class="font-bold">{{ fmt(totalCostCents) }}</span>
                </span>
            </div>

            <div class="flex items-center">
                <Euro class="inline w-4 h-4 mr-2 text-brand-light" />
                <span class="flex-1 flex items-center gap-2">
                    <span v-if="booking.margin_in_percentage">{{ $t('booking_steps.price.margin') }} ({{ booking.margin_percentage }}%)</span>
                    <span v-else>{{ $t('booking_steps.price.margin_fixed') }}</span>
                    <span class="flex-1 border-b border-dotted border-brand-light/60"></span>
                    <span class="font-bold">{{ fmt(booking.margin_in_percentage ? marginedPriceCents - totalCostCents : toCents(booking.margin_amount)) }}</span>
                </span>
            </div>

            <div v-if="!booking.margin_in_percentage" class="flex items-center">
                <Euro class="inline w-4 h-4 mr-2 text-brand-light" />
                <span class="flex-1 flex items-center gap-2">
                    <span>{{ $t('booking_steps.price.fee_total', { count: adultCount }) }}</span>
                    <span class="flex-1 border-b border-dotted border-brand-light/60"></span>
                    <span class="font-bold">{{ fmt(feeTotalCents) }}</span>
                </span>
            </div>

            <div v-for="[key, cents] in feesAndFundsRows" :key="key" class="flex items-center">
                <Euro class="inline w-4 h-4 mr-2 text-brand-light" />
                <span class="flex-1 flex items-center gap-2">
                    <span>{{ $t(`booking_steps.overview.${key}`) }}</span>
                    <span class="flex-1 border-b border-dotted border-brand-light/60"></span>
                    <span class="font-bold">{{ fmt(cents) }}</span>
                </span>
            </div>

            <div class="flex items-center">
                <Euro class="inline w-4 h-4 mr-2 text-brand-light" />
                <span class="flex-1 flex items-center gap-2">
                    <span>{{ $t('booking_steps.price.calculated_price') }}</span>
                    <span class="flex-1 border-b border-dotted border-brand-light/60"></span>
                    <span class="font-bold" :class="{ 'line-through opacity-50': hasOverride }">{{ fmt(calculatedPriceCents) }}</span>
                </span>
            </div>

            <div v-if="hasOverride" class="flex items-center text-brand-accent">
                <Info class="inline w-4 h-4 mr-2" />
                <span class="flex-1 flex items-center gap-2">
                    <span>{{ $t('booking_steps.price.final_price') }}</span>
                    <span class="flex-1 border-b border-dotted border-brand-accent/40"></span>
                    <span class="font-bold">{{ fmt(finalPriceCents) }}</span>
                </span>
            </div>

            <div class="flex items-center pt-2 mt-1 border-t border-brand-light/30">
                <Euro class="inline w-4 h-4 mr-2 text-brand-primary" />
                <span class="flex-1 flex items-center gap-2">
                    <span class="font-semibold">{{ $t('booking_steps.overview.costs_total') }}</span>
                    <span class="flex-1"></span>
                    <span class="font-bold text-brand-primary">{{ fmt(displayPriceCents) }}</span>
                </span>
            </div>
        </div>
    </section>
</template>
