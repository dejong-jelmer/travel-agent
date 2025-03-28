<script setup>
import { Warning } from '@/Pages/Icons';
import IconLink from '@/Pages/Layouts/Components/IconLink.vue';

const  confirmDelete = (href) => {
    if (!confirm('Weet je zeker dat je dit reisplan wilt verwijderen?')) {
        return;
    }
    Inertia.delete(href);
}
</script>
<template>
    <div class="relative max-w-4xl mx-auto bg-white rounded-2xl shadow-2xl overflow-hidden p-4 sm:p-6 lg:p-8"
        :class="{ 'cursor-grab': isAdmin }">
        <div v-if="isAdmin" class="absolute top-2 right-2 flex flex-col gap-y-2">
            <IconLink icon="Delete" :href="route('itineraries.destroy', itinerary)" method="delete" :showConfirm="true" prompt="Weet je zeker dat je dit reisplan wilt verwijderen?"/>
            <IconLink icon="Edit" :href="route('itineraries.edit', itinerary)" />
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-6 gap-4 items-center">
            <div class="flex justify-center lg:col-span-2 order-1 lg:order-2">
                <img :src="itinerary.image" alt="Reisbeschrijving"
                    class="w-[120px] h-[120px] sm:w-[150px] sm:h-[150px] object-cover rounded-lg">
            </div>

            <div class="lg:col-span-4 order-2 lg:order-1">
                <h2 class="text-base font-bold text-custom-light">
                    Dag {{ itinerary.order }}
                </h2>
                <h1 class="text-xl font-semibold text-custom-dark mt-1">
                    {{ itinerary.title }}
                </h1>
                <h5 class="text-sm font-semibold text-custom-primary mt-2">
                    {{ itinerary.subtitle }}
                </h5>
                <p class="text-gray-700 leading-relaxed mt-4">
                    {{ itinerary.description }}
                </p>
                <div v-if="itinerary.remark" class="flex items-center mt-4">
                    <div class="w-full p-2 bg-gray-200 rounded-md inline-flex gap-x-4 border border-gray-600 text-gray-600 text-sm">
                        <Warning class="h-5 w-5" />{{ itinerary.remark }}
                    </div>
                </div>
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
        isAdmin: {
            type: Boolean,
            default: false
        }
    }
}
</script>
