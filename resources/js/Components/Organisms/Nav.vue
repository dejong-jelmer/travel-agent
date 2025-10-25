<script setup>
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    logoClass: Array,
    menuClass: Array,
    isScrolled: {
        type: Boolean,
        default: false
    }
});

const links = {
    // blog: {
    //     label: 'Blog',
    //     path: '#',
    // },
    contact: {
        label: 'Contact',
        path: '/contact',
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
            <NavLink
                v-for="link in links"
                :key="link.label"
                :href="link.path"
                :label="link.label"
                variant="desktop"
                :link-class="menuClass"
            />
        </div>

        <!-- Mobile Menu -->
        <MobileMenu :is-scrolled="isScrolled">
            <template #default="{ closeMenu }">
                <NavLink
                    v-for="link in links"
                    :key="link.label"
                    :href="link.path"
                    :label="link.label"
                    variant="mobile"
                    @click="closeMenu"
                />
            </template>
        </MobileMenu>
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
</style>
