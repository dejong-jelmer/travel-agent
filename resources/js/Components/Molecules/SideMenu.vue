<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from "@inertiajs/vue3"
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { XMarkIcon, Bars3Icon, ChevronRightIcon } from '@heroicons/vue/24/outline'

const open = ref(false)
const page = usePage()

const menuItems = ref([
    { label: "Home", path: new URL(route('home'), window.location.origin).pathname },
    { label: "Dashboard", path: new URL(route('admin.dashboard'), window.location.origin).pathname },
    { label: "Producten", path: new URL(route('admin.products.index'), window.location.origin).pathname },
    { label: "Landen", path: new URL(route('admin.countries.index'), window.location.origin).pathname },
    { label: "Boekingen", path: new URL(route('admin.bookings.index'), window.location.origin).pathname },
    { label: "Loguit", path: new URL(route('admin.logout'), window.location.origin).pathname },
])

const isCurrentPage = (path) => {
    return page.url === path
}
</script>
<template>
    <div class="relative">
        <!-- Toggle knop - alleen zichtbaar als menu gesloten is -->
        <button
            v-show="!open"
            type="button"
            class="fixed top-6 right-6 phone:top-[50%] z-50 rounded-full bg-white p-3 text-gray-600 shadow-lg hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 sm:top-8 sm:right-8"
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
                    <div class="fixed inset-0 bg-gray-500/75 transition-opacity" />
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
                                    <!-- Close button binnen het menu -->
                                    <div class="absolute top-6 right-4 z-20">
                                        <button
                                            type="button"
                                            class="rounded-full bg-white p-2 text-gray-400 hover:bg-gray-50 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-sm"
                                            @click="open = false"
                                            aria-label="Close admin menu"
                                        >
                                            <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                                        </button>
                                    </div>

                                    <!-- Menu content -->
                                    <div class="flex h-full flex-col overflow-y-auto bg-white shadow-xl">
                                        <!-- Header -->
                                        <div class="bg-gray-50 px-4 py-6 sm:px-6">
                                            <div class="flex items-center justify-between">
                                                <DialogTitle class="text-lg font-semibold text-gray-900">
                                                    Admin Menu
                                                </DialogTitle>
                                            </div>
                                        </div>

                                        <!-- Navigation -->
                                        <div class="relative flex-1 px-4 py-6 sm:px-6">
                                            <nav class="space-y-2">
                                                <Link
                                                    v-for="(item, index) in menuItems"
                                                    :key="index"
                                                    :href="item.path"
                                                    class="group flex items-center rounded-lg px-4 py-3 text-sm font-medium text-gray-700 transition-colors duration-200 hover:bg-indigo-50 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                                    :class="{
                                                        'bg-indigo-100 text-indigo-700 font-semibold': isCurrentPage(item.path),
                                                        'hover:bg-gray-100': !isCurrentPage(item.path)
                                                    }"
                                                    @click="open = false"
                                                >
                                                    <span class="flex-1">{{ item.label }}</span>
                                                    <ChevronRightIcon
                                                        v-if="isCurrentPage(item.path)"
                                                        class="ml-2 h-4 w-4 text-indigo-500"
                                                        aria-hidden="true"
                                                    />
                                                </Link>
                                            </nav>
                                        </div>

                                        <!-- Footer (optioneel) -->
                                        <div class="border-t border-gray-200 px-4 py-4 sm:px-6">
                                            <p class="text-xs text-gray-500">
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
