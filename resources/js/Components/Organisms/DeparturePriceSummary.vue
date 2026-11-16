<script setup>
import { computed } from 'vue';
import { Euro, Info } from '@lucide/vue';
import { usePage } from '@inertiajs/vue3';
import { useDeparturePrice } from '@/Composables/useDeparturePrice.js';
import { formatCents } from '@/Support/money.js';

// The read-only price of a booking on a departure, as BookingCostsSummary is for a booking built from cost items.
const props = defineProps({
    booking: { type: Object, required: true },
});

const page = usePage();
const fmt = (cents) => formatCents(cents, page.props.locale);

const {
    travelers,
    singleRooms,
    hasPrice,
    baseTotalCents,
    supplementTotalCents,
    feesAndFundsRows,
    calculatedPriceCents,
    finalPriceCents,
    hasOverride,
    displayPriceCents,
} = useDeparturePrice(props.booking, computed(() => page.props.fees_and_funds ?? {}));
</script>

<template>
    <section v-if="hasPrice" class="w-full" data-testid="departure-price-summary">
        <h2 class="text-xl font-semibold text-brand-primary mb-3 flex items-center gap-2">
            {{ $t('booking_steps.overview.costs_heading') }}
        </h2>
        <div class="grid gap-1 ml-4">
            <div class="flex items-center">
                <Euro class="inline w-4 h-4 mr-2 text-brand-light" />
                <span class="flex-1 flex items-center gap-2">
                    <span>{{ $t('booking_steps.price.travelers_total', { count: travelers }) }}</span>
                    <span class="flex-1 border-b border-dotted border-brand-light/60"></span>
                    <span class="font-bold">{{ fmt(baseTotalCents) }}</span>
                </span>
            </div>
            <div v-if="singleRooms > 0" class="flex items-center">
                <Euro class="inline w-4 h-4 mr-2 text-brand-light" />
                <span class="flex-1 flex items-center gap-2">
                    <span>{{ $t('booking_steps.price.single_rooms_total', { count: singleRooms }) }}</span>
                    <span class="flex-1 border-b border-dotted border-brand-light/60"></span>
                    <span class="font-bold">{{ fmt(supplementTotalCents) }}</span>
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
