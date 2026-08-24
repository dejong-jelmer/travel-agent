<script setup>
import placeholder from '@/../images/placeholder.webp';
import { Camera, BedDouble, AlertTriangle, Info } from 'lucide-vue-next';
import { useRevealEffect } from '@/Composables/useRevealEffect.js';
import { ref } from 'vue'
import { useMq } from 'vue3-mq'

const { rootRef, visible, reveal } = useRevealEffect();
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
    },
    index: {
        type: Number,
        default: 0
    }
})
</script>
<template>
    <div ref="rootRef" class="relative">
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
                        <h4 class="text-base tablet:text-lg font-medium text-brand-primary mb-2">
                            {{ itinerary.title }}
                        </h4>
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
                        <p v-bind="reveal(50)"
                            :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                            class="text-brand-text leading-relaxed text-sm tablet:text-base transition-all duration-200 ease-out">
                            {{ itinerary.description }}
                        </p>
                    </div>
                    <div v-if="itinerary.image?.public_url" class="flex-shrink-0 w-full tablet:w-48 self-start">
                        <div class="rounded-md overflow-hidden">
                            <img :src="itinerary.image?.public_url ?? placeholder" :alt="itinerary.title" loading="lazy"
                                v-bind="reveal(50)"
                                :class="visible ? 'opacity-100 translate-x-0' : (index % 2 === 0 ? 'opacity-0 translate-x-4' : 'opacity-0 -translate-x-4')"
                                class="w-full h-40 tablet:h-32 object-cover cursor-zoom-in hover:opacity-90 transition-all duration-200 ease-out"
                                @click="openLightbox(index)" />
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
