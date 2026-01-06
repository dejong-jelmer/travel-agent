<script setup>
import { Link } from '@inertiajs/vue3';
import { Head, usePage } from '@inertiajs/vue3';
import { useToastWatcher } from '@/Composables/useToastWatcher.js';
import { watchEffect, computed, ref } from 'vue';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue';
import { Bars3Icon, ArrowRightStartOnRectangleIcon, ChevronDownIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    title: String,
    phone: Object
});

const user = usePage().props.auth?.user ?? {};
const flash = usePage().props.flash ?? {};
const breadcrumbs = usePage().props.breadcrumbs ?? {};
const sideMenuOpen = ref(false);

// User initialen voor avatar (overgenomen van SideMenu)
const userInitials = computed(() => {
    if (!user?.name) return 'A';
    const names = user.name.split(' ');
    if (names.length === 1) return names[0].charAt(0).toUpperCase();
    return (names[0].charAt(0) + names[names.length - 1].charAt(0)).toUpperCase();
});

watchEffect(() => {
    document.title = usePage().props.title || `${window.appName} - Admin`;
});

Object.entries(flash).forEach(([type, message]) => {
    useToastWatcher(message, type);
});
</script>

<template v-if="!!user.id">
    <Head :title="title" />

    <!-- Admin Topbar -->
    <header class="fixed top-0 left-0 right-0 z-20 bg-white border-b border-gray-200 shadow-sm">
        <div class="px-4 laptop:px-10">
            <div class="flex items-center justify-between laptop:justify-end h-16">
                <!-- Left: Menu Toggle Button (only visible on phone/tablet) -->
                <button
                    type="button"
                    @click="sideMenuOpen = true"
                    class="laptop:hidden rounded-lg p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-primary transition-colors"
                    aria-label="Open admin menu"
                >
                    <Bars3Icon class="h-6 w-6" aria-hidden="true" />
                </button>

                <!-- Right: User Info with Dropdown -->
                <Menu as="div" class="relative">
                    <MenuButton class="flex items-center space-x-3 rounded-lg px-3 py-2 hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-brand-primary">
                        <!-- Avatar -->
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-accent-primary to-accent-terracotta flex items-center justify-center text-white font-semibold text-sm shadow-md">
                            {{ userInitials }}
                        </div>
                        <!-- User Info (hidden on small screens) -->
                        <div class="hidden tablet:block text-left">
                            <p class="text-sm font-medium text-gray-700">
                                {{ user.name }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ user.email }}
                            </p>
                        </div>
                        <!-- Chevron -->
                        <ChevronDownIcon class="hidden tablet:block h-4 w-4 text-gray-400" aria-hidden="true" />
                    </MenuButton>

                    <!-- Dropdown Menu -->
                    <transition
                        enter-active-class="transition ease-out duration-100"
                        enter-from-class="transform opacity-0 scale-95"
                        enter-to-class="transform opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-75"
                        leave-from-class="transform opacity-100 scale-100"
                        leave-to-class="transform opacity-0 scale-95"
                    >
                        <MenuItems class="absolute right-0 mt-2 w-56 origin-top-right rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                            <div class="py-1">
                                <!-- User info for mobile -->
                                <div class="tablet:hidden px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-700">
                                        {{ user.name }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ user.email }}
                                    </p>
                                </div>

                                <!-- Uitloggen -->
                                <MenuItem v-slot="{ active }">
                                    <Link
                                        :href="route('admin.logout')"
                                        :class="[
                                            active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                                            'group flex items-center px-4 py-2 text-sm'
                                        ]"
                                    >
                                        <ArrowRightStartOnRectangleIcon
                                            class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500"
                                            aria-hidden="true"
                                        />
                                        {{ $t('admin_menu.items.logout') }}
                                    </Link>
                                </MenuItem>
                            </div>
                        </MenuItems>
                    </transition>
                </Menu>
                <LocaleSwitcher />
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-16 pr-2 laptop:pr-8">
        <section class="grid grid-cols-1 laptop:grid-cols-4 gap-8">
            <!-- Sidebar in kolom 1 (alleen zichtbaar op laptop+) -->
            <SideMenu v-model:open="sideMenuOpen" class="hidden laptop:block laptop:col-start-1" />

            <!-- Content in kolom 2-4 -->
            <div class="laptop:col-start-2 laptop:col-span-3 p-4">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
                <slot></slot>
            </div>
        </section>
    </main>
</template>
