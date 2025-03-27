<script setup>
import Layout from "@/Pages/Layouts/Layout.vue";
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    product: Object,
    required: true,
});
</script>

<template>
    <Layout>
        <div class="space-y-4">
            <div class="flex justify-end">
                <Link v-if="product.itineraries?.length" class="form-button" as="button" :href="route('products.itineraries.index', product)">Bekijk itinerary</Link>
                <Link v-else class="form-button" as="button" :href="route('products.itineraries.create', product)">Maak een itinerary</Link>
            </div>
            <div class="bg-white p-6 rounded-lg shadow grid grid-cols-2 gap-y-4">
                <h2 class="text-2xl font-bold mb-4">{{ product.name }}</h2>

                <p><strong>Beschrijving:</strong> {{ product.description }}</p>
                <p><strong>Prijs:</strong> €{{ product.price }}</p>
                <p><strong>Duur:</strong> {{ product.duration }} dagen</p>
                <p><strong>Land:</strong> {{ product.countries[0].name }}</p>

                <div>
                    <strong>Hoofdafbeelding:</strong>
                    <img :src="product.image" alt="Product image" class="w-32 h-32 object-cover rounded mt-2" />
                </div>

                <div>
                    <strong>Andere afbeeldingen:</strong>
                    <div class="flex space-x-4">
                        <template v-for="(image, index) in product.images" :key="index">
                            <img :src="image.path" :alt="`Product image ${index}`" class="w-32 h-32 object-cover rounded mt-2" />
                        </template>
                    </div>
                </div>

                <p><strong>Actief:</strong> {{ product.active ? "Ja" : "Nee" }}</p>
                <p><strong>Uitgelicht:</strong> {{ product.featured ? "Ja" : "Nee" }}</p>
                <Link :href="route('products.edit', { id: product.id })" method="get" as="button" class="form-button">
                    Bewerk Product
                </Link>
            </div>
        </div>
    </Layout>
</template>
