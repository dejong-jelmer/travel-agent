<script setup>
import { router } from "@inertiajs/vue3";
import { defineProps } from "vue";
import Layout from "../../Layouts/Layout.vue";

const props = defineProps({
    product: Object,
    required: true,
});

const editProduct = () => {
    router.visit(route("products.edit", props.product.id), { preserveScroll: true });
};
</script>

<template>
    <Layout>
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-2xl font-bold mb-4">{{ product.name }}</h2>

            <p><strong>Beschrijving:</strong> {{ product.description }}</p>
            <p><strong>Prijs:</strong> €{{ product.price }}</p>
            <p><strong>Duur:</strong> {{ product.duration }} minuten</p>

            <div class="my-4">
                <strong>Hoofdafbeelding:</strong>
                <img :src="product.image" alt="Product Image" class="w-32 h-32 object-cover rounded mt-2" />
            </div>

            <div v-if="product.images.length">
                <strong>Extra Afbeeldingen:</strong>
                <div class="flex space-x-2 mt-2">
                    <img v-for="(img, index) in product.images" :key="index" :src="img" alt="Extra Image" class="w-16 h-16 object-cover rounded" />
                </div>
            </div>

            <p><strong>Actief:</strong> {{ product.active ? "Ja" : "Nee" }}</p>
            <p><strong>Uitgelicht:</strong> {{ product.featured ? "Ja" : "Nee" }}</p>

            <button @click="editProduct" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">
                Bewerken
            </button>
        </div>
    </Layout>
</template>
<!-- <script>
export default {
    props: {
        product: Object,
        required: true,
    },
    methods: {
        editProduct() {
                console.log("Edit product: ", route("products.edit", props.product.id));
                // router.visit(route("products.edit", product.id), { preserveScroll: true });
        }
    },
};
</script> -->
