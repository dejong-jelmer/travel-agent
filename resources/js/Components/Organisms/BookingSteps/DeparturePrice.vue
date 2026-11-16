<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Info } from '@lucide/vue';
import { useDeparturePrice } from '@/Composables/useDeparturePrice.js';
import { formatCents } from '@/Support/money.js';

// The price step of a booking on a departure: the fixed price of the departure, the single rooms and an optional
// final price. There are no cost items or margin here; those belong to the departure.
const props = defineProps({
    booking: { type: Object, required: true },
});

const page = usePage();
const fmt = (cents) => formatCents(cents, page.props.locale);

const {
    adults,
    travelers,
    singleRooms,
    hasPrice,
    pricePerPersonCents,
    singleSupplementCents,
    baseTotalCents,
    supplementTotalCents,
    feesAndFundsRows,
    calculatedPriceCents,
    hasOverride,
    displayPriceCents,
} = useDeparturePrice(props.booking, computed(() => page.props.fees_and_funds ?? {}));

// Shows the default while nothing was chosen; typing a number makes it a choice
const singleRoomsModel = computed({
    get: () => singleRooms.value,
    set: (value) => {
        props.booking.single_rooms = value === '' ? null : Number(value);
        props.booking.clearErrors('single_rooms');
    },
});
</script>

<template>
    <div key="departure-price" class="space-y-6">
        <h2 class="text-xl font-bold text-brand-primary">{{ $t('booking_steps.price.departure_heading') }}</h2>
        <p class="text-sm text-brand-text/70">{{ $t('booking_steps.price.departure_intro') }}</p>
        <hr class="border-brand-subtle/20">

        <p v-if="!hasPrice" data-error="true" data-testid="no-departure-price"
            class="text-sm text-status-error bg-status-error/10 rounded-lg p-2">
            {{ $t('validation.errors.departure_without_price') }}
        </p>

        <section v-else class="space-y-4 p-4 border rounded-lg bg-white">
            <div class="max-w-xs">
                <Input type="number" name="single_rooms" :label="$t('booking_steps.price.single_rooms')"
                    :showLabel="true" v-model="singleRoomsModel" min="0" :max="adults" step="1"
                    :feedback="booking.errors['single_rooms']" />
                <p class="mt-1 text-xs text-brand-text/70">
                    {{ $t('booking_steps.price.single_rooms_help', { amount: fmt(singleSupplementCents) }) }}
                </p>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-brand-subtle/20">
                <span class="text-brand-text/70">
                    {{ $t('booking_steps.price.travelers_total', { count: travelers }) }} ({{ fmt(pricePerPersonCents) }})
                </span>
                <span class="font-medium text-brand-text/70" data-testid="base-total">{{ fmt(baseTotalCents) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-brand-text/70">
                    {{ $t('booking_steps.price.single_rooms_total', { count: singleRooms }) }}
                </span>
                <span class="font-medium text-brand-text/70" data-testid="supplement-total">{{ fmt(supplementTotalCents) }}</span>
            </div>
            <div v-for="[key, cents] in feesAndFundsRows" :key="key" class="flex items-center justify-between">
                <span class="text-brand-text/70">{{ $t(`booking_steps.overview.${key}`) }}</span>
                <span class="font-medium text-brand-text/70">{{ fmt(cents) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-brand-text/70">{{ $t('booking_steps.price.calculated_price') }}</span>
                <span class="font-bold text-brand-primary" data-testid="calculated-price"
                    :class="{ 'line-through opacity-50': hasOverride }">{{ fmt(calculatedPriceCents) }}</span>
            </div>

            <div class="grid grid-cols-1 tablet:grid-cols-2 gap-4 tablet:gap-6 items-end pt-2 border-t border-brand-subtle/20">
                <div>
                    <Input type="number" name="final_price" :label="$t('booking_steps.price.final_price')"
                        :showLabel="true" v-model="booking.final_price" step="10.00" min="0"
                        :placeholder="$t('booking_steps.price.final_price_help')"
                        :feedback="booking.errors['final_price']" @keyup="booking.clearErrors('final_price')" />
                    <p v-if="hasOverride" class="mt-1 flex items-center gap-1 text-xs text-brand-accent">
                        <Info class="w-3 h-3" />
                        {{ $t('booking_steps.price.override_active') }}
                    </p>
                </div>
                <div class="flex items-center justify-between text-lg">
                    <span class="font-semibold text-brand-primary">{{ $t('booking_steps.price.display_price') }}</span>
                    <span class="font-bold text-brand-primary" data-testid="display-price">{{ fmt(displayPriceCents) }}</span>
                </div>
            </div>
        </section>
    </div>
</template>
