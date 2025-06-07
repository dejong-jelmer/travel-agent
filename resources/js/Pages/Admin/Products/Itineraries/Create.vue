<script setup>
import { reactive } from "vue";
import { useForm } from "@inertiajs/vue3";
import AdminLayout from "@/Pages/Layouts/AdminLayout.vue";
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
    <AdminLayout>
        <div class="bg-white rounded-lg shadow p-4 tablet:p-6 laptop:p-10 desktop:p-12">
            <ItineraryForm :form="form" :errors="errors" @submit="submit" />
        </div>
    </AdminLayout>
</template>
