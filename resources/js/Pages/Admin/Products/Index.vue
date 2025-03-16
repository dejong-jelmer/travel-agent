<script setup>
import Layout from '@/Pages/Layouts/Layout.vue'
import { Link } from '@inertiajs/vue3';

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
                    <th class="py-3 px-6 text-center">Land</th>
                    <th class="py-3 px-6 text-center">Prijs</th>
                    <th class="py-3 px-6 text-center">Dagen</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm divide-y divide-gray-200">
                    <tr
                    v-for="(product, index) in products"
                    :key="product.id"
                    @click="editProduct(product)"
                    class="hover:bg-gray-100 transition cursor-pointer"
                    >
                    <td class="py-3 px-6 text-center">{{ index + 1 }}</td>
                    <td class="py-3 px-6 text-center">{{ product.name }}</td>
                    <td class="py-3 px-6 text-center">{{ product.country.name }}</td>
                    <td class="py-3 px-6 text-center">€ {{ product.price }}</td>
                    <td class="py-3 px-6 text-center">{{ product.duration }}</td>
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
    methods: {
        editProduct(product) {
            this.$inertia.visit(route('products.show', { id: product.id }));
        }
    }
    };
  </script>

