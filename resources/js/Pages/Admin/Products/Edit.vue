<script setup>
import { ref } from 'vue';
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    product: Object,
    countries: Object,
});

const form = useForm({
    ...props.product,
    countries: props.product.countries?.map(country => country.id) ?? [],
    featuredImage: props.product.featured_image?.path ?? null,
    images: props.product.image_urls ?? [],
});

// Counter for image uploader initialization (featuredImage + images = 2)
const initCounter = ref(2);

function handleImageInitialized() {
    initCounter.value--;
    if (initCounter.value === 0) {
        // All uploaders have finished initialization
        form.defaults({
            ...form.data(),
        });
    }
}

function submit() {
    form.post(route("admin.products.update", props.product), { forceFormData: true });
}

</script>

<template>
    <Admin>
        <ProductForm :form="form" :countries="countries" @submit="submit" @initialized="handleImageInitialized" />
    </Admin>
</template>
