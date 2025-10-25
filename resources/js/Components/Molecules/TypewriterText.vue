<template>
    <span>{{ output }}</span>
</template>

<script setup>
import { useTypeWriter } from "@/Composables/useTypeWriter.js";

const props = defineProps({
    texts: {
        type: Array,
        required: true
    }
});

const { output } = useTypeWriter(props.texts);
</script>
