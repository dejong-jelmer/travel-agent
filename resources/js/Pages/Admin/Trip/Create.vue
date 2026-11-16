<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    destinations: Object,
    typeOptions: Object,
    transportOptions: Object,
    priceLabelOptions: Object,
    keyFactIconOptions: Array,
    highlightCategoryOptions: Array,
    heroFocusOptions: Array,
    tripTypeOptions: Array,
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
    journey_section: "",
    duration: "",
    type: props.tripTypeOptions?.[0]?.id ?? "",
    transport: [],
    destinations: [],
    heroImage: null,
    hero_focus: null,
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
            :highlight-category-options="highlightCategoryOptions"
            :hero-focus-options="heroFocusOptions"
            :trip-type-options="tripTypeOptions"
            :practical-sections="practicalSections"
            @submit="submit" />
    </Admin>
</template>
