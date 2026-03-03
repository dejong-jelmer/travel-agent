<script setup>
import axios from "@/axios"
import { computed, ref } from 'vue'
import { computedAsync } from '@vueuse/core'
import { Euro, LoaderCircle } from "lucide-vue-next"
import { useDateFormatter } from '@/Composables/useDateFormatter.js'

const { toDateString } = useDateFormatter()

const props = defineProps({
    booking: {
        type: Object,
        required: true
    }
})

const fmt = (value) => new Intl.NumberFormat('nl-NL', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value)

const totalTravelers = computed(() =>
    (props.booking.participants?.adults || 0) + (props.booking.participants?.children || 0)
)

const isLoadingPrices = ref(false)

const asyncTripPrices = computedAsync(
    () => axios.get(route("trips.prices", props.booking.trip), {
        params: {
            travelers: totalTravelers.value,
            date: toDateString(props.booking.departure_date),
        },
    })
        .then((response) => response.data)
        .catch((error) => { console.error(error); return null }),
    null,
    { evaluating: isLoadingPrices }
)

const totalPrice = computed(() => fmt(asyncTripPrices.value?.grand_total ?? 0))
</script>

<template>
    <section class="w-full">
        <h2 class="text-xl font-semibold text-brand-primary mb-3 flex items-center gap-2">
            {{ $t('booking_steps.overview.costs_heading') }}
        </h2>
        <div class="grid gap-1 ml-4">
            <div v-if="isLoadingPrices || asyncTripPrices?.total_price > 0" class="flex items-center">
                <Euro class="inline w-4 h-4 mr-2 text-brand-light" />
                <span class="flex-1 flex items-center gap-2">
                    <span>{{ $t('booking_steps.overview.trip_price') }}</span>
                    <LoaderCircle v-if="isLoadingPrices" class="size-2 animate-spin" />
                    <span v-else class="text-sm text-brand-light/70">€ {{ asyncTripPrices?.price_per_person }} × {{ totalTravelers }}</span>
                    <span class="flex-1 border-b border-dotted border-brand-light/60"></span>
                    <span class="font-bold">
                        <LoaderCircle v-if="isLoadingPrices" class="size-5 animate-spin" />
                        <span v-else>€ {{ fmt(asyncTripPrices?.total_price) }}</span>
                    </span>
                </span>
            </div>

            <div v-if="totalTravelers === 1 && asyncTripPrices?.single_supplement > 0" class="flex items-center">
                <Euro class="inline w-4 h-4 mr-2 text-brand-light" />
                <span class="flex-1 flex items-center gap-2">
                    <span>{{ $t('booking_steps.overview.single_supplement') }}</span>
                    <span class="flex-1 border-b border-dotted border-brand-light/60"></span>
                    <span class="font-bold">€ {{ fmt(asyncTripPrices.single_supplement) }}</span>
                </span>
            </div>

            <div v-if="asyncTripPrices?.booking_fee > 0" class="flex items-center">
                <Euro class="inline w-4 h-4 mr-2 text-brand-light" />
                <span class="flex-1 flex items-center gap-2">
                    <span>{{ $t('booking_steps.overview.booking_fee') }}</span>
                    <span class="flex-1 border-b border-dotted border-brand-light/60"></span>
                    <span class="font-bold">€ {{ fmt(asyncTripPrices.booking_fee) }}</span>
                </span>
            </div>

            <div v-if="asyncTripPrices?.guarantee_fund > 0" class="flex items-center">
                <Euro class="inline w-4 h-4 mr-2 text-brand-light" />
                <span class="flex-1 flex items-center gap-2">
                    <span>{{ $t('booking_steps.overview.guarantee_fund') }}</span>
                    <span class="flex-1 border-b border-dotted border-brand-light/60"></span>
                    <span class="font-bold">€ {{ fmt(asyncTripPrices.guarantee_fund) }}</span>
                </span>
            </div>

            <div v-if="asyncTripPrices?.emergency_fund > 0" class="flex items-center">
                <Euro class="inline w-4 h-4 mr-2 text-brand-light" />
                <span class="flex-1 flex items-center gap-2">
                    <span>{{ $t('booking_steps.overview.emergency_fund') }}</span>
                    <span class="flex-1 border-b border-dotted border-brand-light/60"></span>
                    <span class="font-bold">€ {{ fmt(asyncTripPrices.emergency_fund) }}</span>
                </span>
            </div>

            <div class="flex items-center pt-2 mt-1 border-t border-brand-light/30">
                <Euro class="inline w-4 h-4 mr-2 text-brand-primary" />
                <span class="flex-1 flex items-center gap-2">
                    <span class="font-semibold">{{ $t('booking_steps.overview.costs_total') }}</span>
                    <span class="flex-1"></span>
                    <LoaderCircle v-if="isLoadingPrices" class="size-5 animate-spin" />
                    <span v-else class="font-bold text-brand-primary">€ {{ totalPrice }}</span>
                </span>
            </div>
        </div>
    </section>
</template>
