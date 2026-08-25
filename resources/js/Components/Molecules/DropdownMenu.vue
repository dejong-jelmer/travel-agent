<script setup>
import { computed, ref } from 'vue';
import { EllipsisVertical } from 'lucide-vue-next';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue';
import { useElementBounding, useWindowSize } from '@vueuse/core';

// This menu is located in the horizontally scrolling container of the DataTable.
// An absolutely positioned menu is truncated there, so we position it fixed relative to the viewport.
const button = ref(null);
const buttonEl = computed(() => button.value?.$el ?? null);
const { top, bottom, left } = useElementBounding(buttonEl);
const { height: viewportHeight } = useWindowSize();

// Flip up when the button is in the bottom half of the screen.
const opensUpward = computed(() => bottom.value > viewportHeight.value / 2);

const menuStyle = computed(() => ({
    left: `${left.value - 8}px`,
    ...(opensUpward.value
        ? { bottom: `${viewportHeight.value - top.value + 8}px` }
        : { top: `${bottom.value + 8}px` }),
}));
</script>
<template>
    <div class="relative w-fit mx-auto">
        <Menu as="div" class="relative w-fit mx-auto">
            <MenuButton ref="button" class="info-button">
                <EllipsisVertical class="h-5" />
            </MenuButton>

            <MenuItems
                :style="menuStyle"
                class="fixed z-50 space-y-2 bg-white p-2 border border-slate-700 rounded-lg shadow-lg"
            >
                 <slot :MenuItem="MenuItem" />
            </MenuItems>
        </Menu>
    </div>
</template>
