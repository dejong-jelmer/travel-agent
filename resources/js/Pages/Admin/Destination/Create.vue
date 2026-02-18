<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    travelInfoSections: Object,
    countries: Array,
});

// Initialize travel_info with all keys from travelInfoSections
const initializeTravelInfo = () => {
    const info = {};
    Object.keys(props.travelInfoSections).forEach(key => {
        info[key] = '';
    });
    return info;
};

const form = useForm({
    country_code: "",
    region: "",
    name: "",
    travel_info: initializeTravelInfo(),
});

function submit() {
    form.transform((data) => ({
        ...data,
        travel_info: form.region ? null : form.travel_info,
    })).post(route("admin.destinations.store"), { forceFormData: true });
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
