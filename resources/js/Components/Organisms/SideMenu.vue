<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from "@inertiajs/vue3"
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { LayoutPanelTop } from 'lucide-vue-next'
import {
    XMarkIcon,
    ChevronRightIcon,
    ChevronDownIcon,
    HomeIcon,
    ChartBarIcon,
    GlobeAltIcon,
    MapPinIcon,
    CalendarDaysIcon,
    ArrowRightStartOnRectangleIcon,
    PlusIcon
} from '@heroicons/vue/24/outline'

const open = defineModel('open', { type: Boolean, default: false })
const page = usePage()
const adminStats = page.props.adminStats ?? {};

// Collapsible state voor menu groepen
const collapsedSections = ref({
    overview: false,
    content: false,
    bookings: false,
    general: false,
})

// Collapsible state voor items met sub-items
const collapsedItems = ref({
    products: false,
    countries: false,
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
                id: 'products',
                label: 'Producten',
                icon: LayoutPanelTop,
                children: [
                    {
                        label: 'Alle Producten',
                        path: new URL(route('admin.products.index'), window.location.origin).pathname,
                        icon: GlobeAltIcon,
                    },
                    {
                        label: 'Nieuw Product',
                        path: new URL(route('admin.products.create'), window.location.origin).pathname,
                        icon: PlusIcon,
                    },
                ]
            },
            {
                id: 'countries',
                label: 'Landen',
                icon: MapPinIcon,
                children: [
                    {
                        label: 'Alle Landen',
                        path: new URL(route('admin.countries.index'), window.location.origin).pathname,
                        icon: MapPinIcon,
                    },
                    {
                        label: 'Nieuw Land',
                        path: new URL(route('admin.countries.create'), window.location.origin).pathname,
                        icon: PlusIcon,
                    },
                ]
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
                icon: ArrowRightStartOnRectangleIcon,
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

const toggleItem = (itemId) => {
    collapsedItems.value[itemId] = !collapsedItems.value[itemId]
}

// Stats voor badge counts
const stats = computed(() => adminStats)
</script>

<template>
    <div class="relative">
        <!-- Desktop Sidebar (sticky, altijd zichtbaar op laptop+) -->
        <aside class="hidden laptop:block sticky top-16 h-[calc(100vh-4rem)] overflow-y-auto bg-gradient-to-b from-brand-primary to-brand-primary shadow-xl">
            <div class="flex flex-col h-full">
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
                                    <template v-for="(item, index) in group.items" :key="index">
                                        <!-- Item with children (collapsible) -->
                                        <div v-if="item.children" class="space-y-1">
                                            <button
                                                @click="toggleItem(item.id)"
                                                class="w-full group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 text-white/80 hover:bg-white/10 hover:text-white"
                                            >
                                                <!-- Icon -->
                                                <component
                                                    :is="item.icon"
                                                    class="mr-3 h-5 w-5 flex-shrink-0 text-white/60 group-hover:text-white"
                                                    aria-hidden="true"
                                                />
                                                <!-- Label -->
                                                <span class="flex">{{ item.label }}</span>
                                                <!-- Chevron -->
                                                <ChevronDownIcon
                                                    class="ml-2 h-4 w-4 transition-transform duration-200"
                                                    :class="{ 'transform rotate-180': !collapsedItems[item.id] }"
                                                    aria-hidden="true"
                                                />
                                            </button>

                                            <!-- Sub-items -->
                                            <TransitionRoot
                                                :show="!collapsedItems[item.id]"
                                                enter="transition ease-out duration-200"
                                                enter-from="opacity-0 -translate-y-1"
                                                enter-to="opacity-100 translate-y-0"
                                                leave="transition ease-in duration-150"
                                                leave-from="opacity-100 translate-y-0"
                                                leave-to="opacity-0 -translate-y-1"
                                            >
                                                <div class="ml-6 space-y-1">
                                                    <Link
                                                        v-for="(child, childIndex) in item.children"
                                                        :key="childIndex"
                                                        :href="child.path"
                                                        class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200"
                                                        :class="{
                                                            'bg-white text-brand-primary shadow-lg shadow-white/20': isCurrentPage(child.path),
                                                            'text-white/70 hover:bg-white/10 hover:text-white': !isCurrentPage(child.path)
                                                        }"
                                                    >
                                                        <!-- Icon -->
                                                        <component
                                                            :is="child.icon"
                                                            class="mr-3 h-4 w-4 flex-shrink-0"
                                                            :class="{
                                                                'text-brand-primary': isCurrentPage(child.path),
                                                                'text-white/50 group-hover:text-white': !isCurrentPage(child.path)
                                                            }"
                                                            aria-hidden="true"
                                                        />
                                                        <!-- Label -->
                                                        <span class="flex-1">{{ child.label }}</span>
                                                        <!-- Active Indicator -->
                                                        <ChevronRightIcon
                                                            v-if="isCurrentPage(child.path)"
                                                            class="ml-2 h-3 w-3 text-brand-primary"
                                                            aria-hidden="true"
                                                        />
                                                    </Link>
                                                </div>
                                            </TransitionRoot>
                                        </div>

                                        <!-- Regular item (no children) -->
                                        <Link
                                            v-else
                                            :href="item.path"
                                            class="group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200"
                                            :class="{
                                                'bg-white text-brand-primary shadow-lg shadow-white/20': isCurrentPage(item.path),
                                                'text-white/80 hover:bg-white/10 hover:text-white': !isCurrentPage(item.path)
                                            }"
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
                                                v-if="item.badge && stats?.newBookingsCount > 0"
                                                class="ml-auto inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                                :class="{
                                                    'bg-brand-primary text-white': isCurrentPage(item.path),
                                                    'bg-accent-terracotta text-white': !isCurrentPage(item.path)
                                                }"
                                            >
                                                {{ stats.newBookingsCount }}
                                            </span>

                                            <!-- Active Indicator -->
                                            <ChevronRightIcon
                                                v-if="isCurrentPage(item.path)"
                                                class="ml-2 h-4 w-4 text-brand-primary"
                                                aria-hidden="true"
                                            />
                                        </Link>
                                    </template>
                                </div>
                            </TransitionRoot>
                        </div>
                    </nav>
                </div>

                <!-- Footer (desktop) -->
                <div class="border-t border-white/10 px-6 py-4">
                    <p class="text-xs text-white/40 text-center">
                        Admin Dashboard v1.0
                    </p>
                </div>
            </div>
        </aside>

        <!-- Mobile Dialog (alleen op phone/tablet) -->
        <TransitionRoot as="template" :show="open" class="laptop:hidden">
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
                    <div class="fixed inset-0 bg-gray-900/50 transition-opacity" />
                </TransitionChild>

                <div class="fixed inset-0 overflow-hidden">
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="pointer-events-none fixed inset-y-0 left-0 flex max-w-[80%] pr-10 sm:pr-16">
                            <TransitionChild
                                as="template"
                                enter="transform transition ease-in-out duration-300 sm:duration-500"
                                enter-from="-translate-x-full"
                                enter-to="translate-x-0"
                                leave="transform transition ease-in-out duration-300 sm:duration-500"
                                leave-from="translate-x-0"
                                leave-to="-translate-x-full"
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
                                    <div class="flex h-full flex-col overflow-y-auto bg-gradient-to-b from-brand-primary to-brand-primary shadow-2xl">
                                        <!-- Header -->
                                        <div class="px-6 py-8 border-b border-white/10">
                                            <DialogTitle class="text-lg font-semibold text-white/90">
                                                Admin Dashboard
                                            </DialogTitle>
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
                                                            <template v-for="(item, index) in group.items" :key="index">
                                                                <!-- Item with children (collapsible) -->
                                                                <div v-if="item.children" class="space-y-1">
                                                                    <button
                                                                        @click="toggleItem(item.id)"
                                                                        class="w-full group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 text-white/80 hover:bg-white/10 hover:text-white"
                                                                    >
                                                                        <!-- Icon -->
                                                                        <component
                                                                            :is="item.icon"
                                                                            class="mr-3 h-5 w-5 flex-shrink-0 text-white/60 group-hover:text-white"
                                                                            aria-hidden="true"
                                                                        />

                                                                        <!-- Label -->
                                                                        <span class="flex-1 text-left">{{ item.label }}</span>

                                                                        <!-- Chevron -->
                                                                        <ChevronDownIcon
                                                                            class="h-4 w-4 transition-transform duration-200"
                                                                            :class="{ 'transform rotate-180': !collapsedItems[item.id] }"
                                                                            aria-hidden="true"
                                                                        />
                                                                    </button>

                                                                    <!-- Sub-items -->
                                                                    <TransitionRoot
                                                                        :show="!collapsedItems[item.id]"
                                                                        enter="transition ease-out duration-200"
                                                                        enter-from="opacity-0 -translate-y-1"
                                                                        enter-to="opacity-100 translate-y-0"
                                                                        leave="transition ease-in duration-150"
                                                                        leave-from="opacity-100 translate-y-0"
                                                                        leave-to="opacity-0 -translate-y-1"
                                                                    >
                                                                        <div class="ml-6 space-y-1">
                                                                            <Link
                                                                                v-for="(child, childIndex) in item.children"
                                                                                :key="childIndex"
                                                                                :href="child.path"
                                                                                class="group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200"
                                                                                :class="{
                                                                                    'bg-white text-brand-primary shadow-lg shadow-white/20': isCurrentPage(child.path),
                                                                                    'text-white/80 hover:bg-white/10 hover:text-white': !isCurrentPage(child.path)
                                                                                }"
                                                                                @click="open = false"
                                                                            >
                                                                                <!-- Icon (smaller for sub-items) -->
                                                                                <component
                                                                                    :is="child.icon"
                                                                                    class="mr-3 h-4 w-4 flex-shrink-0"
                                                                                    :class="{
                                                                                        'text-brand-primary': isCurrentPage(child.path),
                                                                                        'text-white/60 group-hover:text-white': !isCurrentPage(child.path)
                                                                                    }"
                                                                                    aria-hidden="true"
                                                                                />

                                                                                <!-- Label -->
                                                                                <span class="flex-1">{{ child.label }}</span>

                                                                                <!-- Active Indicator -->
                                                                                <ChevronRightIcon
                                                                                    v-if="isCurrentPage(child.path)"
                                                                                    class="ml-2 h-4 w-4 text-brand-primary"
                                                                                    aria-hidden="true"
                                                                                />
                                                                            </Link>
                                                                        </div>
                                                                    </TransitionRoot>
                                                                </div>

                                                                <!-- Regular item (no children) -->
                                                                <Link
                                                                    v-else
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
                                                                        v-if="item.badge && stats?.newBookingsCount > 0"
                                                                        class="ml-auto inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                                                        :class="{
                                                                            'bg-brand-primary text-white': isCurrentPage(item.path),
                                                                            'bg-accent-terracotta text-white': !isCurrentPage(item.path)
                                                                        }"
                                                                    >
                                                                        {{ stats.newBookingsCount }}
                                                                    </span>

                                                                    <!-- Active Indicator -->
                                                                    <ChevronRightIcon
                                                                        v-if="isCurrentPage(item.path)"
                                                                        class="ml-2 h-4 w-4 text-brand-primary"
                                                                        aria-hidden="true"
                                                                    />
                                                                </Link>
                                                            </template>
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
