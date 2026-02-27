<script setup>
import placeholder from '@/../images/placeholder.png';
import { Camera, BedDouble, AlertTriangle } from 'lucide-vue-next';
import { ref } from 'vue'
import { useMq } from 'vue3-mq'

const mq = useMq()

// LigtBox
const lightboxRef = ref(null)
const openLightbox = (index) => {
    lightboxRef.value?.open(index)
}
const props = defineProps({
    itinerary: {
        type: Object,
        required: true
    },
    isAdmin: {
        type: Boolean,
        default: false
    }
})
</script>
<template>
    <div class="relative">
        <!-- Timeline (vertical) -->
        <div class="absolute left-4 top-16 bottom-0 w-0.5 bg-accent-sage/30"></div>

        <!-- Itinerary item -->
        <div class="group relative flex gap-3 tablet:gap-6 pb-8 last:pb-0">
            <!-- Timeline marker -->
            <div class="relative flex-shrink-0">
                <!-- Day cirkel -->
                <div
                    class="w-8 h-8 bg-accent-earth rounded-full flex items-center justify-center shadow-sm relative ring-offset-2 ring-2 ring-accent-primary/30">
                    <span class="text-sm font-bold text-white"></span>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1 pt-2 min-w-0">
                <!-- Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-base tablet:text-lg font-semibold text-brand-primary mb-1">
                            <span class="font-caveat text-accent-earth text-xl tablet:text-2xl mr-5">{{ $t('trip_itinerary.day') }} {{ itinerary.day_from }} <span v-if="itinerary.day_to"> - {{ itinerary.day_to }}</span> </span>{{ itinerary.title }}
                        </h4>
                        <div v-if="itinerary.accommodation" class="flex items-center gap-2 text-sm text-brand-light">
                            <BedDouble class="w-4 h-4" />
                            <span>{{ $t('trip_itinerary.accommodation_text') }} {{ itinerary.accommodation }}</span>
                        </div>
                    </div>
                    <!-- Admin Controls -->
                    <div v-if="isAdmin" class="flex gap-2 ml-4 flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                        <IconLink type="info" icon="Pencil" :href="route('admin.itineraries.edit', itinerary)"
                            v-tippy="$t('itinerary.edit')" />
                        <IconLink type="delete" icon="Trash2" :href="route('admin.itineraries.destroy', itinerary)"
                            method="delete" :showConfirm="true" :prompt="$t('itinerary.delete_confirm')"
                            v-tippy="$t('itinerary.delete')" />
                    </div>
                </div>
                <div class="mb-4">
                    <div v-if="itinerary.image?.public_url"
                        class="flex flex-col tablet:flex-row gap-4 items-start tablet:items-center">
                        <div class="flex-1 min-w-0">
                            <p class="text-accent-text leading-relaxed text-sm tablet:text-base">
                                {{ itinerary.description }}
                            </p>
                        </div>
                        <div class="flex-shrink-0 w-full tablet:w-32 laptop:w-40">
                            <div class="rounded-lg overflow-hidden shadow-sm border border-accent-sage/20">
                                <img :src="itinerary.image?.public_url ?? placeholder" :alt="itinerary.title"
                                    loading="lazy"
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

                <!-- Mobile/tablet: accordions -->
                <div v-if="mq.phone || mq.tablet" class="space-y-2 mb-4">
                    <Accordion v-if="itinerary.activities?.length" :default-open="false">
                        <template #header>
                            <span class="flex items-center gap-2 text-sm">
                                <Camera class="w-4 h-4 text-accent-primary" />
                                {{ $t('trip_itinerary.activities') }}
                            </span>
                        </template>
                        <ul class="space-y-1">
                            <li v-for="activity in itinerary.activities" :key="activity"
                                class="text-sm text-accent-text flex items-start gap-2">
                                <span class="w-1.5 h-1.5 bg-accent-sage rounded-full mt-2 flex-shrink-0"></span>
                                {{ activity }}
                            </li>
                        </ul>
                    </Accordion>

                </div>

                <!-- Laptop+: bestaand flex-grid -->
                <div v-else class="grid grid-cols-1 tablet:flex flex-wrap gap-4 mb-4">
                    <div v-if="itinerary.activities?.length" class="flex-1 min-w-0">
                        <h5
                            class="text-sm font-medium text-brand-primary mb-2 flex laptop:justify-center gap-2 border-b border-gray-200 pb-2 px-2">
                            <Camera class="w-4 h-4 text-accent-primary" />
                            {{ $t('trip_itinerary.activities') }}
                        </h5>
                        <ul class="space-y-1 px-2">
                            <li v-for="activity in itinerary.activities" :key="activity"
                                class="text-sm text-accent-text flex items-start gap-2">
                                <span class="w-1.5 h-1.5 bg-accent-sage rounded-full mt-2 flex-shrink-0"></span>
                                {{ activity }}
                            </li>
                        </ul>
                    </div>

                </div>

                <div v-if="itinerary.remark"
                    class="bg-status-warning/10 rounded-lg p-3 tablet:p-4 border border-status-warning/30 mb-4">
                    <div class="flex items-center gap-3">
                        <AlertTriangle class="w-5 h-5 text-status-warning flex-shrink-0 mt-0.5" />
                        <div class="min-w-0">
                            <p class="text-sm text-brand-primary">{{ itinerary.remark }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
