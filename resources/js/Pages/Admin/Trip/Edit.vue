<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    trip: Object,
    destinations: Object,
    typeOptions: Object,
    transportOptions: Object,
    priceLabelOptions: Object,
    keyFactIconOptions: Array,
    highlightCategoryOptions: Array,
    heroFocusOptions: Array,
    journeySectionOptions: Array,
    practicalSections: Object,
});

// Initialize practical_info with all keys from practicalSections, merging with existing data
const initializePracticalInfo = () => {
    const info = {};
    Object.keys(props.practicalSections).forEach(key => {
        info[key] = props.trip.practical_info?.[key] ?? '';
    });
    return info;
};

const form = useForm({
    ...props.trip,
    destinations: props.trip.destinations?.map(destination => destination.id) ?? [],
    journey_section: props.trip.journey_section ?? "",
    // Spread into an object: PHP sends a trip without section photos as an empty array
    section_images: { ...props.trip.section_images },
    heroImage: props.trip.hero_image?.public_url ?? null,
    images: props.trip.image_paths ?? [],
    items: props.trip.items ?? [],
    prices: props.trip.prices?.map(p => ({
        id: p.id,
        base_price_pp: p.base_price_pp / 100,
        single_supplement: p.single_supplement / 100,
        valid_from: p.valid_from,
        valid_until: p.valid_until,
        label: p.label,
    })) ?? [],
    practical_info: initializePracticalInfo(),
    blocked_dates: JSON.parse(JSON.stringify(props.trip.blocked_dates ?? { dates: [], weekdays: [] })),
});

function submit() {
    form.post(route("admin.trips.update", props.trip), { forceFormData: true });
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
            :journey-section-options="journeySectionOptions"
            :gallery-images="trip.images"
            :practical-sections="practicalSections"
            @submit="submit" />
    </Admin>
</template>
