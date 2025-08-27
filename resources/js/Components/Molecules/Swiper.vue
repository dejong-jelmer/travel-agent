<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from "vue";
// import { Swipe } from "@/Icons";

const props = defineProps(['hasDragged']);
const hasDraggedRef = computed(() => props.hasDragged);

const showIcon = ref(true);
const iconRef = ref(null);
let observer = null;
let hasStartedTimeout = false;

onMounted(() => {
    observer = new IntersectionObserver(
        ([entry]) => {
            if (entry.isIntersecting && !hasStartedTimeout) {
                hasStartedTimeout = true;
                setTimeout(() => {
                    showIcon.value = false;
                }, 10000);
            }
        },
        {
            threshold: 0.5,
        }
    );

    if (iconRef.value) {
        observer.observe(iconRef.value);
    }
});

onBeforeUnmount(() => {
    if (observer && iconRef.value) {
        observer.unobserve(iconRef.value);
    }
});
</script>
<template>
    <transition name="fade">
        <div
            ref="iconRef"
            v-if="!hasDraggedRef && showIcon"
            class="absolute top-[50%] z-10 w-full flex justify-center pt-4 text-slate-500 opacity-50 tablet:hidden"
        >
            <Swipe class="h-24 w-24 animate-wiggle-x" />
        </div>
    </transition>
</template>
<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition-property: opacity;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
    transition-duration: 1000ms;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
.fade-enter-to,
.fade-leave-from {
    opacity: 0.5;
}
</style>
