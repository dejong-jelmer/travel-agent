<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    trip: Object,
});

const form = useForm({
    trip_id: props.trip.id,
    title: '',
    day_from: '',
    day_to: '',
    description: '',
    accommodation: '',
    activities: [],
    remark: '',
    image: '',
});

function submit() {
    form.post(route("admin.trips.itineraries.store", props.trip.id), { forceFormData: true });
}
</script>

<template>
    <Admin>
        <div class="bg-white rounded-lg shadow p-4 tablet:p-6 laptop:p-10 desktop:p-12">
            <ItineraryForm :form="form" @submit="submit" />
        </div>
    </Admin>
</template>
