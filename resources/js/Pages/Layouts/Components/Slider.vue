<script setup>
import Card from './Card.vue';
import Chevron from '@/Pages/Icons/Chevron.vue';
import { isMobile, isTablet } from 'mobile-device-detect';

</script>
<template>
    <div class="w-full px-4 flex items-center justify-center overflow-hidden">
        <template v-if="items.length > 3">
            <button @click="prevSlide"
                class="hidden md:block text-4xl font-thin text-custom-secondary hover:text-custom-primary transition-colors rotate-180">
                <Chevron />
            </button>
        </template>
        <div class="w-full overflow-hidden py-4">
            <div ref="slider" class="flex transition-transform duration-500 ease-in-out cursor-pointer" :class="{'justify-center': items.length < 3}"
                :style="{ transform: `translateX(-${currentIndex * (100 / visibleItems)}%)` }" @mousedown="startDrag"
                @mousemove="onDrag" @mouseup="endDrag" @mouseleave="endDrag">
                <!-- Individual Slides -->
                <div v-for="(item, index) in items" :key="index" class="w-full flex-shrink-0"
                    :style="{ width: `${100 / visibleItems}%` }">
                    <Card :product="item"></Card>
                </div>
            </div>
        </div>
        <template v-if="items.length > 3">
            <button @click="nextSlide"
                class="hidden md:block text-4xl font-thin text-custom-secondary hover:text-custom-primary transition-colors">
                <Chevron />
            </button>
        </template>
    </div>
</template>

<script>
export default {
    props: {
        items: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            currentIndex: 0,
            visibleItems: isMobile ? 1 : (isTablet ? 2 : 3),
            isDragging: false,
            startPosX: 0,
            currentTranslate: 0,
            prevTranslate: 0,
        };
    },
    methods: {
        nextSlide() {
            if (this.currentIndex < this.items.length - this.visibleItems) {
                this.currentIndex++;
            }
        },
        prevSlide() {
            if (this.currentIndex > 0) {
                this.currentIndex--;
            }
        },
        startDrag(event) {
            this.isDragging = true;
            this.startPosX = event.clientX || event.touches[0].clientX;
            this.prevTranslate = this.currentTranslate;
        },
        onDrag(event) {
            if (this.isDragging) {
                const currentPosX = event.clientX || event.touches[0].clientX;
                this.currentTranslate = this.prevTranslate + currentPosX - this.startPosX;
            }
        },
        endDrag() {
            if (this.isDragging) {
                this.isDragging = false;

                const movedBy = this.currentTranslate - this.prevTranslate;

                if (movedBy < -50 && this.currentIndex < this.items.length - this.visibleItems) {
                    this.nextSlide();
                } else if (movedBy > 50 && this.currentIndex > 0) {
                    this.prevSlide();
                }

                this.currentTranslate = 0;
                this.prevTranslate = 0;
            }
        },
    },
};
</script>
