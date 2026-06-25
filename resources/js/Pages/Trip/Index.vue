<script setup>
import { useRevealEffect } from '@/Composables/useRevealEffect.js';

const props = defineProps({
    trips: Array,
    countries: Array,
});

const { rootRef, visible, reveal } = useRevealEffect();

</script>

<template>
    <Layout>
        <section class="max-w-6xl mx-auto px-4 py-12 laptop:py-20 text-center">
            <SectionHeader>
                {{ $t('trips.title') }}
            </SectionHeader>

            <div ref="rootRef" class="grid grid-cols-1 tablet:grid-cols-2 laptop:grid-cols-3 gap-8">
                <TripCard v-for="(trip, index) in trips" :key="trip.id" :trip="trip"
                    class="transition-all duration-1000 ease-out"
                    v-bind="reveal(Math.min(index, 5) * 75)"
                    :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'" />
            </div>

            <!-- Empty state -->
            <div v-if="!trips.length" class="text-center py-20">
                <p class="text-gray-500 text-lg">{{ $t('trips.empty_title') }}</p>
            </div>
        </section>

    </Layout>
</template>
