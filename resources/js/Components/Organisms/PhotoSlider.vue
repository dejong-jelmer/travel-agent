<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { ChevronLeft, ChevronRight } from '@lucide/vue'
import { useI18n } from 'vue-i18n'
import { useRevealEffect } from '@/Composables/useRevealEffect.js'

defineProps({
    items: {
        type: Array,
        required: true,
    },
    // Accessible name of the scrollable region
    label: {
        type: String,
        required: true,
    },
})

const { t } = useI18n()
const { rootRef, visible: revealed } = useRevealEffect()

// A native scroll container with CSS scroll snapping: one slide at 84% with the next peeking in on phones,
// three slides from tablet on. The buttons and the progress bar follow its scroll position.
const track = ref(null)
const overflows = ref(false)
const atStart = ref(true)
const atEnd = ref(true)
const progress = ref({ width: 100, offset: 0 })

// Slides are only fetched once they come into view. Native lazy loading does not help here, because the
// slides sit side by side in a container that is itself within the viewport. Once scrolling starts, the
// next slide is fetched ahead.
const loadedCount = ref(0)

// Distance between the left edges of two neighbouring slides
const slideStep = () => track.value.firstElementChild.offsetWidth + (parseFloat(getComputedStyle(track.value).columnGap) || 0)

const update = () => {
    // Nothing to measure without slides, or while the slider is not rendered
    if (!track.value.firstElementChild?.offsetWidth) return
    const { scrollLeft, scrollWidth, clientWidth } = track.value

    overflows.value = scrollWidth > clientWidth + 1
    atStart.value = scrollLeft <= 1
    atEnd.value = scrollLeft + clientWidth >= scrollWidth - 1
    // The thumb covers the visible part of the track; translateX is relative to the thumb's own width
    progress.value = { width: clientWidth / scrollWidth * 100, offset: scrollLeft / clientWidth * 100 }

    const ahead = scrollLeft > 0 ? 1 : 0
    loadedCount.value = Math.max(loadedCount.value, Math.ceil((scrollLeft + clientWidth) / slideStep()) + ahead)
}

// Scrolls one slide; the smooth animation comes from the motion-safe:scroll-smooth class
const scroll = (direction) => {
    if (direction < 0 ? atStart.value : atEnd.value) return
    track.value.scrollBy({ left: direction * slideStep() })
}

let resizeObserver = null
onMounted(() => {
    update()
    resizeObserver = new ResizeObserver(update)
    resizeObserver.observe(track.value)
})
onBeforeUnmount(() => resizeObserver?.disconnect())
</script>

<template>
    <div ref="rootRef" class="transition-all duration-1000 ease-out"
        :class="revealed ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'">
        <div class="relative">
            <!-- No tabindex: the slides hold focusable content (the lightbox buttons), which keyboard users scroll through -->
            <div ref="track" role="region" :aria-label="label" @scroll.passive="update"
                class="flex gap-3 overflow-x-auto snap-x snap-mandatory motion-safe:scroll-smooth rounded-xl [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                <div v-for="(item, index) in items" :key="item.id ?? index"
                    class="shrink-0 snap-start w-[84%] tablet:w-[calc((100%-24px)/3)]">
                    <slot :item="item" :index="index" :loaded="index < loadedCount" />
                </div>
            </div>

            <!-- aria-disabled instead of disabled: a disabled button drops the keyboard focus at the start or end -->
            <template v-if="overflows">
                <button type="button" :aria-label="t('photo_slider.previous')" :aria-disabled="atStart" @click="scroll(-1)"
                    class="hidden tablet:flex absolute top-1/2 -translate-y-1/2 -left-[18px] items-center justify-center w-11 h-11 rounded-full bg-white border border-brand-accent/20 shadow-md shadow-brand-text/[0.12] text-brand-primary transition-opacity aria-disabled:opacity-40 aria-disabled:cursor-not-allowed">
                    <ChevronLeft class="w-5 h-5" aria-hidden="true" />
                </button>
                <button type="button" :aria-label="t('photo_slider.next')" :aria-disabled="atEnd" @click="scroll(1)"
                    class="hidden tablet:flex absolute top-1/2 -translate-y-1/2 -right-[18px] items-center justify-center w-11 h-11 rounded-full bg-white border border-brand-accent/20 shadow-md shadow-brand-text/[0.12] text-brand-primary transition-opacity aria-disabled:opacity-40 aria-disabled:cursor-not-allowed">
                    <ChevronRight class="w-5 h-5" aria-hidden="true" />
                </button>
            </template>
        </div>

        <!-- Progress bar instead of the native scrollbar, which iOS hides: phones only -->
        <div v-if="overflows" class="relative h-[3px] mt-3 rounded-full bg-brand-primary/15 overflow-hidden tablet:hidden"
            aria-hidden="true">
            <div class="absolute inset-y-0 left-0 rounded-full bg-brand-primary"
                :style="{ width: `${progress.width}%`, transform: `translateX(${progress.offset}%)` }"></div>
        </div>
    </div>
</template>
