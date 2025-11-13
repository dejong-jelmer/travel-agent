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
        <div class="text-sm mx-4 mt-2">
            <template v-if="!Array.isArray(message)">
                <span class="text-status-error">{{ message }}</span>
            </template>
            <ul v-else class="list-disc list-inside text-status-error space-y-1">
                <li v-for="(error, index) in message" :key="index">{{ error }}</li>
            </ul>
        </div>
    </template>
</template>
