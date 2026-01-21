<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    trip: Object,
    countries: Object,
    typeOptions: Object,
    categoryOptions: Object,
});


const form = useForm({
    ...props.trip,
    countries: props.trip.countries?.map(country => country.id) ?? [],
    heroImage: props.trip.hero_image?.public_url ?? null,
    images: props.trip.image_paths ?? [],
    items: props.trip.items ?? [],
});

function submit() {
    form.post(route("admin.trips.update", props.trip), { forceFormData: true });
}

</script>

<template>
    <Admin>
        <TripForm :form="form" :countries="countries" :type-options="typeOptions" :category-options="categoryOptions" @submit="submit" />
    </Admin>
</template>
