<script setup>
defineProps({
    text: String,
})
</script>
<template>
    <button
        class="bg-brand-primary hover:bg-nature-terracotta text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-ui-blue focus:ring-offset-2 disabled:opacity-40 disabled:cursor-not-allowed"
        >
        <slot></slot>
    </button>
</template>
