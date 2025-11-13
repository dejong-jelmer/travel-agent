<script setup>
import { Link } from "@inertiajs/vue3";
import placehold from '@/../images/placehold.png';
import { Clock, Route } from 'lucide-vue-next';

defineProps({ product: Object });
</script>
<template>
    <Card>
        <div class="h-[55%] min-h-[180px] rounded-t-xl overflow-hidden relative">
            <Link :href="route('trip.show', product)">
            <div class="absolute inset-0 bg-cover bg-center transition-transform duration-500 ease-out scale-100 group-hover:scale-110"
                :style="`background-image: url(${product.featured_image?.path || placehold})`"></div>
            <!-- Prijs badge op de foto -->
            <div class="absolute top-3 right-3">
                <PriceBadge :price="product.price" />
            </div>
            </Link>
        </div>
        <div class="py-5 px-8 space-y-3 text-left bg-white">
            <!-- Landen met sage green accent -->
            <div class="inline-flex items-center gap-2">
                <span class="w-2 h-2 bg-accent-primary rounded-full"></span>
                <h3 class="text-sm text-brand-light font-medium line-clamp-1">
                    {{ product.countries_list }}
                </h3>
            </div>

            <!-- Titel -->
            <h4
                class="text-xl laptop:text-2xl leading-6 font-bold text-brand-primary line-clamp-2 min-h-0 laptop:min-h-8">
                {{ product.name }}
            </h4>

            <!-- Beschrijving -->
            <p class="text-sm text-brand-primary line-clamp-3 leading-relaxed">{{ product.description }}</p>

            <!-- Details sectie -->
            <div class="pt-2 border-t border-neutral-200">
                <div class="flex justify-between items-end">
                    <!-- Links: reis details -->
                    <div class="flex flex-col space-y-2">
                        <div class="inline-flex gap-x-2 items-center">
                            <Clock class="h-5 w-5 text-accent-primary"></Clock>
                            <p class="text-sm text-brand-primary">
                                {{ product.duration }} dagen
                            </p>
                        </div>
                        <div class="inline-flex gap-x-2 items-center">
                            <Route class="h-5 w-5 text-accent-primary"></Route>
                            <p class="text-sm text-brand-primary">Duurzaam reizen</p>
                        </div>
                    </div>

                    <!-- Rechts: CTA button -->
                    <div class="flex justify-end">
                        <Link :href="route('trip.show', product)">
                        <Button>
                            Bekijk&nbsp;reis
                        </Button>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </Card>
</template>
