<script setup>
import { ChevronDown } from 'lucide-vue-next';
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue';

</script>

<template>
    <Disclosure v-slot="{ open }" defaultOpen>
        <div class="border border-gray-200 rounded-lg overflow-hidden">
            <DisclosureButton
                class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100 transition-colors">
                <span class="font-medium text-gray-700">
                    <slot name="header"></slot>
                </span>
                <ChevronDown :class="open ? 'rotate-180' : ''" class="w-5 h-5 text-gray-500 transition-transform" />
            </DisclosureButton>
            <DisclosurePanel class="p-4 bg-white">
                <slot></slot>
            </DisclosurePanel>
        </div>
    </Disclosure>
</template>
