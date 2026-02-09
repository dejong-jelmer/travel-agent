<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    destination: Object,
    travelInfoSections: Object,
    countries: Array,
});

// Initialize travel_info with all keys from travelInfoSections, merging with existing data
const initializeTravelInfo = () => {
    const info = {};
    Object.keys(props.travelInfoSections).forEach(key => {
        info[key] = props.destination.travel_info?.[key] ?? '';
    });
    return info;
};

const form = useForm({
    ...props.destination,
    travel_info: initializeTravelInfo(),
});

function submit() {
    form.put(route("admin.destinations.update", props.destination));
}
</script>

<template>
    <Admin>
        <DestinationForm
            :form="form"
            :travel-info-sections="travelInfoSections"
            :countries="countries"
            @submit="submit"
        />
    </Admin>
</template>
