<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { Logo } from "@/Pages/Icons";

const user = usePage().props.auth?.user ?? {};
</script>

<template>
    <nav class="max-w-screen-desktop mx-auto h-24 px-6 flex items-center justify-between">
        <!-- Logo -->
        <div class="min-w-[80px] w-[80px] ">
            <Transition>
                <div class="text-xl font-bold" v-bind="$attrs">
                    <Link :href="links.home.href">
                        <Logo class="w-[192] h-[62px]" />
                    </Link>
                </div>
            </Transition>
        </div>

        <!-- Navigation Links -->
        <div v-bind="$attrs" class="hidden tablet:flex gap-x-10 font-bold text-xl">
            <Link :href="links.blog.href" class="p-3 border-2 border-transparent hover:border-contrast-pink rounded-md transition-all duration-300 ease">{{ links.blog.name }}</Link>
            <Link :href="links.about.href" class="p-3 border-2 border-transparent hover:border-contrast-pink rounded-md transition-all duration-300 ease">{{ links.about.name }}</Link>
            <Link :href="links.contact.href" class="p-3 border-2 border-transparent hover:border-contrast-pink rounded-md transition-all duration-300 ease">{{ links.contact.name }}</Link>
            <template v-if="!!user.id">
                <Link :href="links.logout.href" class="p-3 border-2 border-transparent hover:border-contrast-pink rounded-md transition-all duration-300 ease">{{ links.logout.name }}</Link>
            </template>
        </div>

        <!-- Mobile Menu -->
        <Transition>
            <div v-if="isMenuOpen"
                class="bg-transparent tablet:hidden flex flex-col mr-2 items-end space-y-1">
                <Link :href="links.home.href" class="px-10 py-1 border-2 rounded-md bg-white text-primary-green border-primary-green">{{ links.home.name }}</Link>
                <Link :href="links.about.href" class="px-10 py-1 border-2 rounded-md bg-white text-primary-green border-primary-green">{{ links.about.name }}</Link>
                <Link :href="links.contact.href" class="px-10 py-1 border-2 rounded-md bg-white text-primary-green border-primary-green">{{ links.contact.name }}</Link>
                <template v-if="!!user.id">
                    <Link :href="links.logout.href" class="px-10 py-1 border-2 rounded-md bg-white text-primary-green border-primary-green">{{ links.logout.name }}</Link>
                </template>
            </div>
        </Transition>
        <!-- Mobile Menu Button -->
        <button @click="toggleMenu" class="tablet:hidden text-contrast-blue">
            <div class="w-6 h-0.5 bg-contrast-blue transition-transform transform"
                :class="{ 'rotate-45 translate-y-2': isMenuOpen }"></div>
            <div class="w-6 h-0.5 bg-contrast-blue mt-1.5 transition-opacity" :class="{ 'opacity-0': isMenuOpen }"></div>
            <div class="w-6 h-0.5 bg-contrast-blue mt-1.5 transition-transform transform"
                :class="{ '-rotate-45 -translate-y-2': isMenuOpen }"></div>
        </button>


    </nav>
</template>

<script>
export default {
    data() {
        return {
            logo: "images/logo/logo.png",
            isMenuOpen: false,
            links: {
                home: {
                    name: 'Home',
                    href: '/',
                },
                blog: {
                    name: 'Blog',
                    href: '#',
                },
                contact: {
                    name: 'Contact',
                    href: '#contact',
                },
                about: {
                    name: 'Over mij',
                    href: '/over-mij',
                },
                logout: {
                    name: 'Log uit',
                    href: '/admin/logout',
                }
            }
        };
    },
    methods: {
        toggleMenu() {
            this.isMenuOpen = !this.isMenuOpen;
        },
    },
};
</script>
<style scoped>
    .v-enter-active,
    .v-leave-active {
        transition: opacity 0.5s ease;
    }

    .v-enter-from,
    .v-leave-to {
        opacity: 0;
    }
</style>
