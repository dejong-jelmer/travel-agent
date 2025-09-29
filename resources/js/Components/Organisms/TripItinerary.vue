<script setup>
import { Camera, AlertTriangle } from 'lucide-vue-next';
import { ref } from 'vue'

// LigtBox
const lightboxRef = ref(null)
const openLightbox = (index) => {
  lightboxRef.value?.open(index)
}
defineProps({
    itinerary: {
        type: Object,
        required: true
    }
})
</script>
<template>
    <div class="relative">
        <!-- Timeline lijn (verticaal) -->
        <div class="absolute left-4 top-16 bottom-0 w-0.5 bg-secondary-sage/30"></div>

        <!-- Itinerary item -->
        <div class="relative flex gap-3 tablet:gap-6 pb-8 last:pb-0">
            <!-- Timeline marker -->
            <div class="relative flex-shrink-0">
                <!-- Dag nummer cirkel -->
                <div
                    class="w-8 h-8 bg-accent-earth rounded-full flex items-center justify-center shadow-sm relative ring-offset-2 ring-2 ring-accent-gold/30">
                    <span class="text-sm font-bold text-neutral-25">{{ itinerary.order }}</span>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1 pt-2 min-w-0">
                <!-- Header -->
                <div class="mb-4">
                    <h4 class="text-base tablet:text-lg font-semibold text-primary-dark mb-1">
                        {{ itinerary.title }}
                    </h4>
                    <div v-if="itinerary.location" class="flex items-center gap-2 text-sm text-secondary-stone">
                        <MapPin class="w-4 h-4" />
                        <span>{{ itinerary.location }}</span>
                    </div>
                </div>

                <!-- Beschrijving met afbeelding -->
                <div class="mb-4">
                    <div v-if="itinerary.image?.path" class="flex flex-col tablet:flex-row gap-4 items-start tablet:items-center">
                        <!-- Tekst -->
                        <div class="flex-1 min-w-0">
                            <p class="text-primary-default leading-relaxed text-sm tablet:text-base">
                                {{ itinerary.description }}
                            </p>
                        </div>
                        <!-- Afbeelding -->
                        <div class="flex-shrink-0 w-full tablet:w-32 laptop:w-40">
                            <div class="rounded-lg overflow-hidden shadow-sm border border-secondary-sage/20">
                                <img :src="itinerary.image.path" :alt="itinerary.title"
                                    class="w-full h-24 tablet:h-12 laptop:h-24 object-cover cursor-zoom-in" @click="openLightbox(index)" />
                            </div>
                        </div>
                        <LightBox ref="lightboxRef" :images="[itinerary.image]"/>
                    </div>
                    <div v-else>
                        <p class="text-primary-default leading-relaxed text-sm tablet:text-base">
                            {{ itinerary.description }}
                        </p>
                    </div>
                </div>

                <!-- Container voor horizontale layout - responsive -->
                <div class="grid grid-cols-1 tablet:flex flex-wrap gap-4 mb-4">
                    <!-- Activiteiten (optioneel) -->
                    <div v-if="itinerary.activities?.length" class="flex-1 min-w-0">
                        <h5 class="text-sm font-medium text-primary-dark mb-2 flex items-center gap-2 border-b border-gray-200 pb-2 px-2">
                            <Camera class="w-4 h-4 text-accent-gold" />
                            Activiteiten
                        </h5>
                        <ul class="space-y-1 px-2">
                            <li v-for="activity in itinerary.activities" :key="activity"
                                class="text-sm text-primary-default flex items-start gap-2">
                                <span class="w-1.5 h-1.5 bg-secondary-sage rounded-full mt-2 flex-shrink-0"></span>
                                {{ activity }}
                            </li>
                        </ul>
                    </div>

                    <!-- Accommodatie info (optioneel) -->
                    <div v-if="itinerary.accommodation" class="flex-1 min-w-0">
                        <h5 class="text-sm font-medium text-primary-dark mb-2 flex items-center gap-2 border-b border-gray-200 pb-2 px-2">
                            <Bed class="w-4 h-4 text-accent-gold" />
                            Overnachting
                        </h5>
                        <p class="text-sm text-primary-default px-2">U verblijft in het {{ itinerary.accommodation }}</p>
                    </div>

                    <!-- Maaltijden (optioneel) -->
                    <div v-if="itinerary.meals?.length" class="flex-1 min-w-0">
                        <h5 class="text-sm font-medium text-primary-dark mb-2 flex items-center gap-2 border-b border-gray-200 pb-2 px-2">
                            <Cutlery class="w-4 h-4 text-accent-gold" />
                            Maaltijden
                        </h5>
                        <div class="flex flex-wrap gap-2 px-2">
                            <span v-for="meal in itinerary.meals" :key="meal"
                                class="px-3 py-1 bg-secondary-sage/20 text-xs font-medium text-primary-dark rounded-full">
                                {{ meal }}
                            </span>
                        </div>
                    </div>

                    <!-- Transport info (optioneel) -->
                    <div v-if="itinerary.transport?.length" class="flex-1 min-w-0">
                        <h5 class="text-sm font-medium text-primary-dark mb-2 flex items-center gap-2 border-b border-gray-200 pb-2 px-2">
                            <Directions class="w-4 h-4 text-accent-gold" />
                            Transport
                        </h5>
                        <span v-for="mode in itinerary.transport" :key="mode"
                                class="inline-flex px-3 py-1 mr-2 border border-secondary-sage text-xs font-medium text-primary-dark rounded-full">
                            <Train class="w-4 h-4 text-secondary-sage mr-2" />
                            {{ mode }}
                        </span>
                    </div>
                </div>

                <!-- Belangrijke opmerking (optioneel) -->
                <div v-if="itinerary.remark" class="bg-status-warning/10 rounded-lg p-3 tablet:p-4 border border-status-warning/30 mb-4">
                    <div class="flex items-start gap-3">
                        <AlertTriangle class="w-5 h-5 text-status-warning flex-shrink-0 mt-0.5" />
                        <div class="min-w-0">
                            <h5 class="text-sm font-medium text-primary-dark mb-1">Belangrijke opmerking</h5>
                            <p class="text-sm text-primary-default">{{ itinerary.remark }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
