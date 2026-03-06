<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    trip: Object,
    destinations: Object,
    typeOptions: Object,
    categoryOptions: Object,
    transportOptions: Object,
    priceLabelOptions: Object,
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
            :category-options="categoryOptions"
            :transport-options="transportOptions"
            :price-label-options="priceLabelOptions"
            :practical-sections="practicalSections"
            @submit="submit" />
    </Admin>
</template>
