<script setup>
defineProps({
    // The published trips a story goes with, each with travel_mode, type_display and format_display, see TripCard.
    // A story without them passes an empty list and the block is not shown.
    trips: { type: Array, default: () => [] },
});
</script>

<template>
    <section v-if="trips.length" aria-labelledby="related-trips-heading" class="text-center">
        <SectionHeader id="related-trips-heading">{{ $t('blog.related_trips') }}</SectionHeader>
        <!-- The cards come into view one after the other -->
        <div class="flex flex-wrap justify-center gap-8">
            <TripCard v-for="(trip, index) in trips" :key="trip.id" :trip="trip"
                v-reveal="{ delay: index * 80 }" class="grow basis-[280px] max-w-[400px]" />
        </div>
    </section>
</template>
