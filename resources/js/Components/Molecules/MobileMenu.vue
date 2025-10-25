<script setup>
import { ref } from 'vue';

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

defineExpose({
    isOpen,
    closeMenu
});
</script>

<template>
    <div>
        <!-- Mobile Menu Button -->
        <button
            @click="toggleMenu"
            class="tablet:hidden relative p-3 rounded-lg border-2 transition-all duration-300 z-10 flex items-center justify-center"
            :class="[
                isScrolled
                    ? 'border-accent-gold text-white hover:bg-accent-gold/20'
                    : 'border-white text-white hover:bg-white/20 backdrop-blur-sm',
                { 'bg-accent-gold/20': isOpen }
            ]"
        >
            <!-- Hamburger Icon -->
            <div class="w-6 h-6 relative flex items-center justify-center">
                <span
                    class="absolute w-5 h-0.5 bg-current transition-all duration-300 transform origin-center"
                    :class="{ 'rotate-45': isOpen, '-translate-y-1.5': !isOpen }"
                ></span>
                <span
                    class="absolute w-5 h-0.5 bg-current transition-opacity duration-300"
                    :class="{ 'opacity-0': isOpen }"
                ></span>
                <span
                    class="absolute w-5 h-0.5 bg-current transition-all duration-300 transform origin-center"
                    :class="{ '-rotate-45': isOpen, 'translate-y-1.5': !isOpen }"
                ></span>
            </div>
        </button>

        <!-- Mobile Menu Panel -->
        <Transition name="slide-down">
            <div v-if="isOpen"
                class="absolute top-full right-6 tablet:hidden bg-neutral-25 rounded-xl shadow-xl border border-neutral-200 overflow-hidden z-10 min-w-[200px]">
                <div class="py-2">
                    <slot :closeMenu="closeMenu"></slot>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.slide-down-enter-active {
    transition: all 0.3s ease-out;
}

.slide-down-leave-active {
    transition: all 0.2s ease-in;
}

.slide-down-enter-from {
    transform: translateY(-10px);
    opacity: 0;
}

.slide-down-leave-to {
    transform: translateY(-10px);
    opacity: 0;
}
</style>
