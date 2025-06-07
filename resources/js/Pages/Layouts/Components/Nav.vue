<script setup>
import { ref } from 'vue';
import { Link } from "@inertiajs/vue3";
import { Logo } from "@/Pages/Icons";

const props = defineProps({
    logoClass: Array,
    menuClass: Array,
    isScrolled: {
        type: Boolean,
        default: false
    }
});

const isMenuOpen = ref(false);

const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
};

const links = {
    blog: {
        label: 'Blog',
        path: '#',
    },
    contact: {
        label: 'Contact',
        path: '#contact',
    },
    about: {
        label: 'Over ons',
        path: '/over-ons',
    }
};
</script>

<template>
    <nav class="max-w-screen-desktop mx-auto h-20 laptop:h-24 px-6 flex items-center justify-between">
        <!-- Logo -->
        <div class="min-w-[80px] w-[80px] laptop:w-[120px]">
            <Transition name="fade" mode="out-in">
                <div class="text-xl font-bold" :class="logoClass">
                    <Link :href="'/'">
                    <Logo class="text-neutral-25 w-[150px] laptop:w-[192px] h-[48px] laptop:h-[62px] filter drop-shadow-md" />
                    </Link>
                </div>
            </Transition>
        </div>

        <!-- Desktop Navigation Links -->
        <div class="hidden tablet:flex gap-x-6 laptop:gap-x-8">
            <Link v-for="link in links" :key="link.label" :href="link.path"
                :class="menuClass"
                class="px-4 py-2 text-sm laptop:text-base font-semibold text-center rounded-lg border-2 border-transparent hover:border-accent-gold transform hover:scale-105 transition-all duration-300 ease-out shadow-sm hover:shadow-md">
            {{ link.label }}
            </Link>
        </div>

        <!-- Mobile Menu -->
        <Transition name="slide-down">
            <div v-if="isMenuOpen"
                class="absolute top-full right-6 tablet:hidden bg-neutral-25 rounded-xl shadow-xl border border-neutral-200 overflow-hidden z-10 min-w-[200px]">
                <div class="py-2">
                    <Link v-for="link in links" :key="link.label"
                        :href="link.path"
                        class="block px-6 py-3 text-primary-dark hover:bg-secondary-sage hover:text-white transition-colors duration-200 font-medium"
                        @click="isMenuOpen = false">
                    {{ link.label }}
                    </Link>
                </div>
            </div>
        </Transition>

        <!-- Mobile Menu Button -->
        <button
            @click="toggleMenu"
            class="tablet:hidden relative p-3 rounded-lg border-2 transition-all duration-300 z-10 flex items-center justify-center"
            :class="[
                isScrolled
                    ? 'border-accent-gold text-white hover:bg-accent-gold/20'
                    : 'border-white text-white hover:bg-white/20 backdrop-blur-sm',
                { 'bg-accent-gold/20': isMenuOpen }
            ]"
        >
            <!-- Hamburger Icon -->
            <div class="w-6 h-6 relative flex items-center justify-center">
                <span
                    class="absolute w-5 h-0.5 bg-current transition-all duration-300 transform origin-center"
                    :class="{ 'rotate-45': isMenuOpen, '-translate-y-1.5': !isMenuOpen }"
                ></span>
                <span
                    class="absolute w-5 h-0.5 bg-current transition-opacity duration-300"
                    :class="{ 'opacity-0': isMenuOpen }"
                ></span>
                <span
                    class="absolute w-5 h-0.5 bg-current transition-all duration-300 transform origin-center"
                    :class="{ '-rotate-45': isMenuOpen, 'translate-y-1.5': !isMenuOpen }"
                ></span>
            </div>
        </button>
    </nav>
</template>

<style scoped>
/* Transitions */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

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
