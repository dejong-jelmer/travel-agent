<script setup>
import { useI18n } from 'vue-i18n';
import { Link } from "@inertiajs/vue3";
import { computed } from 'vue';

const { t } = useI18n();

const links = computed(() => ({
    contact: {
        label: t('nav.contact'),
        path: '/contact',
    },
    about: {
        label: t('nav.about'),
        path: '/over-ons',
    }
}));
</script>

<template>
    <nav class="bg-white backdrop-blur-lg border-b border-accent-primary">
        <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto h-20 laptop:h-24 px-6 laptop:px-8 flex items-center justify-between">
            <!-- Logo -->
            <div class="hover:drop-shadow-xl hover:scale-[1.01] transition-all ease-in duration-200">
                <Link :href="'/'" class="block">
                    <Logo
                        class="text-brand-primary w-[150px] laptop:w-[192px] h-[48px] laptop:h-[62px]" />
                </Link>
            </div>

            <!-- Desktop Navigation Links -->
            <div class="hidden tablet:flex items-center gap-x-8 laptop:gap-x-12">
                <NavLink v-for="link in links" :key="link.label" :href="link.path" :label="link.label" variant="desktop" />
            </div>

            <!-- Mobile Menu -->
            <MobileMenu class="tablet:hidden">
                <template #default="{ closeMenu }">
                    <NavLink v-for="link in links" :key="link.label" :href="link.path" :label="link.label" variant="mobile"
                        @click="closeMenu" />
                </template>
            </MobileMenu>
        </div>
    </nav>
</template>
