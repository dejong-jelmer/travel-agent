<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
   countries: Object,
});

const form = useForm({
    name: "",
    slug: "",
    description: "",
    price: "",
    duration: "",
    countries: [],
    featuredImage: null,
    images: [],
    active: false,
    featured: false,
});


function submit() {
    form.post(route("admin.products.store"), { forceFormData: true });
}
</script>

<template>
    <Admin>
        <ProductForm :form="form" :countries="countries" @submit="submit" />
    </Admin>
</template>
