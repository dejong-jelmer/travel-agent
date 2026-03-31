<script setup>
import { ref, onMounted } from 'vue'
import { ChevronDown } from 'lucide-vue-next'

const props = defineProps({
    delay: {
        type: Number,
        default: 3200,
    },
})

const visible = ref(false)
const scrolled = ref(false)
const prefersReducedMotion = ref(false)

onMounted(() => {
    prefersReducedMotion.value = window.matchMedia('(prefers-reduced-motion: reduce)').matches

    setTimeout(() => {
        visible.value = true
    }, props.delay)

    const onScroll = () => {
        if (window.scrollY > 50) {
            scrolled.value = true
            window.removeEventListener('scroll', onScroll)
        }
    }
    window.addEventListener('scroll', onScroll, { passive: true })
})
</script>

<template>
    <div
        :class="visible && !scrolled ? 'opacity-100' : 'opacity-0 pointer-events-none'"
        class="absolute bottom-8 left-1/2 -translate-x-1/2 transition-opacity duration-[1200ms] ease-in-out"
    >
        <ChevronDown
            :class="{ 'animate-bounce': !prefersReducedMotion }"
            class="h-8 w-8 text-brand-secondary drop-shadow-lg"
        />
    </div>
</template>
