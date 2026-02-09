<script setup>
import { Link } from "@inertiajs/vue3";
import placeholder from '@/../images/placeholder.png';
import { Clock, Route } from 'lucide-vue-next';

defineProps({ trip: Object });
</script>
<template>
    <Card>
        <div class="h-[55%] min-h-[180px] rounded-t-xl overflow-hidden relative">
            <Link :href="route('trips.show', trip)">
            <div class="absolute inset-0 bg-cover bg-center transition-transform duration-500 ease-out scale-100 group-hover:scale-110"
                :style="`background-image: url(${trip.hero_image?.public_url || placeholder})`"></div>
            <div class="absolute top-3 right-3">
                <PriceBadge :price="trip.price" />
            </div>
            </Link>
        </div>
        <div class="py-5 px-8 space-y-3 text-left bg-white">
            <div class="inline-flex items-center gap-2">
                <span class="w-2 h-2 bg-accent-primary rounded-full"></span>
                <h3 class="text-sm text-brand-light font-medium line-clamp-1">
                    {{ trip.destinations_formatted }}
                </h3>
            </div>

            <!-- Titel -->
            <h4
                class="text-xl laptop:text-2xl leading-6 font-bold text-brand-primary line-clamp-2 min-h-0 laptop:min-h-8">
                {{ trip.name }}
            </h4>

            <!-- Beschrijving -->
            <p class="text-sm text-brand-primary line-clamp-3 leading-relaxed">{{ trip.description }}</p>

            <!-- Details sectie -->
            <div class="pt-2 border-t border-neutral-200">
                <div class="flex justify-between items-end">
                    <!-- Links: reis details -->
                    <div class="flex flex-col space-y-2">
                        <div class="inline-flex gap-x-2 items-center">
                            <Clock class="h-5 w-5 text-accent-primary"></Clock>
                            <p class="text-sm text-brand-primary">
                                {{ trip.duration }} {{ $t('trip_card.days') }}
                            </p>
                        </div>
                        <div class="inline-flex gap-x-2 items-center">
                            <Route class="h-5 w-5 text-accent-primary"></Route>
                            <p class="text-sm text-brand-primary">{{ $t('trip_card.sustainable_travel') }}</p>
                        </div>
                    </div>

                    <!-- Rechts: CTA button -->
                    <div class="flex justify-end">
                        <Link :href="route('trips.show', trip)">
                        <Button class="whitespace-nowrap">
                            {{ $t('trip_card.view_trip') }}
                        </Button>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </Card>
</template>
