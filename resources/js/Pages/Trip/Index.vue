<script setup>
import { computed, toRef } from 'vue';
import { useTripFilters } from '@/Composables/useTripFilters.js';

const props = defineProps({
    trips: Array,
    countries: Array,
});

const tripsRef = toRef(props, 'trips');

const {
    selectedCountries,
    selectedTransports,
    durationMin,
    durationMax,
    priceMin,
    priceMax,
    minDurationBound,
    maxDurationBound,
    minPriceBound,
    maxPriceBound,
    allTransports,
    filteredTrips,
    hasActiveFilters,
    isFiltering,
    tripCountForCountry,
    tripCountForTransport,
    clearAll,
} = useTripFilters(tripsRef);
</script>

<template>
    <Layout>

        <!-- Hero / page header -->
        <template #hero>
            <section class="bg-brand-primary py-16 tablet:py-24">
                <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-4 tablet:px-6 laptop:px-8">
                    <h1 class="text-3xl tablet:text-4xl laptop:text-5xl font-cormorant font-bold text-white mb-4">
                        {{ $t('trips.title') }}
                    </h1>
                    <p class="text-brand-light text-base tablet:text-lg max-w-2xl">
                        {{ $t('trips.subtitle') }}
                    </p>
                </div>
            </section>
        </template>

        <DecorativeLine />

        <!-- Main content -->
        <section class="py-12 tablet:py-20">
            <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-4 tablet:px-6 laptop:px-8">

                <div class="flex flex-col laptop:flex-row gap-8 laptop:gap-12 items-start">

                    <!-- Trips grid -->
                    <div class="flex-1 min-w-0">

                        <!-- Result count -->
                        <p class="text-sm text-brand-light mb-6 flex items-center gap-2">
                            <span>
                                {{ filteredTrips.length }}
                                {{ filteredTrips.length === 1 ? $t('trips.result_singular') : $t('trips.result_plural') }}
                            </span>
                            <span v-if="isFiltering" class="inline-flex items-center gap-1.5 text-brand-primary">
                                <svg class="animate-spin w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                            </span>
                            <span v-if="hasActiveFilters && !isFiltering">
                                &mdash;
                                <button @click="clearAll" class="underline hover:text-brand-primary transition-colors duration-200">
                                    {{ $t('trips.clear_all') }}
                                </button>
                            </span>
                        </p>

                        <!-- Grid -->
                        <div
                            v-if="filteredTrips.length"
                            class="grid grid-cols-1 tablet:grid-cols-2 gap-6 transition-opacity duration-300"
                            :class="isFiltering ? 'opacity-40 pointer-events-none' : 'opacity-100'"
                        >
                            <TripCard v-for="trip in filteredTrips" :key="trip.id" :trip="trip" />
                        </div>

                        <!-- Empty state -->
                        <div v-else class="text-center py-20 px-6 rounded-2xl border border-brand-primary/20 bg-brand-secondary">
                            <p class="text-xl font-cormorant font-semibold text-brand-primary mb-2">
                                {{ $t('trips.empty_title') }}
                            </p>
                            <p class="text-sm text-brand-text mb-6">
                                {{ $t('trips.empty_body') }}
                            </p>
                            <button
                                @click="clearAll"
                                class="inline-flex items-center gap-2 bg-brand-primary text-white font-bold py-2.5 px-5 rounded-xl hover:bg-brand-primary/90 transition-colors duration-200 text-sm"
                            >
                                {{ $t('trips.clear_all') }}
                            </button>
                        </div>

                    </div>

                    <!-- Filter sidebar -->
                    <aside class="w-full laptop:w-64 laptop:shrink-0 laptop:sticky laptop:top-28">
                        <div class="rounded-2xl border border-brand-primary/20 bg-white shadow-sm p-6 space-y-5">

                            <!-- Header -->
                            <div class="flex items-center justify-between">
                                <h2 class="text-base font-bold text-brand-primary font-poppins">
                                    {{ $t('trips.filter_title') }}
                                </h2>
                                <button
                                    v-if="hasActiveFilters"
                                    @click="clearAll"
                                    class="text-xs text-brand-link hover:text-brand-primary transition-colors duration-200 underline"
                                >
                                    {{ $t('trips.clear_all') }}
                                </button>
                            </div>

                            <!-- Bestemming -->
                            <div>
                                <p class="text-xs font-semibold text-brand-light uppercase tracking-wide mb-3">
                                    {{ $t('trips.filter_destination') }}
                                </p>
                                <ul class="space-y-1">
                                    <li v-for="country in countries" :key="country.code">
                                        <label class="flex items-center gap-3 cursor-pointer group py-1.5 px-2 rounded-lg hover:bg-brand-secondary transition-colors duration-200">
                                            <input
                                                type="checkbox"
                                                :value="country.code"
                                                v-model="selectedCountries"
                                                class="w-4 h-4 rounded border-brand-light cursor-pointer accent-[#2d5f6e]"
                                            />
                                            <span class="flex-1 text-sm text-brand-text group-hover:text-brand-primary transition-colors duration-200">
                                                {{ country.name }}
                                            </span>
                                            <span class="text-xs font-medium text-white bg-brand-light rounded-full px-2 py-0.5 min-w-[1.5rem] text-center">
                                                {{ tripCountForCountry(country.code) }}
                                            </span>
                                        </label>
                                    </li>
                                </ul>
                            </div>

                            <hr class="border-brand-primary/10" />

                            <!-- Vervoer -->
                            <div v-if="allTransports.length">
                                <p class="text-xs font-semibold text-brand-light uppercase tracking-wide mb-3">
                                    {{ $t('trips.filter_transport') }}
                                </p>
                                <ul class="space-y-1">
                                    <li v-for="mode in allTransports" :key="mode.value">
                                        <label class="flex items-center gap-3 cursor-pointer group py-1.5 px-2 rounded-lg hover:bg-brand-secondary transition-colors duration-200">
                                            <input
                                                type="checkbox"
                                                :value="mode.value"
                                                v-model="selectedTransports"
                                                class="w-4 h-4 rounded border-brand-light cursor-pointer accent-[#2d5f6e]"
                                            />
                                            <EnumIcon :enum="mode.value" class="w-4 h-4 text-brand-light flex-none" />
                                            <span class="flex-1 text-sm text-brand-text group-hover:text-brand-primary transition-colors duration-200">
                                                {{ mode.label }}
                                            </span>
                                            <span class="text-xs font-medium text-white bg-brand-light rounded-full px-2 py-0.5 min-w-[1.5rem] text-center">
                                                {{ tripCountForTransport(mode.value) }}
                                            </span>
                                        </label>
                                    </li>
                                </ul>
                            </div>

                            <hr class="border-brand-primary/10" />

                            <!-- Reisdagen -->
                            <div v-if="minDurationBound !== maxDurationBound">
                                <p class="text-xs font-semibold text-brand-light uppercase tracking-wide mb-3">
                                    {{ $t('trips.filter_duration') }}
                                </p>

                                <!-- Number inputs -->
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="flex-1">
                                        <label class="text-xs text-brand-light block mb-1">Min</label>
                                        <input
                                            type="number"
                                            v-model.number="durationMin"
                                            :min="minDurationBound"
                                            :max="durationMax"
                                            class="w-full text-sm border border-brand-primary/20 rounded-lg px-2 py-1.5 text-brand-text focus:outline-none focus:ring-1 focus:ring-brand-primary"
                                        />
                                    </div>
                                    <span class="text-brand-light mt-4">—</span>
                                    <div class="flex-1">
                                        <label class="text-xs text-brand-light block mb-1">Max</label>
                                        <input
                                            type="number"
                                            v-model.number="durationMax"
                                            :min="durationMin"
                                            :max="maxDurationBound"
                                            class="w-full text-sm border border-brand-primary/20 rounded-lg px-2 py-1.5 text-brand-text focus:outline-none focus:ring-1 focus:ring-brand-primary"
                                        />
                                    </div>
                                    <span class="text-xs text-brand-light mt-4">{{ $t('trips.filter_days_unit') }}</span>
                                </div>

                                <!-- Dual slider -->
                                <DualRangeSlider
                                    :min="minDurationBound"
                                    :max="maxDurationBound"
                                    v-model:modelMin="durationMin"
                                    v-model:modelMax="durationMax"
                                />
                            </div>

                            <hr class="border-brand-primary/10" />

                            <!-- Vanaf prijs -->
                            <div v-if="minPriceBound !== maxPriceBound">
                                <p class="text-xs font-semibold text-brand-light uppercase tracking-wide mb-3">
                                    {{ $t('trips.filter_price') }}
                                </p>

                                <!-- Number inputs -->
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="flex-1">
                                        <label class="text-xs text-brand-light block mb-1">Min</label>
                                        <div class="relative">
                                            <span class="absolute left-2 top-1/2 -translate-y-1/2 text-sm text-brand-light">€</span>
                                            <input
                                                type="number"
                                                v-model.number="priceMin"
                                                :min="minPriceBound"
                                                :max="priceMax"
                                                class="w-full text-sm border border-brand-primary/20 rounded-lg pl-5 pr-2 py-1.5 text-brand-text focus:outline-none focus:ring-1 focus:ring-brand-primary"
                                            />
                                        </div>
                                    </div>
                                    <span class="text-brand-light mt-4">—</span>
                                    <div class="flex-1">
                                        <label class="text-xs text-brand-light block mb-1">Max</label>
                                        <div class="relative">
                                            <span class="absolute left-2 top-1/2 -translate-y-1/2 text-sm text-brand-light">€</span>
                                            <input
                                                type="number"
                                                v-model.number="priceMax"
                                                :min="priceMin"
                                                :max="maxPriceBound"
                                                class="w-full text-sm border border-brand-primary/20 rounded-lg pl-5 pr-2 py-1.5 text-brand-text focus:outline-none focus:ring-1 focus:ring-brand-primary"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <!-- Dual slider -->
                                <DualRangeSlider
                                    :min="minPriceBound"
                                    :max="maxPriceBound"
                                    :step="10"
                                    v-model:modelMin="priceMin"
                                    v-model:modelMax="priceMax"
                                />
                            </div>

                        </div>
                    </aside>

                </div>
            </div>
        </section>

    </Layout>
</template>
