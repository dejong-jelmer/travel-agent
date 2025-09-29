<template>
        <label :for="formField" class="block text-base font-bold">
            <slot />
        </label>
</template>
<script setup>
    defineProps({
        formField: {
            type: String,
            required: true
        }
    })
</script>
