<script setup>
import { EllipsisVertical } from 'lucide-vue-next';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue';

</script>
<template>
    <div class="relative w-fit mx-auto">
        <Menu as="div" class="relative w-fit mx-auto">
            <MenuButton class="info-button">
                <EllipsisVertical class="h-5" />
            </MenuButton>

            <MenuItems class="absolute z-50 space-y-2 bg-white mt-2 -left-2 p-2 border border-slate-700 rounded-lg shadow-lg">
                 <slot :MenuItem="MenuItem" />
            </MenuItems>
        </Menu>
    </div>
</template>
