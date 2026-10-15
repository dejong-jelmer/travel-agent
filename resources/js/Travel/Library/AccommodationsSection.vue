<script setup>
import { computed, ref } from 'vue';
import LegEditor from '@/Travel/LegEditor.vue';
import RowActions from '@/Travel/RowActions.vue';
import { destroy } from '@/Travel/destroy.js';
import AccommodationForm from '@/Travel/Library/AccommodationForm.vue';
import LibraryCard from '@/Travel/Library/LibraryCard.vue';
import { matches } from '@/Travel/Library/search.js';

const props = defineProps({
    accommodations: {
        type: Array,
        required: true,
    },
    cityOptions: {
        type: Array,
        required: true,
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
});

const library = computed(() => ({
    legTypes: props.legTypeOptions,
    stations: props.stations,
    transitLines: props.transitLines,
}));
const types = computed(() => Object.fromEntries(props.accommodationTypeOptions.map((option) => [option.id, option.name])));

const search = ref('');
const shown = computed(() =>
    props.accommodations.filter((accommodation) => matches(search.value, accommodation.name, accommodation.city)),
);

// The accommodation in the form, as { accommodation }, with null for a new one.
const accommodationForm = ref(null);
</script>

<template>
    <LibraryCard v-model:search="search" title="Accommodaties"
        description="De hotels en andere adressen, met de route van het station naar de deur." add-label="Accommodatie toevoegen"
        :shown="shown.length" :total="accommodations.length" @add="accommodationForm = { accommodation: null }">
        <ul class="divide-y divide-gray-100">
            <li v-for="accommodation in shown" :key="accommodation.id" class="grid grid-cols-1 laptop:grid-cols-2 gap-6 px-6 py-5">
                <div class="min-w-0 space-y-2 text-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-base font-semibold text-gray-900">{{ accommodation.name }}</p>
                            <p class="text-gray-500">{{ types[accommodation.type] }} in {{ accommodation.city }}</p>
                        </div>
                        <RowActions :subject="`Accommodatie ${accommodation.name}`"
                            @edit="accommodationForm = { accommodation }"
                            @delete="destroy(route('admin.accommodations.destroy', accommodation.id), `Accommodatie ${accommodation.name} met haar aankomstroute verwijderen?`)" />
                    </div>
                    <p v-if="accommodation.address" class="text-gray-700">{{ accommodation.address }}</p>
                    <p v-if="accommodation.phone" class="text-gray-700">{{ accommodation.phone }}</p>
                    <p v-if="accommodation.check_in || accommodation.check_out" class="text-gray-700">
                        Inchecken vanaf {{ accommodation.check_in ?? '?' }}, uitchecken voor {{ accommodation.check_out ?? '?' }}
                    </p>
                </div>
                <div class="min-w-0">
                    <h3 class="mb-3 text-sm font-medium text-gray-700">Aankomstroute</h3>
                    <LegEditor :legs="accommodation.arrival_route" :owner="{ type: 'accommodation', id: accommodation.id }"
                        :library="library" />
                    <p v-if="accommodation.walk_directions" class="mt-3 text-sm text-gray-600 whitespace-pre-line">
                        {{ accommodation.walk_directions }}
                    </p>
                </div>
            </li>
        </ul>

        <Modal :open="accommodationForm !== null" @close="accommodationForm = null">
            <AccommodationForm v-if="accommodationForm" :accommodation="accommodationForm.accommodation"
                :city-options="cityOptions" :accommodation-type-options="accommodationTypeOptions"
                @close="accommodationForm = null" />
        </Modal>
    </LibraryCard>
</template>
