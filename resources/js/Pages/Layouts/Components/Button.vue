<script setup>
defineProps({
    text: String,
})
</script>
<template>
    <button
        v-html="text"
        class="px-2 tablet:px-4 py-3 font-semibold text-base tablet:text-lg cursor-pointer select-none text-white bg-accent-gold rounded-lg transition-all duration-300 ease hover:bg-accent-earth focus:outline-none focus:ring-2 focus:ring-secondary-stone focus:ring-offset-2">
    </button>
</template>
