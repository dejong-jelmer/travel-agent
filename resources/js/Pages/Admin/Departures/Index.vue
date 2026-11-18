<script setup>
import { Link } from '@inertiajs/vue3';
import DepartureList from '@/Departures/DepartureList.vue';

// The departures of all trips: what needs attention first, then what is coming up, then what is over.
defineProps({
    attention: { type: Array, required: true },
    upcoming: { type: Array, required: true },
    past: { type: Array, required: true },
});
</script>

<template>
    <Admin>
        <div class="max-w-wide mx-auto space-y-10">
            <div>
                <h1 class="text-3xl font-bold text-gray-700">{{ $t('admin.departures.title') }}</h1>
                <p class="mt-1 text-sm text-gray-700/50">{{ $t('admin.departures.overview.subtitle') }}</p>
            </div>

            <div v-if="attention.length + upcoming.length + past.length === 0"
                class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 space-y-3">
                <p class="text-brand-text">{{ $t('admin.departures.overview.empty') }}</p>
                <Link :href="route('admin.trips.index')" class="text-sm text-brand-link underline">
                    {{ $t('admin.departures.overview.to_trips') }}
                </Link>
            </div>

            <section v-if="attention.length" data-testid="section-attention" class="space-y-4">
                <h2 class="text-xl font-semibold text-gray-700">
                    {{ $t('admin.departures.overview.attention') }}
                    <span class="ml-2 inline-flex items-center rounded-full bg-status-warning/20 px-2.5 py-0.5 text-sm font-semibold text-brand-text">
                        {{ attention.length }}
                    </span>
                </h2>
                <DepartureList :departures="attention" :show-trip="true" />
            </section>

            <section v-if="attention.length + upcoming.length" data-testid="section-upcoming" class="space-y-4">
                <h2 class="text-xl font-semibold text-gray-700">{{ $t('admin.departures.overview.upcoming') }}</h2>
                <p v-if="upcoming.length === 0" class="text-sm text-gray-700/50">
                    {{ $t('admin.departures.overview.upcoming_empty') }}
                </p>
                <DepartureList :departures="upcoming" :show-trip="true" />
            </section>

            <section v-if="past.length" data-testid="section-past" class="space-y-4">
                <h2 class="text-xl font-semibold text-gray-700">{{ $t('admin.departures.overview.past') }}</h2>
                <DepartureList :departures="past" :show-trip="true" />
            </section>
        </div>
    </Admin>
</template>
