<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    destinations: Object,
    typeOptions: Object,
    transportOptions: Object,
    priceLabelOptions: Object,
    keyFactIconOptions: Array,
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
    subtitle: "",
    description: "",
    duration: "",
    transport: [],
    destinations: [],
    heroImage: null,
    images: [],
    featured: false,
    published_at: new Date(),
    highlights: [],
    key_facts: [],
    items: [],
    prices: [],
    blocked_dates: { dates: [], weekdays: [] },
    practical_info: initializePracticalInfo(),
    meta_title: "",
    meta_description: "",
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
            :transport-options="transportOptions"
            :price-label-options="priceLabelOptions"
            :key-fact-icon-options="keyFactIconOptions"
            :practical-sections="practicalSections"
            @submit="submit" />
    </Admin>
</template>
