<script setup>
    defineProps({
        text:  String,
    })
</script>
<template>
    <button
        class="px-8 py-3 font-semibold text-lg cursor-pointer select-none text-white bg-contrast-pink accent-earth rounded-lg transition-all duration-300 ease hover:bg-secondary-blue focus:outline-none focus:ring-2 focus:ring-secondary-blue focus:ring-offset-2"
    >
        {{ text }}
    </button>
</template>
