<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    itinerary: Object,
});

const form = useForm({
    ...props.itinerary,
    image: props.itinerary.image?.public_url ?? null,
});

function submit() {
    form.post(route("admin.itineraries.update", props.itinerary.id), { forceFormData: true });
}
</script>

<template>
    <Admin>
        <div class="bg-white rounded-lg shadow p-4 tablet:p-6 laptop:p-10 desktop:p-12">
            <ItineraryForm :form="form" @submit="submit" />
        </div>
    </Admin>
</template>
