<script setup>
defineProps({
    message: {
        type: [ String, Array ],
        required: false,
    },
});
</script>
<template>
    <template v-if="message">
        <p class="text-sm mx-4 mt-2">
            <span class="text-red-500">
                {{ !Array.isArray(message) ? message : message[0] }}
            </span>
        </p>
    </template>
</template>
