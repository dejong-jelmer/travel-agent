<script setup>
defineProps({
    text: String,
})
</script>
<template>
    <button
        class="bg-primary-default hover:bg-accent-terracotta text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-secondary-stone focus:ring-offset-2 disabled:opacity-40 disabled:cursor-not-allowed"
        >
        <slot></slot>
    </button>
</template>
