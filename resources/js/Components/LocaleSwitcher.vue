<script setup>
import { computed } from 'vue'
import { useLocale } from '@/Composables/useLocale.js'
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue';
import { ChevronDownIcon, LanguageIcon } from '@heroicons/vue/24/outline';

const { currentLocale, switchLocale, availableLocales } = useLocale()

// Get the locale codes (keys) from the availableLocales object
const localeKeys = computed(() => Object.keys(availableLocales.value))

const getLocaleName = (localeCode) => {
    return availableLocales.value[localeCode] || localeCode
}

</script>
<template>
    <Menu as="div" class="relative ml-3">
        <MenuButton class="flex items-center space-x-2 rounded-lg px-3 py-2 hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-brand-primary">
            <LanguageIcon class="h-5 w-5 text-gray-600" aria-hidden="true" />
            <span class="hidden tablet:block text-sm font-medium text-gray-700">
                {{ getLocaleName(currentLocale) }}
            </span>
            <ChevronDownIcon class="h-4 w-4 text-gray-400" aria-hidden="true" />
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
            <MenuItems class="absolute right-0 mt-2 w-48 origin-top-right rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-[100]">
                <div class="py-1">
                    <MenuItem v-for="locale in localeKeys" :key="locale" v-slot="{ active }">
                        <button
                            @click="switchLocale(locale)"
                            :class="[
                                active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                                currentLocale === locale ? 'font-semibold' : '',
                                'group flex items-center w-full px-4 py-2 text-sm'
                            ]"
                        >
                            {{ getLocaleName(locale) }}
                            <span v-if="currentLocale === locale" class="ml-auto text-brand-primary">✓</span>
                        </button>
                    </MenuItem>
                </div>
            </MenuItems>
        </transition>
    </Menu>
</template>
