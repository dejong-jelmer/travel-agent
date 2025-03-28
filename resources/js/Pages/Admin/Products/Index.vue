<script setup>
import Layout from '@/Pages/Layouts/Layout.vue'
import { Link } from '@inertiajs/vue3';
import IconLink from '@/Pages/Layouts/Components/IconLink.vue';
import { More } from '@/Pages/Icons';

</script>
<template>
    <Layout>
        <div class="pt-[100px] px-12">
            <div class="w-full flex justify-between mb-4">
                <h1 class="text-2xl font-bold mb-4">Producten</h1>
                <Link :href="route('products.create')" class="form-button">
                    Nieuw Product
                </Link>
            </div>
            <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
                <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-center">#</th>
                    <th class="py-3 px-6 text-center">Product</th>
                    <th class="py-3 px-6 text-center">Land(en)</th>
                    <th class="py-3 px-6 text-center">Prijs</th>
                    <th class="py-3 px-6 text-center">Dagen</th>
                    <th class="py-3 px-6 text-center">Acties</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm divide-y divide-gray-200">
                    <tr
                    v-for="(product, index) in products"
                    :key="product.id"
                    class="hover:bg-gray-100 transition">
                    <td class="py-3 px-6 text-center">{{ index + 1 }}</td>
                    <td class="py-3 px-6 text-center">{{ product.name }}</td>
                    <td class="py-3 px-6 text-center">{{ product.countries?.map(country => country.name).join(", ") }}</td>
                    <td class="py-3 px-6 text-center">€ {{ product.price }}</td>
                    <td class="py-3 px-6 text-center">{{ product.duration }}</td>
                    <td class="py-3 px-6 text-center space-y-2">
                        <IconLink icon="View" :href="route('products.show', { product })" v-tippy="'Bekijk reisproduct'" />
                        <IconLink icon="Edit" :href="route('products.edit', product)"  v-tippy="'Bewerk reisproduct'" />
                            <div class="w-fit mx-auto" v-tippy="`Meer opties`">
                                <button class="info-button" @click="showMoreOptions[product.id] =  !showMoreOptions[product.id]">
                                    <More class="h-5"/>
                                </button>
                            </div>
                            <div v-if="showMoreOptions[product.id]" class="space-y-2">
                                <IconLink icon="Itinerary" :href="product.itineraries?.length ?
                                    route('products.itineraries.index', product)
                                    : route('products.itineraries.create', product)"
                                    v-tippy="'Bekijk reisplan van deze reis'"
                                    />
                                <IconLink icon="Delete" :href="route('products.destroy', product)"
                                    method="delete"
                                    :showConfirm="true"
                                    prompt="Weet je zeker dat je deze reis wilt verwijderen?"
                                    v-tippy="'Verwijder reisproduct!'"
                                />
                        </div>
                    </td>
                    </tr>
                </tbody>
                </table>
            </div>
        </div>
    </Layout>
  </template>

  <script>
export default {
    props: {
        products: Array,
    },
    data() {
        return {
            showMoreOptions: {}
        }
    },
    methods: {
        editProduct(product) {
            this.$inertia.visit(route('products.show', { product }));
        }
    }
    };
  </script>

