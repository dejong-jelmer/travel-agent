<script setup>
import { EllipsisVertical } from 'lucide-vue-next';
import { reactive } from 'vue';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue';

defineProps({
    trips: Object,
});
const showActions = reactive({});

function toggleActions(tripId) {
    // Sluit alle andere dropdowns
    Object.keys(showActions).forEach(key => {
        if (key != tripId) {
            showActions[key] = false;
        }
    });
    // Toggle de geklikte dropdown
    showActions[tripId] = !showActions[tripId];
}

</script>
<template>
    <Admin :links="trips.links">
        <div class="w-full flex flex-col tablet:flex-row justify-between mb-6">
            <h1 class="text-3xl font-bold mb-4 tablet:mb-0">Reizen</h1>
            <IconLink v-tippy="'Maak een reis aan'" icon="Plus" type="info" :href="route('admin.trips.create')" />
        </div>
        <template v-if="trips.data.length > 0">
            <div class="overflow-x-auto bg-white shadow-lg rounded-2xl">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                            <th class="py-4 px-6 text-center">#</th>
                            <th class="py-4 px-6 text-center">Afbeelding</th>
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
                            <td class="py-4 px-6 flex justify-center items-center">
                                <Thumbnail :imageUrl="trip.hero_image?.public_url || ''" :alt="trip.name" />
                            </td>
                            <td class="py-4 px-6 text-center">{{ trip.name }}</td>
                            <td class="py-4 px-6 text-center">{{ trip.formatted_countries }}</td>
                            <td class="py-4 px-6 text-center">€ {{ trip.price }}</td>
                            <td class="py-4 px-6 text-center">{{ trip.duration }}</td>
                            <td class="py-4 px-6 text-center space-y-2">
                                <div class="relative w-fit mx-auto" v-tippy="`Opties`">
                                    <Menu as="div" class="relative w-fit mx-auto">
                                        <MenuButton class="info-button">
                                            <EllipsisVertical class="h-5" />
                                        </MenuButton>

                                        <MenuItems
                                            class="absolute z-10 space-y-2 bg-white mt-2 -left-2 p-2 border border-slate-700 rounded-lg">
                                            <MenuItem v-slot="{ active }">
                                            <IconLink :class="{ 'bg-gray-100': active }" icon="Eye"
                                                :href="route('admin.trips.show', { trip })" />
                                            </MenuItem>
                                            <MenuItem v-slot="{ active }">
                                            <IconLink :class="{ 'bg-gray-100': active }" icon="Pencil"
                                                :href="route('admin.trips.edit', trip)" />
                                            </MenuItem>
                                            <MenuItem v-slot="{ active }">
                                            <IconLink :class="{ 'bg-gray-100': active }" icon="Route" :href="trip.itineraries?.length ?
                                                route('admin.trips.itineraries.index', trip)
                                                : route('admin.trips.itineraries.create', trip)"
                                                v-tippy="'Bekijk reisplan van deze reis'" />
                                            </MenuItem>
                                            <MenuItem v-slot="{ active }">
                                            <IconLink :class="{ 'bg-gray-100': active }" type="delete" icon="Trash2"
                                                :href="route('admin.trips.destroy', trip)" method="delete"
                                                :showConfirm="true"
                                                prompt="Weet je zeker dat je deze reis wilt verwijderen?"
                                                v-tippy="'Verwijder reis!'" />
                                            </MenuItem>
                                        </MenuItems>
                                    </Menu>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>
        <template v-else>
            <div class="p-5">
                <p>Je hebt nog geen reizen aangemaakt.</p>
            </div>
        </template>
    </Admin>
</template>
