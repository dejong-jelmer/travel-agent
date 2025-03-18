<script setup>
import Edit from '@/Pages/Icons/Edit.vue';
import { Link } from '@inertiajs/vue3';

</script>
<template>
    <div class="relative w-fit mx-auto" :class="{ 'cursor-grab': isAdmin }">
        <div v-if="isAdmin" class="absolute w-5 top-0 right-[-20px]">
            <Link :href="route('itineraries.edit', itinerary)">
                <button  class="bg-custom-red p-2 rounded-lg">
                    <Edit class="h-5 stroke-white" />
                </button>
            </Link>
        </div>
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-2xl overflow-hidden flex">
          <!-- Text Section -->
          <div class="p-8 w-3/4 space-y-1">
            <div class="space-x-4">
                <h2 class="inline-flex text-base font-bold text-custom-light">Dag {{ itinerary.order }}</h2>
                <h1 class="inline-flex text-xl font-semibold text-custom-dark">{{ itinerary.title }}</h1>
            </div>
            <div class="space-y-8">
                <h5 class="inline-flex text-sm font-semibold text-custom-primary">{{ itinerary.subtitle }}</h5>
                <p class="text-gray-700 leading-relaxed">
                  {{ itinerary.description }}
                </p>
            </div>
          </div>

          <!-- Image Section -->
          <div class="w-1/4 inline-flex my-auto justify-center">
            <img :src="imageUrl" alt="Reisbeschrijving" class="w-auto h-auto object-center">
          </div>
        </div>

    </div>
  </template>

<script>

export default {
    props: {
        itinerary: {
            type: Object
        },
        imageUrl: {
            type: String,
            required: true
        },
        isAdmin: {
            type: Boolean,
            default: false
        }
    }
}
</script>

<style scoped>
/* Optionele custom styles kunnen hier toegevoegd worden */
</style>
