<script setup>
// The buttons under a form in a modal.
defineProps({
    processing: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['cancel']);
</script>

<template>
    <div class="flex justify-end items-center gap-3">
        <button type="button" class="px-4 text-sm font-medium text-gray-600 hover:text-gray-900" @click="$emit('cancel')">
            Annuleren
        </button>
        <Button type="submit" :disabled="processing">Opslaan</Button>
    </div>
</template>
