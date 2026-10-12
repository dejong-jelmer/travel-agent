<script setup>
import { computed, ref, watch } from 'vue';
import { ArrowLeft, BedDouble, Library, Pill, Plus, Route } from '@lucide/vue';
import DocumentsCard from '@/Travel/DocumentsCard.vue';
import JourneyForm from '@/Travel/JourneyForm.vue';
import LegEditor from '@/Travel/LegEditor.vue';
import RowActions from '@/Travel/RowActions.vue';
import StayForm from '@/Travel/StayForm.vue';
import { destroy } from '@/Travel/destroy.js';
import { formatDate, formatShortDate } from '@/Travel/format.js';
import { saveOrder } from '@/Travel/saveOrder.js';
import { moveItem, useSortable } from '@/Travel/useSortable.js';

const props = defineProps({
    booking: {
        type: Object,
        required: true,
    },
    journeys: {
        type: Array,
        required: true,
    },
    stays: {
        type: Array,
        required: true,
    },
    cities: {
        type: Array,
        required: true,
    },
    shareLink: {
        type: Object,
        default: null,
    },
    legTypeOptions: {
        type: Array,
        required: true,
    },
    accommodationTypeOptions: {
        type: Array,
        required: true,
    },
    stations: {
        type: Array,
        required: true,
    },
    transitLines: {
        type: Array,
        required: true,
    },
    accommodations: {
        type: Array,
        required: true,
    },
});

const library = computed(() => ({
    legTypes: props.legTypeOptions,
    stations: props.stations,
    transitLines: props.transitLines,
}));
const accommodationTypes = computed(() =>
    Object.fromEntries(props.accommodationTypeOptions.map((option) => [option.id, option.name])),
);

const nights = (count) => (count === 1 ? '1 nacht' : `${count} nachten`);

// The lists keep their own order while dragging; a save reloads the page, and they follow the props again.
const journeys = ref([...props.journeys]);
const stays = ref([...props.stays]);
watch(() => props.journeys, (value) => {
    journeys.value = [...value];
});
watch(() => props.stays, (value) => {
    stays.value = [...value];
});

const journeyList = ref(null);
useSortable(journeyList, '.journey-handle', (from, to) => {
    moveItem(journeys.value, from, to);
    saveOrder(route('admin.bookings.journeys.order', props.booking.id), journeys.value);
});

const stayList = ref(null);
useSortable(stayList, '.stay-handle', (from, to) => {
    moveItem(stays.value, from, to);
    saveOrder(route('admin.bookings.stays.order', props.booking.id), stays.value);
});

// The journey or stay in the form, as { journey } or { stay }; null for a new one.
const journeyForm = ref(null);
const stayForm = ref(null);

const deleteJourney = (journey) =>
    destroy(route('admin.journeys.destroy', journey.id), `Reis "${journey.label}" met al zijn etappes verwijderen?`);

const deleteStay = (stay) =>
    destroy(route('admin.stays.destroy', stay.id), `Verblijf in ${stay.accommodation.name} verwijderen? De accommodatie zelf blijft bestaan.`);
</script>

