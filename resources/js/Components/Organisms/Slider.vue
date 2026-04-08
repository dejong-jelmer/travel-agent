<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import { ChevronRightIcon } from '@heroicons/vue/24/outline'
import { useMq } from "vue3-mq";
const mq = useMq();

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
    visible: Number
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
    startPosX.value = event.clientX || event.touches[0].clientX;
    prevTranslate.value = currentTranslate.value;
};

const onDrag = (event) => {
    if (isDragging.value) {
        const currentPosX = event.clientX || event.touches[0].clientX;
        currentTranslate.value = prevTranslate.value + currentPosX - startPosX.value;
    }
};

const endDrag = () => {
    if (isDragging.value) {
        hasDragged.value = true;
        isDragging.value = false;

        const movedBy = currentTranslate.value - prevTranslate.value;

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
    <div class="flex items-center justify-center group">
        <div class="relative max-w-screen-wide laptop:max-w-screen-desktop w-full">
            <div class="overflow-hidden">
                <div
                    ref="slider"
                    class="flex transition-transform duration-500 ease-in-out"
                    :class="{ 'justify-center': items.length < visibleItems }"
                    :style="{
                        transform: `translateX(-${currentIndex * (100 / visibleItems)}%)`,
                    }"
                    @mousedown="startDrag"
                    @mousemove="onDrag"
                    @mouseup="endDrag"
                    @mouseleave="endDrag"
                    v-touch:press="startDrag"
                    v-touch:drag="onDrag"
                    v-touch:release="endDrag"
                >
                    <div
                        v-for="(item, index) in items"
                        :key="index"
                        class="flex-shrink-0 m-[1%]"
                        :style="{
                            width: `calc(${100 / ((items < 3) ? 50 : visibleItems )}% - ${'2%'})`,
                        }"
                    >
                        <slot :item="item" :index="index" />
                    </div>
                </div>
            </div>
            <template v-if="items.length > visibleItems">
                <button
                    @click="prevSlide"
                    class="hidden tablet:block absolute left-2 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity text-white/80 hover:text-white p-1 bg-black/10 hover:bg-black/30 rounded-full"
                >
                    <ChevronRightIcon class="h-12 w-12 rotate-180" />
                </button>
                <button
                    @click="nextSlide"
                    class="hidden tablet:block absolute right-2 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity text-white/80 hover:text-white p-1 bg-black/10 hover:bg-black/30 rounded-full"
                >
                    <ChevronRightIcon class="h-12 w-12" />
                </button>
            </template>
        </div>
    </div>
</template>
