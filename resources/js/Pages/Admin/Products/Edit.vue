<script setup>
import { useForm } from "@inertiajs/vue3";
import Layout from "../../Layouts/Layout.vue";
import { defineProps } from "vue";
import { ref } from "vue";
const props = defineProps({
    product: Object,
});

const form = useForm({
    name: props.product.name,
    description: props.product.description,
    price:  props.product.price,
    duration: props.product.duration,
    image: props.product.image,
    images: props.product.images,
    active: ref(props.product.active),
    featured: ref(props.product.featured,)
});

const submit = () => {
    form.put(route("products.update", props.product.id));
};

</script>

<template>
    <Layout>
        <form @submit.prevent="submit" class="space-y-4 bg-white p-6 rounded-lg shadow">
            <div>
                <label class="block text-sm font-medium">Naam</label>
                <input v-model="form.name" type="text" class="w-full border p-2 rounded" required />
            </div>

            <div>
                <label class="block text-sm font-medium">Beschrijving</label>
                <textarea v-model="form.description" class="w-full border p-2 rounded" required></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium">Prijs (€)</label>
                <input v-model="form.price" type="number" step="0.01" class="w-full border p-2 rounded" required />
            </div>

            <div>
                <label class="block text-sm font-medium">Duur (dagen)</label>
                <input v-model="form.duration" type="number" class="w-full border p-2 rounded" required />
            </div>

            <div>
                <label class="block text-sm font-medium">Hoofdafbeelding (URL)</label>
                <input v-model="form.image" type="text" class="w-full border p-2 rounded" required />
            </div>

            <div>
                <label class="block text-sm font-medium">Extra Afbeeldingen (JSON)</label>
                <input v-model="form.images" type="text" class="w-full border p-2 rounded" placeholder='["img1.jpg", "img2.jpg"]' />
            </div>


            <div class="flex items-center mb-4">
                <input v-model="form.active" id="active" type="checkbox" value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                <label for="active" class="ms-2 text-sm font-medium text-gray-900">Actief</label>
            </div>
            <div class="flex items-center">
                <input v-model="form.featured" id="featured" type="checkbox" value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                <label for="featured" class="ms-2 text-sm font-medium text-gray-900">Uitgelicht</label>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Opslaan</button>
        </form>
    </Layout>
</template>
