<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from "@inertiajs/vue3"
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import {
    XMarkIcon,
    Bars3Icon,
    ChevronRightIcon,
    ChevronDownIcon,
    HomeIcon,
    ChartBarIcon,
    GlobeAltIcon,
    MapPinIcon,
    CalendarDaysIcon,
    ArrowRightOnRectangleIcon,
    UserCircleIcon
} from '@heroicons/vue/24/outline'

const open = ref(false)
const page = usePage()

// Collapsible state voor menu groepen
const collapsedSections = ref({
    overview: false,
    content: false,
    bookings: false,
    general: false,
})

// Menu structuur met categorieën
const menuGroups = ref([
    {
        id: 'overview',
        label: 'Overzicht',
        items: [
            {
                label: 'Dashboard',
                path: new URL(route('admin.dashboard'), window.location.origin).pathname,
                icon: ChartBarIcon,
            },
        ]
    },
    {
        id: 'content',
        label: 'Content Management',
        items: [
            {
                label: 'Producten',
                path: new URL(route('admin.products.index'), window.location.origin).pathname,
                icon: GlobeAltIcon,
            },
            {
                label: 'Landen',
                path: new URL(route('admin.countries.index'), window.location.origin).pathname,
                icon: MapPinIcon,
            },
        ]
    },
    {
        id: 'bookings',
        label: 'Bookings & Orders',
        items: [
            {
                label: 'Boekingen',
                path: new URL(route('admin.bookings.index'), window.location.origin).pathname,
                icon: CalendarDaysIcon,
                badge: true, // Badge wordt getoond via adminStats
            },
        ]
    },
    {
        id: 'general',
        label: 'Algemeen',
        items: [
            {
                label: 'Home',
                path: new URL(route('home'), window.location.origin).pathname,
                icon: HomeIcon,
            },
            {
                label: 'Uitloggen',
                path: new URL(route('admin.logout'), window.location.origin).pathname,
                icon: ArrowRightOnRectangleIcon,
            },
        ]
    },
])

const isCurrentPage = (path) => {
    return page.url === path
}

const toggleSection = (sectionId) => {
    collapsedSections.value[sectionId] = !collapsedSections.value[sectionId]
}

// User gegevens ophalen uit shared Inertia data
const authUser = computed(() => page.props.auth?.user)
const adminStats = computed(() => page.props.adminStats)

// User initialen voor avatar
const userInitials = computed(() => {
    if (!authUser.value?.name) return 'A'
    const names = authUser.value.name.split(' ')
    if (names.length === 1) return names[0].charAt(0).toUpperCase()
    return (names[0].charAt(0) + names[names.length - 1].charAt(0)).toUpperCase()
})
</script>