<template>
    <Admin>
        <div class="max-w-wide mx-auto">
            <div class="grid grid-cols-1 laptop:grid-cols-3 gap-8">
                <!-- Header Section -->
                <div class="laptop:col-span-3 bg-white py-10 border-b border-gray-200">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-700">Reisplan</h1>
                            <p class="mt-2 text-sm text-gray-700/50">
                                Boeking {{ booking.reference }}
                                · {{ formatShortDate(booking.departure_date) }} – {{ formatShortDate(booking.return_date) }}
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-6">
                            <DefaultLink :href="route('admin.travel-library')"
                                class="inline-flex items-center gap-2 text-sm text-gray-700 hover:text-brand-link">
                                <Library class="h-4 w-4" aria-hidden="true" />
                                Reisbibliotheek
                            </DefaultLink>
                            <DefaultLink :href="route('admin.bookings.show', booking.id)"
                                class="inline-flex items-center gap-2 text-sm text-gray-700 hover:text-brand-link">
                                <ArrowLeft class="h-4 w-4" aria-hidden="true" />
                                Terug naar de boeking
                            </DefaultLink>
                        </div>
                    </div>
                </div>

                <div class="laptop:col-span-2 space-y-8">
                    <!-- Journeys Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="flex items-start justify-between gap-4 border-b border-gray-200 bg-white px-6 py-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <Route class="h-4 w-4 text-gray-400 flex-shrink-0" />
                                    <h2 class="text-lg font-semibold text-gray-700">Reizen</h2>
                                </div>
                                <p class="mt-1 text-sm text-gray-700/30 pl-6">De reisdagen met hun etappes, in volgorde.</p>
                            </div>
                            <button type="button"
                                class="inline-flex items-center gap-1 text-sm font-medium text-brand-primary hover:text-brand-link"
                                @click="journeyForm = { journey: null }">
                                <Plus class="h-4 w-4" aria-hidden="true" />
                                Reis toevoegen
                            </button>
                        </div>
                        <p v-if="!journeys.length" class="p-6 text-sm text-gray-500 italic">Nog geen reizen.</p>
                        <ol ref="journeyList" class="divide-y divide-gray-100">
                            <li v-for="journey in journeys" :key="journey.id" class="p-6">
                                <header class="flex items-start justify-between gap-4 mb-4">
                                    <div class="min-w-0">
                                        <h3 class="font-semibold text-gray-900">{{ journey.label }}</h3>
                                        <p v-if="journey.date" class="text-sm text-gray-500">{{ formatDate(journey.date) }}</p>
                                    </div>
                                    <RowActions handle="journey-handle" :subject="`Reis ${journey.label}`"
                                        @edit="journeyForm = { journey }" @delete="deleteJourney(journey)" />
                                </header>
                                <LegEditor :legs="journey.legs" :owner="{ type: 'journey', id: journey.id }"
                                    :date="journey.date" :library="library" />
                            </li>
                        </ol>
                    </section>
                </div>

                <div class="laptop:col-start-3 space-y-8">
                    <DocumentsCard :booking="booking" :share-link="shareLink" />

                    <!-- Stays Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="flex items-start justify-between gap-4 border-b border-gray-200 bg-white px-6 py-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <BedDouble class="h-4 w-4 text-gray-400 flex-shrink-0" />
                                    <h2 class="text-lg font-semibold text-gray-700">Verblijven</h2>
                                </div>
                                <p class="mt-1 text-sm text-gray-700/30 pl-6">De overnachtingen, met de route van het station naar de deur.</p>
                            </div>
                            <button type="button"
                                class="inline-flex items-center gap-1 text-sm font-medium text-brand-primary hover:text-brand-link whitespace-nowrap"
                                @click="stayForm = { stay: null }">
                                <Plus class="h-4 w-4" aria-hidden="true" />
                                Verblijf toevoegen
                            </button>
                        </div>
                        <p v-if="!stays.length" class="p-6 text-sm text-gray-500 italic">Nog geen verblijven.</p>
                        <ol ref="stayList" class="divide-y divide-gray-100">
                            <li v-for="stay in stays" :key="stay.id" class="p-6 space-y-4">
                                <header class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <h3 class="font-semibold text-gray-900">{{ stay.accommodation.name }}</h3>
                                        <p class="text-sm text-gray-500">
                                            {{ accommodationTypes[stay.accommodation.type] }} in {{ stay.accommodation.city }}
                                        </p>
                                    </div>
                                    <RowActions handle="stay-handle" :subject="`Verblijf ${stay.accommodation.name}`"
                                        @edit="stayForm = { stay }" @delete="deleteStay(stay)" />
                                </header>
                                <dl class="grid grid-cols-3 gap-x-4 gap-y-2 text-sm">
                                    <dt class="font-medium text-gray-500">Verblijf</dt>
                                    <dd class="col-span-2 text-gray-900">
                                        {{ formatShortDate(stay.arrival) }} – {{ formatShortDate(stay.departure) }}
                                        <span v-if="stay.nights !== null" class="text-gray-500">· {{ nights(stay.nights) }}</span>
                                    </dd>
                                    <template v-if="stay.accommodation.check_in || stay.accommodation.check_out">
                                        <dt class="font-medium text-gray-500">In- en uitchecken</dt>
                                        <dd class="col-span-2 text-gray-900">
                                            {{ stay.accommodation.check_in ?? '?' }} / {{ stay.accommodation.check_out ?? '?' }}
                                        </dd>
                                    </template>
                                    <template v-if="stay.accommodation.address">
                                        <dt class="font-medium text-gray-500">Adres</dt>
                                        <dd class="col-span-2 text-gray-900">{{ stay.accommodation.address }}</dd>
                                    </template>
                                    <template v-if="stay.accommodation.phone">
                                        <dt class="font-medium text-gray-500">Telefoon</dt>
                                        <dd class="col-span-2 text-gray-900">{{ stay.accommodation.phone }}</dd>
                                    </template>
                                </dl>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700">Aankomstroute</h4>
                                    <p class="mb-3 text-xs text-gray-500">Geldt voor elke boeking bij deze accommodatie.</p>
                                    <LegEditor :legs="stay.accommodation.arrival_route"
                                        :owner="{ type: 'accommodation', id: stay.accommodation.id }" :library="library" />
                                    <p v-if="stay.accommodation.walk_directions"
                                        class="mt-3 text-sm text-gray-600 whitespace-pre-line">{{ stay.accommodation.walk_directions }}</p>
                                </div>
                            </li>
                        </ol>
                    </section>

                    <!-- Pharmacies Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <div class="flex items-center gap-2">
                                <Pill class="h-4 w-4 text-gray-400 flex-shrink-0" />
                                <h2 class="text-lg font-semibold text-gray-700">Apotheken</h2>
                            </div>
                            <p class="mt-1 text-sm text-gray-700/30 pl-6">Per plaats op de route.</p>
                        </div>
                        <p v-if="!cities.length" class="p-6 text-sm text-gray-500 italic">Nog geen plaatsen.</p>
                        <ul v-else class="divide-y divide-gray-100">
                            <li v-for="city in cities" :key="city.id" class="px-6 py-3 text-sm">
                                <p class="font-medium text-gray-900">{{ city.name }}</p>
                                <p v-for="pharmacy in city.pharmacies" :key="pharmacy.id" class="text-gray-700">
                                    {{ pharmacy.name }}<span v-if="pharmacy.address" class="text-gray-500"> · {{ pharmacy.address }}</span>
                                </p>
                                <p v-if="!city.pharmacies.length" class="text-gray-400 italic">Geen apotheek</p>
                            </li>
                        </ul>
                    </section>
                </div>
            </div>
        </div>

        <Modal :open="journeyForm !== null" @close="journeyForm = null">
            <JourneyForm v-if="journeyForm" :booking-id="booking.id" :journey="journeyForm.journey" @close="journeyForm = null" />
        </Modal>
        <Modal :open="stayForm !== null" @close="stayForm = null">
            <StayForm v-if="stayForm" :booking-id="booking.id" :stay="stayForm.stay"
                :defaults="{ arrival: stays.at(-1)?.departure ?? '' }" :accommodations="accommodations"
                @close="stayForm = null" />
        </Modal>
    </Admin>
</template>
