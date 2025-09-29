<script setup>
import { useForm } from "@inertiajs/vue3";
const props = defineProps({
    itinerary: Object,
    meals: Object,
    transport: Object,
});

const form = useForm({
    title: props.itinerary.title,
    location: props.itinerary.location,
    description: props.itinerary.description,
    accommodation: props.itinerary.accommodation,
    activities: props.itinerary.activities,
    meals: props.itinerary.meals,
    transport: props.itinerary.transport,
    remark: props.itinerary.remark,
    image: props.itinerary.image?.path,
});

function submit() {
    form.post(route("admin.itineraries.update", props.itinerary.id), { forceFormData: true });
}
</script>

<template>
    <Admin>
        <div class="bg-white rounded-lg shadow p-4 tablet:p-6 laptop:p-10 desktop:p-12">
            <ItineraryForm :form="form" :meals="meals" :transport="transport" @submit="submit" />
        </div>
    </Admin>
</template>
