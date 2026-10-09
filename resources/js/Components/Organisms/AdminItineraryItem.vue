<script setup>
import { Camera, BedDouble, AlertTriangle, Info } from '@lucide/vue';
import { ref } from 'vue'
import { useMq } from 'vue3-mq'

const mq = useMq()

// Rendered image width: w-48 from tablet on, below that the full content column, which on the trip page is the
// viewport minus the page padding (px-4), the card border and padding (p-6), the day label (w-20) and the gap (gap-4).
const imageSizes = '(min-width: 600px) 192px, calc(100vw - 178px)'

// LightBox (single image per itinerary, so always index 0)
const lightboxRef = ref(null)
const thumbnail = ref(null)
const openLightbox = () => {
    lightboxRef.value?.open(0, [thumbnail.value])
}
const props = defineProps({
    itinerary: {
        type: Object,
        required: true
    },
    isAdmin: {
        type: Boolean,
        default: false
    },
    index: {
        type: Number,
        default: 0
    }
})
</script>
<template>
    <div class="relative">
        <!-- Timeline (vertical) -->
        <div class="absolute left-4 top-16 bottom-0 w-px bg-brand-subtle/30"></div>

        <!-- Itinerary item -->
        <div class="group relative flex gap-4 tablet:gap-8 pb-10 last:pb-0">
            <!-- Day label -->
            <div class="relative flex-shrink-0 w-20 tablet:w-24 pt-1">
                <span class="text-sm tablet:text-base font-light text-brand-primary/60 tabular-nums">
                    {{ $t('trip_itinerary.day') }} {{ itinerary.day_from }}<span v-if="itinerary.day_to">-{{
                        itinerary.day_to }}</span>
                </span>
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
                <!-- Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base tablet:text-lg font-medium text-brand-primary mb-2">
                            {{ itinerary.title }}
                        </h3>
                        <div v-if="itinerary.accommodation" class="flex items-center gap-2 text-sm text-brand-text/70">
                            <BedDouble class="w-4 h-4" />
                            <span>{{ $t('trip_itinerary.accommodation_text') }} {{ itinerary.accommodation }}</span>
                        </div>
                    </div>

                    <!-- Admin Controls -->
                    <div v-if="isAdmin"
                        class="flex gap-2 ml-4 flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                        <IconLink type="info" icon="Pencil" :href="route('admin.itineraries.edit', itinerary)"
                            v-tippy="$t('itinerary.edit')" />
                        <IconLink type="delete" icon="Trash2" :href="route('admin.itineraries.destroy', itinerary)"
                            method="delete" :showConfirm="true" :prompt="$t('itinerary.delete_confirm')"
                            v-tippy="$t('itinerary.delete')" />
                    </div>
                </div>

                <!-- Description + optional image -->
                <div
                    :class="['flex flex-col gap-6 items-start', index % 2 === 0 ? 'tablet:flex-row' : 'tablet:flex-row-reverse']">
                    <div class="flex-1 min-w-0">
                        <p class="text-brand-text leading-relaxed text-sm tablet:text-base">
                            {{ itinerary.description }}
                        </p>
                    </div>
                    <div v-if="itinerary.image?.public_url" class="flex-shrink-0 w-full tablet:w-48 self-start">
                        <div class="rounded-md overflow-hidden">
                            <!-- The wrapper clips anything outside the image, so the focus outline is drawn inside -->
                            <button ref="thumbnail" type="button" aria-haspopup="dialog"
                                class="block w-full rounded-md focus-visible:outline focus-visible:outline-2 focus-visible:-outline-offset-4 focus-visible:outline-brand-accent"
                                @click="openLightbox">
                                <ResponsiveImage :image="itinerary.image" :sizes="imageSizes" :alt="itinerary.title" loading="lazy"
                                    class="w-full h-auto max-h-40 tablet:max-h-60 object-cover cursor-zoom-in hover:opacity-90" />
                            </button>
                        </div>
                        <LightBox ref="lightboxRef" :images="[itinerary.image]" />
                    </div>
                    <div v-else class="hidden tablet:block flex-shrink-0 tablet:w-48 self-start" aria-hidden="true">
                    </div>
                </div>

                <!-- Remark -->
                <div v-if="itinerary.remark" class="flex items-start gap-3 mt-6 text-sm text-brand-text/70">
                    <Info class="w-4 h-4 text-brand-primary/50 flex-shrink-0 mt-0.5" />
                    <p>{{ itinerary.remark }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
