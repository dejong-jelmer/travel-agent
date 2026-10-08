<script setup>
defineProps({
    // The element the card renders as
    as: {
        type: String,
        default: 'section',
        validator: (value) => ['section', 'article', 'aside'].includes(value)
    }
})
</script>

<template>
    <component :is="as" class="bg-white border border-brand-accent/20 rounded-2xl px-5 py-6 tablet:px-10 tablet:py-9">
        <slot></slot>
    </component>
</template>
