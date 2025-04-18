<script setup>
import { Train, NightTrain, Euro, Duration, Directions } from "@/Pages/Icons";
import { Link } from "@inertiajs/vue3";
import Button from "./Button.vue";

defineProps({ product: Object });
</script>

<template>
    <div class="mx-auto bg-white rounded-2xl shadow-md select-none">
        <div class="h-[55%] min-h-[180px] rounded-t-2xl overflow-hidden relative">
            <Link :href="route('trip.show', product)">
                <div
                    class="absolute inset-0 bg-cover bg-center transition-transform duration-500 ease-out scale-100 hover:scale-110"
                    :style="`background-image: url(${product.featured_image?.path})`"
                ></div>
            </Link>
        </div>
        <div class="py-5 px-8 space-y-5 text-left">
            <h3 class="text-base text-deep-blue line-clamp-1">
                {{ product.countries_list }}
            </h3>
            <h4
                class="text-xl laptop:text-2xl leading-6 font-bold text-warm-terracotta line-clamp-2 min-h-0 laptop:min-h-16"
            >
                {{ product.name }}
            </h4>
            <p class="text-base text-deep-blue line-clamp-3">{{ product.description }}</p>
            <div class="grid gap-y-4">
                <div class="flex justify-between">
                    <div class="flex flex-col space-y-2">
                        <div class="inline-flex gap-x-1 items-center">
                            <Euro class="h-8 w-8 text-ochre-yellow"></Euro>
                            <p class="text-base text-deep-blue inline">
                                Vanaf&nbsp;€&nbsp;<span class="font-bold"
                                    >{{ product.price }},-</span
                                >
                            </p>
                        </div>
                        <div class="inline-flex gap-x-1 items-center">
                            <Duration class="h-8 w-8 text-ochre-yellow"></Duration>
                            <p class="text-base text-deep-blue inline">
                                {{ product.duration }}&nbsp;dagen
                            </p>
                        </div>
                        <div class="inline-flex gap-x-1 items-center">
                            <Directions class="h-8 w-8 text-ochre-yellow"></Directions>
                            <p class="text-base text-deep-blue inline">
                                <Train class="h-8 w-8 text-deep-blue" />
                            </p>
                        </div>
                    </div>
                    <div class="flex w-full justify-end items-end">
                        <Link :href="route('trip.show', product)">
                            <Button text="Bekijk"></Button>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
