<script setup>
import { reactive, ref } from 'vue'
import Layout from '@/Pages/Layouts/Layout.vue'
import { Link } from '@inertiajs/vue3';
import IconLink from '@/Pages/Layouts/Components/IconLink.vue';
import { More } from '@/Pages/Icons';
import { Inertia } from '@inertiajs/inertia';

defineProps({
    products: Array,
})
const showMoreOptions = reactive({});

</script>
<template>
    <Layout>
        <div class="pt-[100px] px-4 tablet:px-6 laptop:px-12 desktop:px-16">
            <div class="w-full flex flex-col tablet:flex-row justify-between mb-6">
                <h1 class="text-3xl font-bold mb-4 tablet:mb-0">Producten</h1>
                <IconLink v-tippy="'Voeg een reisproduct toe'" icon="Add" type="info" :href="route('products.create')" />
            </div>
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
                        <tr v-for="(product, index) in products" :key="product.id" class="hover:bg-gray-100 transition">
                            <td class="py-4 px-6 text-center">{{ index + 1 }}</td>
                            <td class="py-4 px-6 text-center">{{ product.name }}</td>
                            <td class="py-4 px-6 text-center">{{ product.countries_list }}</td>
                            <td class="py-4 px-6 text-center">€ {{ product.price }}</td>
                            <td class="py-4 px-6 text-center">{{ product.duration }}</td>
                            <td class="py-4 px-6 text-center space-y-2">
                                <IconLink class="mx-auto" icon="View" :href="route('products.show', { product })"
                                    v-tippy="'Bekijk reisproduct'" />
                                <IconLink class="mx-auto" icon="Edit" :href="route('products.edit', product)"
                                    v-tippy="'Bewerk reisproduct'" />
                                <div class="w-fit mx-auto" v-tippy="`Meer opties`">
                                    <button class="info-button"
                                        @click="showMoreOptions[product.id] = !showMoreOptions[product.id]">
                                        <More class="h-5" />
                                    </button>
                                </div>
                                <div v-if="showMoreOptions[product.id]" class="space-y-2">
                                    <IconLink class="mx-auto" icon="Itinerary" :href="product.itineraries?.length ?
                                        route('products.itineraries.index', product)
                                        : route('products.itineraries.create', product)"
                                        v-tippy="'Bekijk reisplan van deze reis'" />
                                    <IconLink class="mx-auto" type="delete" icon="Delete" :href="route('products.destroy', product)" method="delete"
                                        :showConfirm="true" prompt="Weet je zeker dat je deze reis wilt verwijderen?"
                                        v-tippy="'Verwijder reisproduct!'" />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </Layout>
</template>
