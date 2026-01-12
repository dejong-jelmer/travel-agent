<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
   countries: Object,
});

const form = useForm({
    name: "",
    slug: "",
    description: "",
    price: "",
    duration: "",
    countries: [],
    heroImage: null,
    images: [],
    featured: false,
    published_at: new Date(),
    highlights: [],
});


function submit() {
    form.post(route("admin.trips.store"), { forceFormData: true });
}
</script>

<template>
    <Admin>
        <TripForm :form="form" :countries="countries" @submit="submit" />
    </Admin>
</template>
