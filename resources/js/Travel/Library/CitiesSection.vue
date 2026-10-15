<script setup>
import { computed, ref } from 'vue';
import { Plus } from '@lucide/vue';
import RowActions from '@/Travel/RowActions.vue';
import { destroy } from '@/Travel/destroy.js';
import CityForm from '@/Travel/Library/CityForm.vue';
import LibraryCard from '@/Travel/Library/LibraryCard.vue';
import PharmacyForm from '@/Travel/Library/PharmacyForm.vue';
import { matches } from '@/Travel/Library/search.js';

const props = defineProps({
    cities: {
        type: Array,
        required: true,
    },
});

const search = ref('');
const shown = computed(() =>
    props.cities.filter((city) =>
        matches(search.value, city.name, city.country, ...city.pharmacies.map((pharmacy) => pharmacy.name)),
    ),
);

// What is in a form: { city } or { city, pharmacy }, with null for a new one.
const cityForm = ref(null);
const pharmacyForm = ref(null);
</script>

<template>
    <LibraryCard v-model:search="search" title="Plaatsen en apotheken"
        description="De plaatsen van de stations en accommodaties, met de apotheken die het reisdocument noemt."
        add-label="Plaats toevoegen" :shown="shown.length" :total="cities.length" @add="cityForm = { city: null }">
        <ul class="divide-y divide-gray-100">
            <li v-for="city in shown" :key="city.id" class="px-6 py-4">
                <div class="flex items-start justify-between gap-4">
                    <p class="font-medium text-gray-900">
                        {{ city.name }}
                        <span class="ml-1 rounded bg-gray-100 px-1.5 py-0.5 text-xs font-medium text-gray-500">{{ city.country }}</span>
                    </p>
                    <RowActions :subject="`Plaats ${city.name}`" @edit="cityForm = { city }"
                        @delete="destroy(route('admin.cities.destroy', city.id), `Plaats ${city.name} verwijderen?`)" />
                </div>
                <ul v-if="city.pharmacies.length" class="mt-2 space-y-1 text-sm">
                    <li v-for="pharmacy in city.pharmacies" :key="pharmacy.id" class="flex items-start justify-between gap-4 pl-4">
                        <p class="text-gray-700">
                            {{ pharmacy.name }}<span v-if="pharmacy.address" class="text-gray-500"> · {{ pharmacy.address }}</span>
                        </p>
                        <RowActions :subject="`Apotheek ${pharmacy.name}`" @edit="pharmacyForm = { city, pharmacy }"
                            @delete="destroy(route('admin.pharmacies.destroy', pharmacy.id), `Apotheek ${pharmacy.name} verwijderen?`)" />
                    </li>
                </ul>
                <button type="button"
                    class="mt-2 ml-4 inline-flex items-center gap-1 text-sm font-medium text-brand-primary hover:text-brand-link"
                    @click="pharmacyForm = { city, pharmacy: null }">
                    <Plus class="h-4 w-4" aria-hidden="true" />
                    Apotheek toevoegen
                </button>
            </li>
        </ul>

        <Modal :open="cityForm !== null" @close="cityForm = null">
            <CityForm v-if="cityForm" :city="cityForm.city" @close="cityForm = null" />
        </Modal>
        <Modal :open="pharmacyForm !== null" @close="pharmacyForm = null">
            <PharmacyForm v-if="pharmacyForm" :city="pharmacyForm.city" :pharmacy="pharmacyForm.pharmacy"
                @close="pharmacyForm = null" />
        </Modal>
    </LibraryCard>
</template>
