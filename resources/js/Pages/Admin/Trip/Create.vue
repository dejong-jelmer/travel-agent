<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    destinations: Object,
    typeOptions: Object,
    categoryOptions: Object,
    transportOptions: Object,
    priceLabelOptions: Object,
    practicalSections: Object,
});

// Initialize practical_info with all keys from practicalSections
const initializePracticalInfo = () => {
    const info = {};
    Object.keys(props.practicalSections).forEach(key => {
        info[key] = '';
    });
    return info;
};

const form = useForm({
    name: "",
    description: "",
    duration: "",
    transport: [],
    destinations: [],
    heroImage: null,
    images: [],
    featured: false,
    published_at: new Date(),
    highlights: [],
    items: [],
    prices: [],
    blocked_dates: { dates: [], weekdays: [] },
    practical_info: initializePracticalInfo(),
<<<<<<< HEAD
    meta_title: "",
    meta_description: "",
=======
>>>>>>> b9e884b3fa401a1a668de43a24b0f92b9502b33e
});

function submit() {
    form.post(route("admin.trips.store"), { forceFormData: true });
}
</script>

<template>
    <Admin>
        <TripForm
            :form="form"
            :destinations="destinations"
            :type-options="typeOptions"
            :category-options="categoryOptions"
            :transport-options="transportOptions"
            :price-label-options="priceLabelOptions"
            :practical-sections="practicalSections"
            @submit="submit" />
    </Admin>
</template>
