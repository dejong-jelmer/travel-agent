<script setup>
    const props = defineProps({
        imageUrl: {
            type: String,
            required: true
        },
        alt: {
            type: String,
            required: true
        }
    })
</script>
<template>
    <div class="flex-shrink-0 w-24 overflow-hidden rounded-md border border-gray-200">
        <img class="aspect-video w-full object-cover" :src="imageUrl" :alt="alt" />
    </div>
</template>
