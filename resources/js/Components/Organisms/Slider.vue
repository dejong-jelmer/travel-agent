<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import { useRevealEffect } from '@/Composables/useRevealEffect.js';
import { ChevronRightIcon } from '@heroicons/vue/24/outline'
import { useMq } from "vue3-mq";

const mq = useMq();
const { rootRef, visible: revealed, reveal } = useRevealEffect();

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
    visible: Number,
    internalArrows: {
        type: Boolean,
        default: true,
    }
});

const items = ref(props.items);
const currentIndex = ref(0);
const visibleItems = ref(1);
const isDragging = ref(false);
const hasDragged = ref(false);
const startPosX = ref(0);
const currentTranslate = ref(0);
const prevTranslate = ref(0);

const updateVisibleItems = () => {
    const max = props.visible ?? 3
    if (mq.wide || mq.desktop || mq.laptop) {
        visibleItems.value = Math.min(3, max)
    } else if (mq.tablet) {
        visibleItems.value = Math.min(2, max)
    } else {
        visibleItems.value = 1
    }
};

let resizeTimer = null
const debouncedResize = () => {
    clearTimeout(resizeTimer)
    resizeTimer = setTimeout(updateVisibleItems, 100)
}

onMounted(() => {
    updateVisibleItems();
    window.addEventListener("resize", debouncedResize, { passive: true });
});

onBeforeUnmount(() => {
    window.removeEventListener("resize", debouncedResize);
    clearTimeout(resizeTimer)
});

const WINDOW_SIZE = 5;

const sizeForDistance = (distance) => {
    if (distance === 0) return 'lg';
    if (distance === 1) return 'md';
    return 'sm';
};

const visibleDots = computed(() => {
    const total = items.value.length;
    const active = currentIndex.value;

    if (total <= WINDOW_SIZE) {
        return Array.from({ length: total }, (_, i) => ({
            index: i,
            size: sizeForDistance(Math.abs(i - active)),
        }));
    }

    const windowStart = Math.min(Math.max(active - 2, 0), total - WINDOW_SIZE);

    return Array.from({ length: WINDOW_SIZE }, (_, i) => {
        const index = windowStart + i;
        return { index, size: sizeForDistance(Math.abs(index - active)) };
    });
});

const prevSlide = () => {
    if (currentIndex.value > 0) currentIndex.value--;
};

const nextSlide = () => {
    if (currentIndex.value < items.value.length - visibleItems.value) {
        currentIndex.value++;
    }
};

const startDrag = (event) => {
    isDragging.value = true;
    hasDragged.value = false;
    startPosX.value = event.clientX || event.touches?.[0]?.clientX;
    prevTranslate.value = currentTranslate.value;
    window.addEventListener('mouseup', endDrag, { once: true });
};

const onDrag = (event) => {
    if (isDragging.value) {
        const currentPosX = event.clientX || event.touches[0].clientX;
        currentTranslate.value = prevTranslate.value + currentPosX - startPosX.value;
    }
};

const cancelClickAfterDrag = (event) => {
    if (hasDragged.value) {
        event.preventDefault();
        event.stopPropagation();
        hasDragged.value = false;
    }
};

const endDrag = () => {
    if (isDragging.value) {
        isDragging.value = false;

        const movedBy = currentTranslate.value - prevTranslate.value;

        if (Math.abs(movedBy) > 10) {
            hasDragged.value = true;
        }

        if (
            movedBy < -50 &&
            currentIndex.value < items.value.length - visibleItems.value
        ) {
            nextSlide();
        } else if (movedBy > 50 && currentIndex.value > 0) {
            prevSlide();
        }

        currentTranslate.value = 0;
        prevTranslate.value = 0;
    }
};
</script>
<template>
    <section ref="rootRef">
        <div class="flex items-center justify-center group gap-2">
            <!-- External arrows (internalArrows=false) -->
            <template v-if="!internalArrows && items.length > visibleItems">
                <button @click="prevSlide" :disabled="currentIndex === 0"
                    class="hidden tablet:flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-brand-primary/60 hover:text-brand-primary p-1 shrink-0 disabled:opacity-20 disabled:cursor-not-allowed">
                    <ChevronRightIcon class="h-8 w-8 rotate-180" />
                </button>
            </template>
            <template v-else-if="!internalArrows">
                <span class="hidden tablet:block w-10 shrink-0"></span>
            </template>

            <div class="relative max-w-screen-wide laptop:max-w-screen-desktop w-full">
                <div class="overflow-hidden">
                    <div ref="slider" class="flex" :class="{
                        'transition-transform duration-500 ease-in-out': !isDragging,
                        'justify-center': items.length < visibleItems
                    }" :style="{
                        transform: `translateX(calc(-${currentIndex * (100 / visibleItems)}% + ${currentTranslate}px))`,
                        cursor: isDragging ? 'grabbing' : 'grab',
                    }" @mousedown="startDrag" @mousemove="onDrag" @mouseup="endDrag"
                        @click.capture="cancelClickAfterDrag" @dragstart.prevent v-touch:press="startDrag"
                        v-touch:drag="onDrag" v-touch:release="endDrag">
                        <div v-for="(item, index) in items" :key="index" class="flex-shrink-0 m-[1%] select-none transition-all duration-1000 ease-out"
                            v-bind="reveal(Math.min(index, 2) * 50)" :class="revealed ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                            :style="{
                                width: `calc(${100 / ((items < 3) ? 50 : visibleItems)}% - ${'2%'})`,
                            }">
                            <slot :item="item" :index="index" />
                        </div>
                    </div>
                </div>
                <!-- Internal arrows (internalArrows=true, default) -->
                <template v-if="internalArrows && items.length > visibleItems">
                    <button @click="prevSlide"
                        class="hidden tablet:block absolute left-3 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity text-white/80 hover:text-white p-1 hover:bg-black/50 rounded-full">
                        <ChevronRightIcon class="h-12 w-12 rotate-180" />
                    </button>
                    <button @click="nextSlide"
                        class="hidden tablet:block absolute right-3 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity text-white/80 hover:text-white p-1 hover:bg-black/50 rounded-full">
                        <ChevronRightIcon class="h-12 w-12" />
                    </button>
                </template>
            </div>

            <!-- External arrows (internalArrows=false) -->
            <template v-if="!internalArrows && items.length > visibleItems">
                <button @click="nextSlide" :disabled="currentIndex >= items.length - visibleItems"
                    class="hidden tablet:flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-brand-primary/60 hover:text-brand-primary p-1 shrink-0 disabled:opacity-20 disabled:cursor-not-allowed">
                    <ChevronRightIcon class="h-8 w-8" />
                </button>
            </template>
            <template v-else-if="!internalArrows">
                <span class="hidden tablet:block w-10 shrink-0"></span>
            </template>
        </div>

        <!-- Pagination dots: mobile only -->
        <div v-if="items.length > 1" class="flex justify-center items-center gap-1.5 mt-4 tablet:hidden">
            <button v-for="dot in visibleDots" :key="dot.index" @click="currentIndex = dot.index"
                class="rounded-full transition-all duration-200" :class="{
                    'w-2.5 h-2.5 bg-brand-primary': dot.size === 'lg',
                    'w-2 h-2 bg-brand-primary/50': dot.size === 'md',
                    'w-1.5 h-1.5 bg-brand-primary/30': dot.size === 'sm',
                }" />
        </div>
    </section>
</template>
