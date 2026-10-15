<script setup>
import { computed, ref } from 'vue';
import RowActions from '@/Travel/RowActions.vue';
import { destroy } from '@/Travel/destroy.js';
import LibraryCard from '@/Travel/Library/LibraryCard.vue';
import StationForm from '@/Travel/Library/StationForm.vue';
import { matches } from '@/Travel/Library/search.js';

const props = defineProps({
    stations: {
        type: Array,
        required: true,
    },
    cityOptions: {
        type: Array,
        required: true,
    },
});

const search = ref('');
const shown = computed(() => props.stations.filter((station) => matches(search.value, station.name, station.city)));

// The station in the form, as { station }, with null for a new one.
const stationForm = ref(null);
</script>

<template>
    <LibraryCard v-model:search="search" title="Stations"
        description="De stations en haltes waar etappes vertrekken en aankomen." add-label="Station toevoegen"
        :shown="shown.length" :total="stations.length" @add="stationForm = { station: null }">
        <ul class="divide-y divide-gray-100">
            <li v-for="station in shown" :key="station.id" class="flex items-start justify-between gap-4 px-6 py-3 text-sm">
                <div class="min-w-0">
                    <p class="font-medium text-gray-900">{{ station.name }}</p>
                    <p class="text-gray-500">
                        {{ station.city }}
                        <span v-if="station.latitude && station.longitude" class="tabular-nums">
                            · {{ station.latitude }}, {{ station.longitude }}
                        </span>
                    </p>
                </div>
                <RowActions :subject="`Station ${station.name}`" @edit="stationForm = { station }"
                    @delete="destroy(route('admin.stations.destroy', station.id), `Station ${station.name} verwijderen?`)" />
            </li>
        </ul>

        <Modal :open="stationForm !== null" @close="stationForm = null">
            <StationForm v-if="stationForm" :station="stationForm.station" :city-options="cityOptions"
                @close="stationForm = null" />
        </Modal>
    </LibraryCard>
</template>
