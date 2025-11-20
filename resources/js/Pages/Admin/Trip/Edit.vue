<script setup>
import { ref } from 'vue';
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    trip: Object,
    countries: Object,
});

const form = useForm({
    ...props.trip,
    countries: props.trip.countries?.map(country => country.id) ?? [],
    featuredImage: props.trip.featured_image?.full_path ?? null,
    images: props.trip.image_urls ?? [],
});

function submit() {
    form.post(route("admin.trips.update", props.trip), { forceFormData: true });
}

</script>

<template>
    <Admin>
        <TripForm :form="form" :countries="countries" @submit="submit" />
    </Admin>
</template>
