<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from "vue";

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
    <Transition
        enter-active-class="transition-opacity duration-1000 ease-in-out"
        leave-active-class="transition-opacity duration-1000 ease-in-out"
        enter-from-class="opacity-0"
        leave-to-class="opacity-0"
        enter-to-class="opacity-50"
        leave-from-class="opacity-50"
    >
        <div
            ref="iconRef"
            v-if="!hasDraggedRef && showIcon"
            class="absolute top-[50%] z-10 w-full flex justify-center pt-4 text-slate-500 opacity-50 tablet:hidden"
        >
            <Swipe class="h-24 w-24 animate-wiggle-x" />
        </div>
    </Transition>
</template>
