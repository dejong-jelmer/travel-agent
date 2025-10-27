<script setup>
import { Link } from "@inertiajs/vue3";
import placehold from '@/../images/placehold.png';

defineProps({ product: Object });
</script>

<template>
    <div
        class="mx-auto bg-white rounded-xl shadow-sm select-none border border-neutral-200 hover:shadow-md transition-shadow duration-300">
        <div class="h-[55%] min-h-[180px] rounded-t-xl overflow-hidden relative">
            <Link :href="route('trip.show', product)">
                <div class="absolute inset-0 bg-cover bg-center transition-transform duration-500 ease-out scale-100 hover:scale-110"
                    :style="`background-image: url(${product.featured_image?.path || placehold})`"></div>
                <!-- Prijs badge op de foto -->
                <div class="absolute top-3 right-3">
                    <PriceBadge :price="product.price" />
                </div>
            </Link>
        </div>
        <div class="py-5 px-8 space-y-3 text-left bg-neutral-50">
            <!-- Landen met sage green accent -->
            <div class="inline-flex items-center gap-2">
                <span class="w-2 h-2 bg-secondary-sage rounded-full"></span>
                <h3 class="text-sm text-secondary-stone font-medium line-clamp-1">
                    {{ product.countries_list }}
                </h3>
            </div>

            <!-- Titel -->
            <h4
                class="text-xl laptop:text-2xl leading-6 font-bold text-primary-dark line-clamp-2 min-h-0 laptop:min-h-8">
                {{ product.name }}
            </h4>

            <!-- Beschrijving -->
            <p class="text-sm text-primary-default line-clamp-3 leading-relaxed">{{ product.description }}</p>

            <!-- Details sectie -->
            <div class="pt-2 border-t border-neutral-200">
                <div class="flex justify-between items-end">
                    <!-- Links: reis details -->
                    <div class="flex flex-col space-y-2">
                        <div class="inline-flex gap-x-2 items-center">
                            <Clock class="h-5 w-5 text-accent-gold"></Clock>
                            <p class="text-sm text-primary-dark">
                                {{ product.duration }} dagen
                            </p>
                        </div>
                        <div class="inline-flex gap-x-2 items-center">
                            <Directions class="h-5 w-5 text-accent-gold"></Directions>
                            <Train class="h-5 w-5 text-secondary-stone" />
                            <p class="text-xs text-secondary-stone">Duurzaam reizen</p>
                        </div>
                    </div>

                    <!-- Rechts: CTA button -->
                    <div class="flex justify-end">
                        <Link :href="route('trip.show', product)">
                        <Button
                            class="bg-accent-earth hover:bg-accent-terracotta text-primary-dark hover:text-white transition-colors duration-200 px-4 py-2 rounded-lg font-medium text-sm">
                            Bekijk&nbsp;reis
                        </Button>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
