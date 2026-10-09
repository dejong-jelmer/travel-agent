<script setup>
const props = defineProps({
    trips: Array,
    countries: Array,
});

</script>

<template>
    <Layout>
        <section class="max-w-6xl mx-auto px-4 py-12 laptop:py-20 text-center">
            <SectionHeader>
                {{ $t('trips.title') }}
            </SectionHeader>

            <!-- The cards of a row come into view one after the other (three columns from laptop on) -->
            <div class="grid grid-cols-1 tablet:grid-cols-2 laptop:grid-cols-3 gap-8">
                <TripCard v-for="(trip, index) in trips" :key="trip.id" :trip="trip"
                    v-reveal="{ delay: (index % 3) * 80 }" />
            </div>

            <!-- Empty state -->
            <div v-if="!trips.length" class="text-center py-20">
                <p class="text-gray-500 text-lg">{{ $t('trips.empty_title') }}</p>
            </div>
        </section>

    </Layout>
</template>
