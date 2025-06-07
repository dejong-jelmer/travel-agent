<script setup>
import { reactive } from "vue";
import { useForm } from "@inertiajs/vue3";
import AdminLayout from "@/Pages/Layouts/AdminLayout.vue";
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
    <AdminLayout>
        <div class="bg-white rounded-lg shadow p-4 tablet:p-6 laptop:p-10 desktop:p-12">
            <ItineraryForm :form="form" :errors="errors" @submit="submit" />
        </div>
    </AdminLayout>
</template>
