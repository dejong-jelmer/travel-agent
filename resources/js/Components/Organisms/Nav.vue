<script setup>
import { useI18n } from 'vue-i18n';
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from 'vue';
import { route } from 'ziggy-js';

const { t } = useI18n();
const page = usePage();

const navCountries = computed(() => page.props.navCountries ?? []);

// Dropdown items: "All trips" first, then one entry per country
const tripItems = computed(() => [
    { label: t('nav.all_trips'), href: route('trips') },
    ...navCountries.value.map(c => ({
        label: c.name,
        href: `${route('trips')}?land=${c.code}`,
    })),
]);

const links = computed(() => ({
    contact: {
        label: t('nav.contact'),
        path: route('contact'),
    },
    about: {
        label: t('nav.about'),
        path: route('about'),
    },
    blog: {
        label: t('nav.blog'),
        path: route('blog.index'),
    }
}));
</script>

<template>
    <nav class="bg-white backdrop-blur-lg border-b border-brand-accent py-2">
        <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto h-20 laptop:h-24 px-6 laptop:px-8 flex items-center justify-between">
            <!-- Logo -->
            <div class="hover:drop-shadow-xl hover:scale-[1.01] transition-all ease-in duration-200">
                <Link :href="'/'" class="block">
                    <Logo
                        class="w-[150px] laptop:w-[200px]" />
                </Link>
            </div>

            <!-- Desktop Navigation Links -->
            <div class="hidden tablet:flex items-center gap-x-8 laptop:gap-x-12">
                <NavDropdown
                    :label="$t('nav.trips')"
                    :href="route('trips')"
                    :items="tripItems"
                    variant="desktop"
                />
                <NavLink v-for="link in links" :key="link.label" :href="link.path" :label="link.label" variant="desktop" />
            </div>

            <!-- Mobile Menu -->
            <MobileMenu class="tablet:hidden">
                <template #default="{ closeMenu }">
                    <NavDropdown
                        :label="$t('nav.trips')"
                        :href="route('trips')"
                        :items="tripItems"
                        variant="mobile"
                        @close="closeMenu"
                    />
                    <NavLink v-for="link in links" :key="link.label" :href="link.path" :label="link.label" variant="mobile"
                        @click="closeMenu" />
                </template>
            </MobileMenu>
        </div>
    </nav>
</template>
