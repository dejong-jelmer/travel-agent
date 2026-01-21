<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
   countries: Object,
   typeOptions: Object,
    categoryOptions: Object,
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
    items: [],
});

function submit() {
    form.post(route("admin.trips.store"), { forceFormData: true });
}
</script>

<template>
    <Admin>
        <TripForm :form="form" :countries="countries" :type-options="typeOptions" :category-options="categoryOptions" @submit="submit" />
    </Admin>
</template>
