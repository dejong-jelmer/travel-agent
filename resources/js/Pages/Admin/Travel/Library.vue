<script setup>
import { Link } from '@inertiajs/vue3';
import AccommodationsSection from '@/Travel/Library/AccommodationsSection.vue';
import CitiesSection from '@/Travel/Library/CitiesSection.vue';
import LinesSection from '@/Travel/Library/LinesSection.vue';
import StationsSection from '@/Travel/Library/StationsSection.vue';

// Each section comes with its own props; the others stay undefined.
defineProps({
    section: {
        type: String,
        required: true,
    },
    cities: Array,
    cityOptions: Array,
    stations: Array,
    transitLines: Array,
    transitModeOptions: Array,
    accommodations: Array,
    legTypeOptions: Array,
    accommodationTypeOptions: Array,
});

const sections = [
    { id: 'cities', label: 'Plaatsen en apotheken' },
    { id: 'stations', label: 'Stations' },
    { id: 'lines', label: 'Lijnen' },
    { id: 'accommodations', label: 'Accommodaties' },
];
</script>

<template>
    <Admin>
        <div class="max-w-wide mx-auto space-y-8">
            <div class="bg-white pt-10 border-b border-gray-200">
                <h1 class="text-3xl font-bold text-gray-700">Reisbibliotheek</h1>
                <p class="mt-2 text-sm text-gray-700/50">
                    Waar de reisplannen uit kiezen. Een wijziging geldt voor elke reis die het gebruikt.
                </p>
                <nav class="mt-6 -mb-px flex flex-wrap gap-x-6" aria-label="Onderdelen van de bibliotheek">
                    <Link v-for="item in sections" :key="item.id" :href="route('admin.travel-library', item.id)"
                        :aria-current="item.id === section ? 'page' : undefined"
                        :class="['border-b-2 pb-3 text-sm font-medium transition-colors', item.id === section
                            ? 'border-brand-primary text-brand-primary'
                            : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']">
                        {{ item.label }}
                    </Link>
                </nav>
            </div>

            <CitiesSection v-if="section === 'cities'" :cities="cities" />
            <StationsSection v-else-if="section === 'stations'" :stations="stations" :city-options="cityOptions" />
            <LinesSection v-else-if="section === 'lines'" :transit-lines="transitLines"
                :transit-mode-options="transitModeOptions" />
            <AccommodationsSection v-else :accommodations="accommodations" :city-options="cityOptions"
                :leg-type-options="legTypeOptions" :accommodation-type-options="accommodationTypeOptions"
                :stations="stations" :transit-lines="transitLines" />
        </div>
    </Admin>
</template>
