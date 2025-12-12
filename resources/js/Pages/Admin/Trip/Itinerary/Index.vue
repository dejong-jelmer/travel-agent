<script setup>
import { usePage } from "@inertiajs/vue3";
import axios from '@/axios'
const user = usePage().props.auth?.user ?? {};
const props = defineProps({
    trip: Object,
});

function updateOrder(orderedItinerary) {
    axios
        .patch(route("admin.trips.itineraries.order", props.trip), {
            itineraries: orderedItinerary,
        })
        .then((response) => console.log(response.data))
        .catch((error) => console.error(error));
}
</script>
<template>
    <Admin>
        <div class="space-y-4">
            <div class="flex mx-auto justify-end">
                <IconLink type="info" v-tippy="'Voeg het dagschema toe'" icon="Plus"
                    :href="route('admin.trips.itineraries.create', trip)" />
            </div>
            <SortableBlocks :blocks="trip.itineraries" @update:order="updateOrder" class="grid gap-y-10">
                <template v-slot:default="slotProps">
                    <Itinerary :isAdmin="!!user.id" :itinerary="slotProps.block" />
                </template>
            </SortableBlocks>
        </div>
    </Admin>
</template>
