<script setup>
import placeholder from '@/../images/placeholder.webp';
import { Camera, BedDouble, AlertTriangle, Info } from 'lucide-vue-next';
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
        <div class="absolute left-4 top-16 bottom-0 w-px bg-brand-subtle/30"></div>

        <!-- Itinerary item -->
        <div class="group relative flex gap-4 tablet:gap-8 pb-10 last:pb-0">
            <!-- Day label -->
            <div class="relative flex-shrink-0 w-20 tablet:w-24 pt-1">
                <span class="text-sm tablet:text-base font-light text-brand-primary/60 tabular-nums">
                    {{ $t('trip_itinerary.day') }} {{ itinerary.day_from }}<span v-if="itinerary.day_to"> - {{ itinerary.day_to }}</span>
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
                    <div
                        v-if="isAdmin"
                        class="flex gap-2 ml-4 flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity duration-150"
                    >
                        <IconLink
                            type="info"
                            icon="Pencil"
                            :href="route('admin.itineraries.edit', itinerary)"
                            v-tippy="$t('itinerary.edit')"
                        />
                        <IconLink
                            type="delete"
                            icon="Trash2"
                            :href="route('admin.itineraries.destroy', itinerary)"
                            method="delete"
                            :showConfirm="true"
                            :prompt="$t('itinerary.delete_confirm')"
                            v-tippy="$t('itinerary.delete')"
                        />
                    </div>
                </div>

                <!-- Description + optional image -->
                <div v-if="itinerary.image?.public_url" class="flex flex-col tablet:flex-row gap-6 items-start">
                    <div class="flex-1 min-w-0">
                        <p class="text-brand-text leading-relaxed text-sm tablet:text-base">
                            {{ itinerary.description }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 w-full tablet:w-48 self-start">
                        <div class="rounded-md overflow-hidden">
                            <img
                                :src="itinerary.image?.public_url ?? placeholder"
                                :alt="itinerary.title"
                                loading="lazy"
                                class="w-full h-40 tablet:h-32 object-cover cursor-zoom-in transition-opacity duration-200 hover:opacity-90"
                                @click="openLightbox(index)"
                            />
                        </div>
                        <LightBox ref="lightboxRef" :images="[itinerary.image]" />
                    </div>
                </div>
                <div v-else>
                    <p class="text-brand-text leading-relaxed text-sm tablet:text-base">
                        {{ itinerary.description }}
                    </p>
                </div>

                <!-- Remark -->
                <div
                    v-if="itinerary.remark"
                    class="flex items-start gap-3 mt-6 text-sm text-brand-text/70"
                >
                    <Info class="w-4 h-4 text-brand-primary/50 flex-shrink-0 mt-0.5" />
                    <p>{{ itinerary.remark }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
