<script setup>
    defineProps({
        text:  String,
    })
</script>
<template>
    <button
        class="px-8 py-3 font-semibold text-lg cursor-pointer select-none text-white bg-ochre-yellow rounded-lg transition-all duration-300 ease hover:bg-warm-terracotta focus:outline-none focus:ring-2 focus:ring-deep-green focus:ring-offset-2"
    >
        {{ text }}
    </button>
</template>