<template>
    <div class="relative">
        <!-- Toggle knop - alleen zichtbaar als menu gesloten is -->
        <button
            v-show="!open"
            type="button"
            class="fixed top-6 right-6 phone:top-[50%] z-50 rounded-full bg-white p-3 text-gray-600 shadow-lg hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:ring-offset-2 transition-all duration-200 sm:top-8 sm:right-8"
            @click="open = true"
            aria-label="Open admin menu"
        >
            <Bars3Icon class="h-6 w-6" aria-hidden="true" />
        </button>

        <TransitionRoot as="template" :show="open">
            <Dialog class="relative z-20" @close="open = false">
                <!-- Backdrop -->
                <TransitionChild
                    as="template"
                    enter="ease-in-out duration-300"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="ease-in-out duration-300"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" />
                </TransitionChild>

                <div class="fixed inset-0 overflow-hidden">
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                            <TransitionChild
                                as="template"
                                enter="transform transition ease-in-out duration-300 sm:duration-500"
                                enter-from="translate-x-full"
                                enter-to="translate-x-0"
                                leave="transform transition ease-in-out duration-300 sm:duration-500"
                                leave-from="translate-x-0"
                                leave-to="translate-x-full"
                            >
                                <DialogPanel class="pointer-events-auto relative w-screen max-w-sm sm:max-w-md">
                                    <!-- Close button -->
                                    <div class="absolute top-6 right-4 z-20">
                                        <button
                                            type="button"
                                            class="rounded-full bg-white/10 p-2 text-gray-400 hover:bg-white/20 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-brand-primary shadow-sm backdrop-blur-sm transition-colors"
                                            @click="open = false"
                                            aria-label="Close admin menu"
                                        >
                                            <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                                        </button>
                                    </div>

                                    <!-- Menu content -->
                                    <div class="flex h-full flex-col overflow-y-auto bg-gradient-to-b from-brand-primary to-brand-dark shadow-2xl">
                                        <!-- Header met User Profile -->
                                        <div class="px-6 py-8 border-b border-white/10">
                                            <DialogTitle class="text-lg font-semibold text-white/90 mb-6">
                                                Admin Dashboard
                                            </DialogTitle>

                                            <!-- User Profile -->
                                            <div v-if="authUser" class="flex items-center space-x-3 p-3 rounded-lg bg-white/5 backdrop-blur-sm">
                                                <!-- Avatar -->
                                                <div class="flex-shrink-0">
                                                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-ui-gold to-nature-terracotta flex items-center justify-center text-white font-semibold text-sm shadow-lg">
                                                        {{ userInitials }}
                                                    </div>
                                                </div>
                                                <!-- User Info -->
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-white truncate">
                                                        {{ authUser.name }}
                                                    </p>
                                                    <p class="text-xs text-white/60 truncate">
                                                        {{ authUser.email }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Navigation -->
                                        <div class="relative flex-1 px-4 py-6">
                                            <nav class="space-y-6">
                                                <!-- Menu Groups -->
                                                <div
                                                    v-for="group in menuGroups"
                                                    :key="group.id"
                                                    class="space-y-1"
                                                >
                                                    <!-- Group Header -->
                                                    <button
                                                        @click="toggleSection(group.id)"
                                                        class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-white/50 uppercase tracking-wider hover:text-white/70 transition-colors group"
                                                    >
                                                        <span>{{ group.label }}</span>
                                                        <ChevronDownIcon
                                                            class="h-4 w-4 transition-transform duration-200"
                                                            :class="{ 'transform rotate-180': !collapsedSections[group.id] }"
                                                            aria-hidden="true"
                                                        />
                                                    </button>

                                                    <!-- Group Items -->
                                                    <TransitionRoot
                                                        :show="!collapsedSections[group.id]"
                                                        enter="transition ease-out duration-200"
                                                        enter-from="opacity-0 -translate-y-1"
                                                        enter-to="opacity-100 translate-y-0"
                                                        leave="transition ease-in duration-150"
                                                        leave-from="opacity-100 translate-y-0"
                                                        leave-to="opacity-0 -translate-y-1"
                                                    >
                                                        <div class="space-y-1">
                                                            <Link
                                                                v-for="(item, index) in group.items"
                                                                :key="index"
                                                                :href="item.path"
                                                                class="group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200"
                                                                :class="{
                                                                    'bg-white text-brand-primary shadow-lg shadow-white/20': isCurrentPage(item.path),
                                                                    'text-white/80 hover:bg-white/10 hover:text-white': !isCurrentPage(item.path)
                                                                }"
                                                                @click="open = false"
                                                            >
                                                                <!-- Icon -->
                                                                <component
                                                                    :is="item.icon"
                                                                    class="mr-3 h-5 w-5 flex-shrink-0"
                                                                    :class="{
                                                                        'text-brand-primary': isCurrentPage(item.path),
                                                                        'text-white/60 group-hover:text-white': !isCurrentPage(item.path)
                                                                    }"
                                                                    aria-hidden="true"
                                                                />

                                                                <!-- Label -->
                                                                <span class="flex-1">{{ item.label }}</span>

                                                                <!-- Badge (voor notifications) -->
                                                                <span
                                                                    v-if="item.badge && adminStats?.newBookingsCount > 0"
                                                                    class="ml-auto inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                                                    :class="{
                                                                        'bg-brand-primary text-white': isCurrentPage(item.path),
                                                                        'bg-nature-terracotta text-white': !isCurrentPage(item.path)
                                                                    }"
                                                                >
                                                                    {{ adminStats.newBookingsCount }}
                                                                </span>

                                                                <!-- Active Indicator -->
                                                                <ChevronRightIcon
                                                                    v-if="isCurrentPage(item.path)"
                                                                    class="ml-2 h-4 w-4 text-brand-primary"
                                                                    aria-hidden="true"
                                                                />
                                                            </Link>
                                                        </div>
                                                    </TransitionRoot>
                                                </div>
                                            </nav>
                                        </div>

                                        <!-- Footer -->
                                        <div class="border-t border-white/10 px-6 py-4">
                                            <p class="text-xs text-white/40 text-center">
                                                Admin Dashboard v1.0
                                            </p>
                                        </div>
                                    </div>
                                </DialogPanel>
                            </TransitionChild>
                        </div>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </div>
</template>
