<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    product: Object,
    errors: Object,
    meals: Object,
    transport: Object,
});

const form = useForm({
    title: '',
    location: '',
    description: '',
    accommodation: '',
    activities: '',
    meals: [],
    transport: [],
    remark: '',
    image: '',
});

function submit() {
    submitForm.post(route("products.itineraries.store", props.product.id), {
        forceFormData: true,
    });
}
</script>

<template>
    <Admin>
        <div class="bg-white rounded-lg shadow p-4 tablet:p-6 laptop:p-10 desktop:p-12">
            <ItineraryForm :form="form" :meals="meals" :transport="transport" :errors="errors" @submit="submit" />
        </div>
    </Admin>
</template>
