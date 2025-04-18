<script setup>
import { reactive } from "vue";
import { useForm } from "@inertiajs/vue3";
import Layout from "@/Pages/Layouts/Layout.vue";
import ItineraryForm from "@/Pages/Layouts/Components/ItineraryForm.vue";

const props = defineProps({
    product: Object,
    errors: Object,
});

const form = reactive({
    title: '',
    subtitle: '',
    description: '',
    image: '',
    remark: '',
});

function submit() {
    const submitForm = useForm(form);
    submitForm.post(route("products.itineraries.store", props.product.id), {
        forceFormData: true,
    });
}
</script>

<template>
    <Layout>
        <ItineraryForm :form="form" :errors="errors" @submit="submit" />
    </Layout>
</template>
