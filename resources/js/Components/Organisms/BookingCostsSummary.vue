<script setup>
import { computed } from 'vue';
import { Euro, Info } from '@lucide/vue';
import { usePage } from '@inertiajs/vue3';
import { useBookingPrice } from '@/Composables/useBookingPrice.js';
import { formatCents } from '@/Support/money.js';

const props = defineProps({
    booking: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const costCategories = computed(() => page.props.cost_categories ?? []);

const fmt = (cents) => formatCents(cents, page.props.locale);

const categoryLabel = (id) =>
    costCategories.value.find((c) => c.id === id)?.name ?? id;

// Snapshot for an existing booking, current settings for a new one.
const {
    marginInPercentage,
    adultCount,
    costItemSubtotalCents,
    totalCostCents,
    fixedMarginCents,
    marginCents,
    feeTotalCents,
    feesAndFundsRows,
    calculatedPriceCents,
    finalPriceCents,
    hasOverride,
    displayPriceCents,
} = useBookingPrice(
    props.booking,
    computed(() => page.props.fees_and_funds ?? {}),
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
                    <span class="font-bold">{{ fmt(costItemSubtotalCents(item)) }}</span>
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
                    <span v-if="marginInPercentage">{{ $t('booking_steps.price.margin') }} ({{ booking.margin_percentage }}%)</span>
                    <span v-else>{{ $t('booking_steps.price.margin_fixed') }}</span>
                    <span class="flex-1 border-b border-dotted border-brand-light/60"></span>
                    <span class="font-bold">{{ fmt(marginInPercentage ? marginCents : fixedMarginCents) }}</span>
                </span>
            </div>

            <div v-if="!marginInPercentage" class="flex items-center">
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
