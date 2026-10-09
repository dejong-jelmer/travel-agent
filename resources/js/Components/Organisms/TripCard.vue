<script setup>
import { Link } from "@inertiajs/vue3";
import placeholder from "@/../images/placeholder.webp";
import { ArrowRight, Moon, TrainFront } from "@lucide/vue";

// The trip comes with travel_mode ({ value: 'night_train' or 'day_train', label }), see Trip::travelMode()
const props = defineProps({ trip: Object });

</script>
<template>
    <!-- The whole card is a single link, so it holds no other buttons or links -->
    <Link :href="route('trips.show', trip)"
        class="group flex flex-col bg-white border border-brand-accent/20 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300 text-left focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-accent">

        <!-- Image, positioned absolutely: in the flex column a taller photo would otherwise stretch the 16/10 box -->
        <div class="relative aspect-[16/10]">
            <img :src="trip.hero_image?.public_url || placeholder" :alt="trip.name"
                class="absolute inset-0 w-full h-full object-cover" loading="lazy" />
            <PriceBadge class="absolute top-3 right-3" :price="trip.price_formatted" :expected="trip.is_expected"
                :expected-label="$t('trip_card.expected')" />
        </div>

        <!-- Content, the footer at the bottom so the cards in a row line up -->
        <div class="flex flex-col flex-1 px-5 tablet:px-6 pt-5">

            <!-- Country / destination, in the style of the labels on the trip page -->
            <p class="text-[12.5px] uppercase tracking-[0.14em] text-brand-light line-clamp-1">
                {{ trip.destinations_formatted }}
            </p>

            <h3 class="mt-2 text-[21px] tablet:text-2xl leading-tight font-bold text-brand-primary line-clamp-2">
                {{ trip.name }}
            </h3>

            <p class="mt-3 mb-5 text-[15.5px] leading-relaxed text-brand-text line-clamp-3">
                {{ trip.subtitle }}
            </p>

            <!-- Travel mode and duration, and the call to action. The footer is a size container: from 300px on, where
                 the longest travel mode and duration fit next to the call to action, they share a line; below that the
                 duration goes under the travel mode, without the separator. -->
            <div
                class="mt-auto flex items-center justify-between gap-3 py-4 border-t border-brand-accent/20 text-sm [container-type:inline-size]">
                <p class="flex items-center gap-2 text-brand-text">
                    <Moon v-if="trip.travel_mode.value === 'night_train'" class="w-4 h-4 shrink-0 text-brand-primary"
                        aria-hidden="true" />
                    <TrainFront v-else class="w-4 h-4 shrink-0 text-brand-primary" aria-hidden="true" />
                    <span class="flex flex-col [@container(min-width:300px)]:flex-row [@container(min-width:300px)]:gap-1">
                        <span>{{ trip.travel_mode.label }}</span>
                        <template v-if="trip.duration">
                            <span class="hidden [@container(min-width:300px)]:inline" aria-hidden="true">·</span>
                            <span class="whitespace-nowrap">{{ trip.duration }} {{ $t("trip_card.days") }}</span>
                        </template>
                    </span>
                </p>
                <span class="flex items-center gap-[10px] whitespace-nowrap font-semibold text-brand-primary">
                    {{ $t("trip_card.view_trip") }}
                    <!-- Moves a little to the right while hovering the card, unless the visitor prefers reduced motion -->
                    <span
                        class="flex items-center justify-center w-7 h-7 tablet:w-[30px] tablet:h-[30px] rounded-full bg-brand-accent text-white transition-transform duration-200 motion-safe:group-hover:translate-x-[3px]"
                        aria-hidden="true">
                        <ArrowRight class="w-4 h-4" />
                    </span>
                </span>
            </div>
        </div>

    </Link>
</template>
