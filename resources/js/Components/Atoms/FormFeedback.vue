<script setup>
defineProps({
    message: {
        type: [String, Array],
        required: false,
    },
});
</script>
<template>
    <div class="text-sm mt-1 px-1">
        <span v-if="!Array.isArray(message)" class="text-status-error">{{ message }}</span>
        <ul v-else class="list-disc list-inside text-status-error space-y-1">
            <li v-for="(error, index) in message" :key="index">{{ error }}</li>
        </ul>
    </div>
</template>
