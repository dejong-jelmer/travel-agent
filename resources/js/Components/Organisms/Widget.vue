<template>
    <component :is="link ? Link : 'div'" :href="link">
        <div class="flex flex-col justify-between p-4 bg-white rounded-2xl shadow-md w-48 h-48">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-medium text-primary-dark">{{ title }}</h3>
                <slot name="icon"></slot>
            </div>
            <p class="text-3xl font-bold text-primary-default mx-auto h-full content-center">{{ value }}</p>
            <p v-if="subtitle" class="text-xs text-secondary-stone">{{ subtitle }}</p>
        </div>
    </component>
</template>

<script setup>
import { Link } from "@inertiajs/inertia-vue3";

defineProps({
    title: String,
    value: [String, Number],
    subtitle: String,
    link: {
        type: String,
        require: false
    }
})
</script>
