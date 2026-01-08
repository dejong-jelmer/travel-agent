<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    trip: Object,
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
    form.transform((data) => ({
        ...data,
        meals: data.meals?.length ? data.meals : [],
        transport: data.transport?.length ? data.transport : [],
    })).post(route("admin.trips.itineraries.store", props.trip.id), {
        forceFormData: true,
    });
}
</script>

<template>
    <Admin>
        <div class="bg-white rounded-lg shadow p-4 tablet:p-6 laptop:p-10 desktop:p-12">
            <ItineraryForm :form="form" :meals="meals" :transport="transport" @submit="submit" />
        </div>
    </Admin>
</template>
