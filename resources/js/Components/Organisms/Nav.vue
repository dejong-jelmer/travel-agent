<script setup>
import { useI18n } from 'vue-i18n';
import { Link, usePage } from "@inertiajs/vue3";
import { computed, ref, onMounted, onBeforeUnmount } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps({
    // The nav floats over a hero image, so it uses white menu items while transparent
    overHero: { type: Boolean, default: false },
});

const { t } = useI18n();
const page = usePage();

const scrolled = ref(false);
const light = computed(() => props.overHero && !scrolled.value);
const updateScrolled = () => {
    scrolled.value = window.scrollY > 0;
};

onMounted(() => {
    updateScrolled();
    window.addEventListener('scroll', updateScrolled, { passive: true });
});
onBeforeUnmount(() => window.removeEventListener('scroll', updateScrolled));

const navCountries = computed(() => page.props.navCountries ?? []);

// Dropdown items: "All trips" first, then one entry per country
const tripItems = computed(() => [
    { label: t('nav.all_trips'), href: route('trips.index') },
    ...navCountries.value.map(c => ({
        label: c.name,
        href: `${route('trips.index')}?land=${c.code}`,
    })),
]);

// "More" dropdown: legal/informational links (mirrors the footer)
const moreItems = computed(() => [
    { label: t('footer.conditions'), href: route('terms') },
    { label: t('footer.privacy'), href: route('privacy') },
    { label: t('footer.guarantee'), href: route('guarantee') },
    { label: t('footer.vvkr'), href: route('vvkr') },
    { label: t('footer.sustainability'), href: route('sustainability'), download: true },
]);

const links = computed(() => ({
    home: {
        label: t('nav.home'),
        path: route('home'),
    },
    trips: {
        label: t('nav.trips'),
        path: route('trips.index'),
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
    <nav class="relative py-2 transition-colors duration-300"
        :class="scrolled ? 'bg-white border-b border-brand-accent' : 'bg-transparent'">
        <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto h-20 laptop:h-24 px-6 laptop:px-8 flex items-center justify-between">
            <!-- Logo -->
            <div class="hover:drop-shadow-xl hover:scale-[1.01] transition-all ease-in duration-200">
                <Link :href="route('home')" class="block">
                    <LogoWhite v-if="light"
                        class="w-[150px] laptop:w-[200px] h-auto" />
                    <Logo v-else
                        class="w-[150px] laptop:w-[200px]" />
                </Link>
            </div>

            <!-- Desktop Navigation Links -->
            <div class="hidden tablet:flex items-center gap-x-8 laptop:gap-x-12">
                <NavLink v-for="link in links" v-show="link.path !== route('home')" :key="link.label" :href="link.path" :label="link.label" variant="desktop" :light="light" />
                <NavDropdown :label="$t('nav.more')" :items="moreItems" variant="desktop" :light="light" />
            </div>

            <!-- Mobile Menu -->
            <MobileMenu class="tablet:hidden" :light="light">
                <template #default="{ closeMenu }">
                    <NavLink v-for="link in links" :key="link.label" :href="link.path" :label="link.label" variant="mobile"
                        @click="closeMenu" />
                    <NavDropdown :label="$t('nav.more')" :items="moreItems" variant="mobile" @close="closeMenu" />
                </template>
            </MobileMenu>
        </div>
    </nav>
</template>
