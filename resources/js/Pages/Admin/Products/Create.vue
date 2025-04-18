<script setup>
import { reactive } from 'vue'
import { useForm } from "@inertiajs/vue3";
import IconLink from "@/Pages/Layouts/Components/IconLink.vue";
import Layout from "@/Pages/Layouts/Layout.vue";
import ProductForm from "@/Pages/Layouts/Components/ProductForm.vue";

const props = defineProps({
    errors: Object,
    countries: Object,
});

const form = reactive({
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
    const submitForm = useForm(form);
    submitForm.post(route("products.store"), { forceFormData: true });
}
</script>

<template>
    <Layout>
        <ProductForm
            :form="form"
            :errors="errors"
            :countries="countries"
            @submit="submit"
        />
    </Layout>
</template>
