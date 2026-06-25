<script setup>
import { Link } from "@inertiajs/vue3";
import placeholder from "@/../images/placeholder.webp";
import { Clock, Route } from "lucide-vue-next";

const props = defineProps({ trip: Object });

</script>
<template>
    <Link :href="route('trips.show', trip)">
        <Card class="group/card cursor-pointer">

            <!-- Image -->
            <div class="h-48 tablet:h-52 rounded-t-xl overflow-hidden relative">
                <img :src="trip.hero_image?.public_url || placeholder" :alt="trip.name"
                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 ease-out scale-100 group-hover/card:scale-110"
                    loading="lazy" />
                <div class="absolute top-3 right-3">
                    <PriceBadge :price="trip.price_formatted" :expected="trip.is_expected" :expected-label="$t('trip_card.expected')" />
                </div>
            </div>

            <!-- Content -->
            <div class="py-5 px-8 space-y-3 text-left bg-white select-none">

                <!-- Country / destination -->
                <p class="text-sm text-brand-light font-medium line-clamp-1">
                    {{ trip.destinations_formatted }}
                </p>

                <!-- Title -->
                <div class="min-h-[40px]">

                    <h3 class="text-xl laptop:text-2xl leading-6 font-bold text-brand-primary line-clamp-1">
                        {{ trip.name }}
                    </h3>
                </div>

                <!-- Description -->
                <div class="min-h-[80px]">
                    <p class="text-sm text-brand-text line-clamp-3 leading-relaxed">
                        {{ trip.intro }}
                    </p>
                </div>

                <!-- Details + CTA -->
                <div class="pt-4">
                    <div class="flex justify-between items-center">

                        <!-- Trip details -->
                        <div class="flex flex-col space-y-2">
                            <div v-if="trip.duration" class="inline-flex gap-x-2 items-center">
                                <Clock class="h-5 w-5 text-brand-light" />
                                <p class="text-sm text-brand-primary">
                                    {{ trip.duration }} {{ $t("trip_card.days") }}
                                </p>
                            </div>
                        </div>

                        <!-- CTA -->
                        <Button class="whitespace-nowrap">
                            {{ $t("trip_card.view_trip") }}
                        </Button>

                    </div>
                </div>
            </div>

        </Card>
    </Link>
</template>
