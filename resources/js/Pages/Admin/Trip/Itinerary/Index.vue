<script setup>
import { usePage } from "@inertiajs/vue3";
import { fetchApi } from '@/fetchApi'
const user = usePage().props.auth?.user ?? {};
const props = defineProps({
    trip: Object,
});

function updateOrder(orderedItinerary) {
    fetchApi(route('admin.trips.itineraries.order', props.trip), {
        method: 'PATCH',
        body: { itineraries: orderedItinerary },
    })
        .then((data) => console.log(data))
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
            <SortableBlocks :blocks="trip.itineraries" @update:order="updateOrder" class="space-y-6">
                <template v-slot:default="slotProps">
                    <AdminItineraryItem :isAdmin="!!user.id" :itinerary="slotProps.block" />
                </template>
            </SortableBlocks>
        </div>
    </Admin>
</template>
