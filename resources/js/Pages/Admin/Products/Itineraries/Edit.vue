<script setup>
import { ref } from 'vue';
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    itinerary: Object,
    meals: Object,
    transport: Object,
});

const form = useForm({
    ...props.itinerary,
    image: props.itinerary.image?.path ?? null,
});

// Counter for image uploader initialization (only 1 image uploader)
const initCounter = ref(1);

function handleImageInitialized() {
    initCounter.value--;
    if (initCounter.value === 0) {
        // Uploader has finished initialization
        form.defaults({
            ...form.data(),
        });
    }
}

function submit() {
    form.post(route("admin.itineraries.update", props.itinerary.id), { forceFormData: true });
}
</script>

<template>
    <Admin>
        <div class="bg-white rounded-lg shadow p-4 tablet:p-6 laptop:p-10 desktop:p-12">
            <ItineraryForm :form="form" :meals="meals" :transport="transport" @submit="submit" @initialized="handleImageInitialized" />
        </div>
    </Admin>
</template>
