<template>
    <div class="relative">
        <!-- Onzichtbaar detectiegebied aan de rechterkant -->
        <div class="fixed top-0 right-0 h-full w-10" @mouseenter="open = true"></div>

        <TransitionRoot as="template" :show="open">
            <Dialog class="relative z-10" @close="open = false">
                <TransitionChild as="template" enter="ease-in-out duration-500" enter-from="opacity-0"
                    enter-to="opacity-100" leave="ease-in-out duration-500" leave-from="opacity-100"
                    leave-to="opacity-0">
                    <div class="fixed inset-0 bg-gray-500/75 transition-opacity" />
                </TransitionChild>

                <div class="fixed inset-0 overflow-hidden">
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                            <TransitionChild as="template"
                                enter="transform transition ease-in-out duration-500 sm:duration-700"
                                enter-from="translate-x-full" enter-to="translate-x-0"
                                leave="transform transition ease-in-out duration-500 sm:duration-700"
                                leave-from="translate-x-0" leave-to="translate-x-full">
                                <DialogPanel class="pointer-events-auto relative w-screen max-w-md">
                                    <TransitionChild as="template" enter="ease-in-out duration-500"
                                        enter-from="opacity-0" enter-to="opacity-100" leave="ease-in-out duration-500"
                                        leave-from="opacity-100" leave-to="opacity-0">
                                        <div class="absolute top-0 left-0 -ml-8 flex pt-4 pr-2 sm:-ml-10 sm:pr-4">
                                            <button type="button"
                                                class="relative rounded-md text-gray-300 hover:text-white focus:ring-2 focus:ring-white focus:outline-hidden"
                                                @click="open = false">
                                                <span class="absolute -inset-2.5" />
                                                <span class="sr-only">Close panel</span>
                                                <XMarkIcon class="size-6" aria-hidden="true" />
                                            </button>
                                        </div>
                                    </TransitionChild>
                                    <div class="flex h-full flex-col overflow-y-scroll bg-white py-6 shadow-xl">
                                        <div class="px-4 sm:px-6">
                                            <DialogTitle class="text-base font-semibold text-gray-900">Admin menu
                                            </DialogTitle>
                                        </div>
                                        <div class="relative mt-6 flex-1 px-4 sm:px-6">
                                            <nav class="mt-4 grid gap-y-2">
                                                <Link v-for="(item, index) in menuItems"
                                                    :key="index"
                                                    :href="item.path"
                                                    class="block px-4 py-2 text-gray-700 rounded-lg hover:bg-custom-primary focus:outline-none focus:ring-2 focus:ring-gray-300"
                                                    :class="{ 'block px-4 py-2 font-semibold bg-gray-100 rounded-lg': $page.url === item.path }"
                                                    >
                                                    {{ item.label }}
                                                </Link>
                                            </nav>
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

<script setup>
import { ref } from 'vue'
import { Link } from "@inertiajs/vue3";
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'

const open = ref(false);
const current = window.location.href;
const prod = route().current('products.index');
const dash = route().current('admin.dashboard');
const menuItems = ref([
        { label: "Producten", path: new URL(route('products.index'), window.location.origin).pathname },
        { label: "Dashboard", path: new URL(route('admin.dashboard'), window.location.origin).pathname },
    ]);

</script>
<script>
export default {
    components: { Link }
};
</script>
