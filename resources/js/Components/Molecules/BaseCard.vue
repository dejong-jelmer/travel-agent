<script setup>
defineProps({
    // The element the card renders as
    as: {
        type: String,
        default: 'section',
        validator: (value) => ['section', 'article', 'aside'].includes(value)
    },
    // Light: a white card, dark: a card in the brand text color, for content that should stand out
    variant: {
        type: String,
        default: 'light',
        validator: (value) => ['light', 'dark'].includes(value)
    },
    // Default: roomy padding that grows from tablet on, compact: 24px on all screens, for smaller cards in a grid,
    // none: no padding, for cards whose sections run edge to edge or set their own
    padding: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'compact', 'none'].includes(value)
    }
})

const paddingClasses = {
    default: 'px-5 py-6 tablet:px-10 tablet:py-9',
    compact: 'p-6',
    none: ''
}
</script>

<template>
    <component :is="as" class="border rounded-2xl"
        :class="[
            paddingClasses[padding],
            variant === 'dark' ? 'bg-brand-text border-brand-text' : 'bg-white border-brand-accent/20'
        ]">
        <slot></slot>
    </component>
</template>
