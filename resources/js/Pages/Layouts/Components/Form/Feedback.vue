<script setup>
    defineProps({
        message: {
                type: String,
                required: false
            },
    });
</script>
<template>
    <template v-if="message">
        <p class="text-sm mx-4 mt-2">
            <span class="text-red-500">
                {{ message }}
            </span>
        </p>
    </template>
</template>