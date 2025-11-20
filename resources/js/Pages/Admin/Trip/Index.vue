<script setup>
import { reactive } from 'vue'

defineProps({
    trips: Object,
})
const showMoreOptions = reactive({});

</script>
<template>
    <Admin :links="trips.links">
        <template v-if="trips.data.length > 0">
            <div class="overflow-x-auto bg-white shadow-lg rounded-2xl">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                            <th class="py-4 px-6 text-center">#</th>
                            <th class="py-4 px-6 text-center">Product</th>
                            <th class="py-4 px-6 text-center">Land(en)</th>
                            <th class="py-4 px-6 text-center">Prijs</th>
                            <th class="py-4 px-6 text-center">Dagen</th>
                            <th class="py-4 px-6 text-center">Acties</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm divide-y divide-gray-200">
                        <tr v-for="(trip, index) in trips.data" :key="index" class="hover:bg-gray-100 transition">
                            <td class="py-4 px-6 text-center">{{ trip.id }}</td>
                            <td class="py-4 px-6 text-center">{{ trip.name }}</td>
                            <td class="py-4 px-6 text-center">{{ trip.countries_list }}</td>
                            <td class="py-4 px-6 text-center">€ {{ trip.price }}</td>
                            <td class="py-4 px-6 text-center">{{ trip.duration }}</td>
                            <td class="py-4 px-6 text-center space-y-2">
                                <IconLink class="mx-auto" icon="Eye" :href="route('admin.trips.show', { trip })"
                                    v-tippy="'Bekijk reis'" />
                                <IconLink class="mx-auto" icon="Pencil" :href="route('admin.trips.edit', trip)"
                                    v-tippy="'Bewerk reis'" />
                                <div class="w-fit mx-auto" v-tippy="`Meer opties`">
                                    <button class="info-button"
                                        @click="showMoreOptions[trip.id] = !showMoreOptions[trip.id]">
                                        <More class="h-5" />
                                    </button>
                                </div>
                                <div v-if="showMoreOptions[trip.id]" class="space-y-2">
                                    <IconLink class="mx-auto" icon="Route" :href="trip.itineraries?.length ?
                                        route('admin.trips.itineraries.index', trip)
                                        : route('admin.trips.itineraries.create', trip)"
                                        v-tippy="'Bekijk reisplan van deze reis'" />
                                    <IconLink class="mx-auto" type="delete" icon="Trash2"
                                        :href="route('admin.trips.destroy', trip)" method="delete"
                                        :showConfirm="true" prompt="Weet je zeker dat je deze reis wilt verwijderen?"
                                        v-tippy="'Verwijder reis!'" />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>
        <template v-else>
            <div class="p-5">
                <p>Er zijn nog geen reizen klik <DefaultLink :href="route('admin.trips.create')">hier</DefaultLink> om een reis toe te voegen.
                </p>
            </div>
        </template>
    </Admin>
</template>
