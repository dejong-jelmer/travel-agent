<script setup>
import { reactive } from "vue";
import { useForm } from "@inertiajs/vue3";
import Layout from "@/Pages/Layouts/Layout.vue";
import ItineraryForm from '@/Pages/Layouts/Components/ItineraryForm.vue';

const props = defineProps({
    itinerary: Object,
    errors: Object,
});

const form = reactive({
    title: props.itinerary.title,
    subtitle: props.itinerary.subtitle,
    description: props.itinerary.description,
    image: props.itinerary.image?.path,
    remark: props.itinerary.remark,
});

function submit() {
    const submitForm = useForm(form);
    submitForm.post(route("itineraries.update", props.itinerary.id), { forceFormData: true });
}
</script>

<template>
    <Layout>
        <ItineraryForm :form="form" :errors="errors" @submit="submit" />
    </Layout>
</template>
