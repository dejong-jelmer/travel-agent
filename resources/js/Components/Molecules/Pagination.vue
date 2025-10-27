<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    links: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <div v-if="links?.length > 3" class="flex flex-wrap -mb-1">
        <template v-for="(link, key) in links" :key="key">
            <span v-if="!link.url" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded"
                v-html="link.label" />
            <Link v-else :href="link.url" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded transition-colors"
                :class="[
                    link.active ? 'bg-white text-secondary-stone border-secondary-stone' : 'hover:bg-white hover:text-secondary-stone focus:border-secondary-stone focus:text-secondary-stone'
                ]" v-html="link.label" />
        </template>
    </div>
</template>
