<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    trip: Object,
    typeOptions: Array,
});

const form = useForm({
    trip_id: props.trip.id,
    type: 'stay',
    title: '',
    day_from: '',
    day_to: '',
    description: '',
    accommodation: '',
    remark: '',
    image: '',
});

function submit() {
    form.post(route("admin.trips.itineraries.store", props.trip), { forceFormData: true });
}
</script>

<template>
    <Admin>
        <div class="bg-white rounded-lg shadow p-4 tablet:p-6 laptop:p-10 desktop:p-12">
            <ItineraryForm :form="form" :type-options="typeOptions" @submit="submit" />
        </div>
    </Admin>
</template>
