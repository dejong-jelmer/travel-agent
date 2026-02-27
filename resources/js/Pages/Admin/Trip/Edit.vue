<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    trip: Object,
    destinations: Object,
    typeOptions: Object,
    categoryOptions: Object,
    transportOptions: Object,
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
            :practical-sections="practicalSections"
            @submit="submit" />
    </Admin>
</template>
