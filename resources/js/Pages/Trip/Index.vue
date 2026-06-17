<script setup>
import { computed, toRef } from 'vue';

const props = defineProps({
    trips: Array,
    countries: Array,
});

</script>

<template>
    <Layout>

        <!-- Hero / page header -->
        <template #hero>
            <section class="bg-brand-primary py-16 tablet:py-24">
                <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-4 tablet:px-6 laptop:px-8">
                    <h1 class="text-3xl tablet:text-4xl laptop:text-5xl font-poppins text-white mb-4">
                        {{ $t('trips.title') }}
                    </h1>
                    <p class="text-white/80 text-base tablet:text-lg max-w-2xl">
                        {{ $t('trips.subtitle') }}
                    </p>
                </div>
            </section>
        </template>

        <DecorativeLine />

        <!-- Main content -->
        <section class="py-12 tablet:py-20">
            <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-4 tablet:px-6 laptop:px-8">

                <div class="flex flex-col laptop:flex-row gap-8 laptop:gap-12 items-start">

                    <!-- Trips grid -->
                        <div v-if="trips.length"
                            class="grid grid-cols-1 tablet:grid-cols-3 gap-6 transition-opacity duration-300"
                            :class="isFiltering ? 'opacity-40 pointer-events-none' : 'opacity-100'">
                            <TripCard v-for="trip in trips" :key="trip.id" :trip="trip" />
                        </div>

                        <!-- Empty state -->
                        <div v-else
                            class="text-left py-36 px-6">
                            <p class="text-xl font-poppins text-brand-primary mb-2">
                                {{ $t('trips.empty_title') }}
                            </p>
                        </div>
                    <!-- <div class="flex-1 min-w-0">
                    </div> -->
                </div>
            </div>
        </section>

    </Layout>
</template>
