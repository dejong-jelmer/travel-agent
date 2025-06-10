<script setup>
import AdminLayout from "@/Pages/Layouts/AdminLayout.vue";
// import SortableBlocks from "@/Pages/Layouts/Components/SortableBlocks.vue";
// import Itinerary from "@/Pages/Layouts/Components/Itinerary.vue";
import { usePage } from "@inertiajs/vue3";
import IconLink from "@/Pages/Layouts/Components/IconLink.vue";
// import axios from '@/axios'
const user = usePage().props.auth?.user ?? {};
const props = defineProps({
    product: Object,
    countries: Array,
});
</script>
<template>
    <AdminLayout>
        <div class="">
            <div class="w-full flex flex-col tablet:flex-row justify-between mb-6">
                <h1 class="text-3xl font-bold mb-4 tablet:mb-0">Landen</h1>
                <IconLink v-tippy="'Voeg een reisproduct toe'" icon="Add" type="info"
                    :href="route('countries.create')" />
            </div>
            <div class="overflow-x-auto bg-white shadow-lg rounded-2xl">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                            <th class="py-4 px-6 text-center">#</th>
                            <th class="py-4 px-6 text-center">Land</th>
                               <th class="py-4 px-6 text-center">Acties</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm divide-y divide-gray-200">
                        <tr v-for="(country, index) in countries" :key="country.id" class="hover:bg-gray-100 transition">
                            <td class="py-4 px-6 text-center">{{ index + 1 }}</td>
                            <td class="py-4 px-6 text-center">{{ country.name }}</td>
                             <td class="py-4 px-6 text-center space-y-2">
                                <IconLink class="mx-auto" type="delete" icon="Delete"
                                        :href="route('countries.destroy', country)" method="delete" :showConfirm="true"
                                        prompt="Weet je zeker dat je dit land wilt verwijderen?"
                                        v-tippy="'Verwijder land!'" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
