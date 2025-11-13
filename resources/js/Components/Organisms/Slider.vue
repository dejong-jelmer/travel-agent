<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
// import Swiper from "./Swiper.vue";
import { ChevronRightIcon } from '@heroicons/vue/24/outline'
import { useMq } from "vue3-mq";
const mq = useMq();

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
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
    if (mq.wide || mq.desktop || mq.laptop) {
        visibleItems.value = 3;
    } else if (mq.tablet) {
        visibleItems.value = 2;
    } else {
        visibleItems.value = 1;
    }
};

onMounted(() => {
    updateVisibleItems();
    window.addEventListener("resize", updateVisibleItems);
});

onBeforeUnmount(() => {
    window.removeEventListener("resize", updateVisibleItems);
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
    <div class="relative w-full">
        <div class="flex items-center justify-center overflow-hidden">
            <template v-if="items.length > visibleItems">
                <button
                    @click="prevSlide"
                    class="hidden tablet:block text-4xl font-thin text-brand-primary hover:text-light-blue transition-colors rotate-180"
                >
                    <ChevronRightIcon class="h-12 w-12" />
                </button>
            </template>
            <div class="max-w-screen-wide laptop:max-w-screen-desktop w-full overflow-hidden">
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
                    @click="nextSlide"
                    class="hidden tablet:block text-4xl font-thin text-brand-primary hover:text-light-blue transition-colors"
                >
                    <ChevronRightIcon class="h-12 w-12" />
                </button>
            </template>
        </div>
        <!-- <Swiper :hasDragged="hasDragged" /> -->
    </div>
</template>
