<script setup>
import placeholder from '@/../images/placeholder.png';
import { Camera, MapPinned, BedDouble, UtensilsCrossed, Route, AlertTriangle, TrainFront, ArrowLeftRight, Ship, Bus } from 'lucide-vue-next';
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
        <div class="absolute left-4 top-16 bottom-0 w-0.5 bg-accent-sage/30"></div>

        <!-- Itinerary item -->
        <div class="relative flex gap-3 tablet:gap-6 pb-8 last:pb-0">
            <!-- Timeline marker -->
            <div class="relative flex-shrink-0">
                <!-- Dag nummer cirkel -->
                <div
                    class="w-8 h-8 bg-accent-earth rounded-full flex items-center justify-center shadow-sm relative ring-offset-2 ring-2 ring-accent-primary/30">
                    <span class="text-sm font-bold text-white">{{ itinerary.order }}</span>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1 pt-2 min-w-0">
                <!-- Header -->
                <div class="mb-4">
                    <h4 class="text-base tablet:text-lg font-semibold text-brand-primary mb-1">
                        {{ itinerary.title }}
                    </h4>
                    <div v-if="itinerary.location" class="flex items-center gap-2 text-sm text-brand-light">
                        <MapPinned class="w-4 h-4" />
                        <span>{{ itinerary.location }}</span>
                    </div>
                </div>

                <!-- Beschrijving met afbeelding -->
                <div class="mb-4">
                    <div v-if="itinerary.image?.full_path"
                        class="flex flex-col tablet:flex-row gap-4 items-start tablet:items-center">
                        <!-- Tekst -->
                        <div class="flex-1 min-w-0">
                            <p class="text-brand-primary leading-relaxed text-sm tablet:text-base">
                                {{ itinerary.description }}
                            </p>
                        </div>
                        <!-- Afbeelding -->
                        <div class="flex-shrink-0 w-full tablet:w-32 laptop:w-40">
                            <div class="rounded-lg overflow-hidden shadow-sm border border-accent-sage/20">
                                <img :src="itinerary.image?.full_path ?? placeholder" :alt="itinerary.title"
                                    class="w-full h-24 tablet:h-12 laptop:h-24 object-cover cursor-zoom-in"
                                    @click="openLightbox(index)" />
                            </div>
                        </div>
                        <LightBox ref="lightboxRef" :images="[itinerary.image]" />
                    </div>
                    <div v-else>
                        <p class="text-brand-primary leading-relaxed text-sm tablet:text-base">
                            {{ itinerary.description }}
                        </p>
                    </div>
                </div>

                <!-- Container voor horizontale layout - responsive -->
                <div class="grid grid-cols-1 tablet:flex flex-wrap gap-4 mb-4">
                    <!-- Activiteiten (optioneel) -->
                    <div v-if="itinerary.activities?.length" class="flex-1 min-w-0">
                        <h5
                            class="text-sm font-medium text-brand-primary mb-2 flex items-center gap-2 border-b border-gray-200 pb-2 px-2">
                            <Camera class="w-4 h-4 text-accent-primary" />
                            Activiteiten
                        </h5>
                        <ul class="space-y-1 px-2">
                            <li v-for="activity in itinerary.activities" :key="activity"
                                class="text-sm text-brand-primary flex items-start gap-2">
                                <span class="w-1.5 h-1.5 bg-accent-sage rounded-full mt-2 flex-shrink-0"></span>
                                {{ activity }}
                            </li>
                        </ul>
                    </div>

                    <!-- Accommodatie info (optioneel) -->
                    <div v-if="itinerary.accommodation" class="flex-1 min-w-0">
                        <h5
                            class="text-sm font-medium text-brand-primary mb-2 flex items-center gap-2 border-b border-gray-200 pb-2 px-2">
                            <BedDouble class="w-4 h-4 text-accent-primary" />
                            Overnachting
                        </h5>
                        <p class="text-sm text-brand-primary px-2">U verblijft in het {{ itinerary.accommodation }}</p>
                    </div>

                    <!-- Maaltijden (optioneel) -->
                    <div v-if="itinerary.meals?.length" class="flex-1 min-w-0">
                        <h5
                            class="text-sm font-medium text-brand-primary mb-2 flex items-center gap-2 border-b border-gray-200 pb-2 px-2">
                            <UtensilsCrossed class="w-4 h-4 text-accent-primary" />
                            Maaltijden
                        </h5>
                        <div class="flex flex-wrap gap-2 px-2">
                            <Pill type="sage" v-for="meal in itinerary.meals" :key="meal">
                                {{ meal }}
                            </Pill>
                        </div>
                    </div>

                    <!-- Transport info (optioneel) -->
                    <div v-if="itinerary.transport?.length" class="flex-1 min-w-0">
                        <h5
                            class="text-sm font-medium text-brand-primary mb-2 flex items-center gap-2 border-b border-gray-200 pb-2 px-2">
                            <Route class="w-4 h-4 text-accent-primary" />
                            Transport
                        </h5>
                        <div class="flex flex-wrap gap-1">
                            <Pill class="w-fit" v-for="mode in itinerary.transport" :key="mode" type="sage"
                                variant="transparent">
                                <TrainFront class="w-4 h-4 text-accent-sage mr-2" />
                                {{ mode }}
                            </Pill>
                        </div>
                    </div>
                </div>

                <!-- Belangrijke opmerking (optioneel) -->
                <div v-if="itinerary.remark"
                    class="bg-status-warning/10 rounded-lg p-3 tablet:p-4 border border-status-warning/30 mb-4">
                    <div class="flex items-start gap-3">
                        <AlertTriangle class="w-5 h-5 text-status-warning flex-shrink-0 mt-0.5" />
                        <div class="min-w-0">
                            <h5 class="text-sm font-medium text-brand-primary mb-1">Belangrijke opmerking</h5>
                            <p class="text-sm text-brand-primary">{{ itinerary.remark }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
