<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    isScrolled: {
        type: Boolean,
        default: false
    }
});

const isOpen = ref(false);

const toggleMenu = () => {
    isOpen.value = !isOpen.value;
};

const closeMenu = () => {
    isOpen.value = false;
};

const buttonClasses = computed(() => [
    'tablet:hidden relative p-3 rounded-lg border-2 transition-all duration-300 z-10',
    props.isScrolled
        ? 'border-ui-gold text-white hover:bg-ui-gold/20'
        : 'border-white text-white hover:bg-white/20 backdrop-blur-sm',
    { 'bg-ui-gold/20': isOpen.value }
]);

defineExpose({
    isOpen,
    closeMenu
});
</script>

<template>
    <div>
        <!-- Mobile Menu Button -->
        <button @click="toggleMenu" aria-label="Toggle mobile menu" :aria-expanded="isOpen" aria-controls="mobile-menu"
            :class="buttonClasses">
            <!-- Hamburger Icon -->
            <div class="w-6 h-6 relative flex items-center justify-center">
                <span class="absolute w-5 h-0.5 bg-current transition-all duration-300 transform origin-center"
                    :class="{ 'rotate-45': isOpen, '-translate-y-1.5': !isOpen }"></span>
                <span class="absolute w-5 h-0.5 bg-current transition-opacity duration-300"
                    :class="{ 'opacity-0': isOpen }"></span>
                <span class="absolute w-5 h-0.5 bg-current transition-all duration-300 transform origin-center"
                    :class="{ '-rotate-45': isOpen, 'translate-y-1.5': !isOpen }"></span>
            </div>
        </button>

        <!-- Mobile Menu Panel -->
        <Transition name="slide-down" mode="out-in" enter-active-class="transition-all duration-300 ease-out"
            leave-active-class="transition-all duration-200 ease-in" enter-from-class="-translate-y-[10px] opacity-0"
            leave-to-class="-translate-y-[10px] opacity-0">
            <div v-if="isOpen"
                class="absolute top-full right-6 tablet:hidden bg-neutral-25 rounded-xl shadow-xl border border-neutral-200 overflow-hidden z-10 min-w-[200px]">
                <div class="py-2">
                    <slot :closeMenu="closeMenu"></slot>
                </div>
            </div>
        </Transition>
    </div>
</template>
