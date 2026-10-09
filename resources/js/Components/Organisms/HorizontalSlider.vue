<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import { ChevronLeft, ChevronRight } from '@lucide/vue'
import { useI18n } from 'vue-i18n'

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
    // Accessible name of the scrollable region
    label: {
        type: String,
        required: true,
    },
    // Number of slides side by side from laptop on
    visible: {
        type: Number,
        default: 3,
    },
    // Number of slides side by side on tablets, the same as from laptop on when not given
    tabletVisible: {
        type: Number,
        default: null,
    },
    // Width of a slide on phones, leaving room for the next one to peek in
    mobileWidth: {
        type: String,
        default: '84%',
    },
    // Space between the slides, in px
    gap: {
        type: Number,
        default: 12,
    },
})

const { t } = useI18n()

// A native scroll container with CSS scroll snapping: one slide at the mobile width with the next peeking in on
// phones, the visible number of slides side by side from tablet on. The buttons and the progress bar follow its
// scroll position.
const track = ref(null)

// Width of a slide when `count` slides fill the track, with the gaps between them
const slideWidth = (count) => `calc((100% - ${(count - 1) * props.gap}px) / ${count})`

// The widths per breakpoint (screens.js) go to the slides as custom properties, picked by their width classes
const trackStyle = computed(() => ({
    gap: `${props.gap}px`,
    '--slide-width-phone': props.mobileWidth,
    '--slide-width-tablet': slideWidth(props.tabletVisible ?? props.visible),
    '--slide-width-laptop': slideWidth(props.visible),
}))
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

// One measurement per frame is enough while scrolling, or when several images in the slides load at once
let frame = null
const scheduleUpdate = () => {
    if (frame) return
    frame = requestAnimationFrame(() => {
        frame = null
        update()
    })
}

// Scrolls one slide; the smooth animation comes from the motion-safe:scroll-smooth class. A button fading out at
// the start or end still takes clicks, which do nothing.
const scroll = (direction) => {
    if (direction < 0 ? atStart.value : atEnd.value) return
    track.value.scrollBy({ left: direction * slideStep() })
}

// A button that hides while it has the focus, such as next after the last click, hands the focus to the other one,
// so keyboard users keep their place. Runs after the DOM update, once the other button is displayed again.
const previousButton = ref(null)
const nextButton = ref(null)
watch([atStart, atEnd], ([start, end]) => {
    const focused = document.activeElement
    if (start && !end && focused === previousButton.value) nextButton.value?.focus()
    if (end && !start && focused === nextButton.value) previousButton.value?.focus()
}, { flush: 'post' })

let resizeObserver = null
onMounted(() => {
    update()
    resizeObserver = new ResizeObserver(update)
    resizeObserver.observe(track.value)
})
onBeforeUnmount(() => {
    resizeObserver?.disconnect()
    cancelAnimationFrame(frame)
})
</script>

<template>
    <div>
        <div class="relative">
            <!-- No tabindex: the slides hold focusable content (the lightbox buttons), which keyboard users scroll through.
                 Load events do not bubble, so the images in the slides are caught in the capture phase. -->
            <div ref="track" role="region" :aria-label="label" :style="trackStyle" @scroll.passive="scheduleUpdate"
                @load.capture="scheduleUpdate"
                class="flex overflow-x-auto snap-x snap-mandatory motion-safe:scroll-smooth rounded-xl [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                <div v-for="(item, index) in items" :key="item.id ?? index"
                    class="shrink-0 snap-start w-[--slide-width-phone] tablet:w-[--slide-width-tablet] laptop:w-[--slide-width-laptop]">
                    <slot :item="item" :index="index" :loaded="index < loadedCount" />
                </div>
            </div>

            <!-- Hidden at the start or end: they fade out, unless reduced motion is preferred, after which display: none
                 takes them out of the tab order -->
            <template v-if="overflows">
                <Transition enter-active-class="motion-safe:transition-opacity motion-safe:duration-200"
                    enter-from-class="opacity-0" leave-active-class="motion-safe:transition-opacity motion-safe:duration-200"
                    leave-to-class="opacity-0">
                    <button v-show="!atStart" ref="previousButton" type="button" :aria-label="t('slider.previous')"
                        @click="scroll(-1)"
                        class="hidden tablet:flex absolute top-1/2 -translate-y-1/2 -left-[18px] items-center justify-center w-11 h-11 rounded-full bg-white border border-brand-accent/20 shadow-md shadow-brand-text/[0.12] text-brand-primary">
                        <ChevronLeft class="w-5 h-5" aria-hidden="true" />
                    </button>
                </Transition>
                <Transition enter-active-class="motion-safe:transition-opacity motion-safe:duration-200"
                    enter-from-class="opacity-0" leave-active-class="motion-safe:transition-opacity motion-safe:duration-200"
                    leave-to-class="opacity-0">
                    <button v-show="!atEnd" ref="nextButton" type="button" :aria-label="t('slider.next')" @click="scroll(1)"
                        class="hidden tablet:flex absolute top-1/2 -translate-y-1/2 -right-[18px] items-center justify-center w-11 h-11 rounded-full bg-white border border-brand-accent/20 shadow-md shadow-brand-text/[0.12] text-brand-primary">
                        <ChevronRight class="w-5 h-5" aria-hidden="true" />
                    </button>
                </Transition>
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
