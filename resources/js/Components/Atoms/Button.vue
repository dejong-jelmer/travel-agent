<script setup>
defineProps({
    text: String,
    color: {
        type: String,
        default: 'accent',
        validator: (value) => ['primary', 'accent'].includes(value)
    },
    // Renders a link with the same look, for a button that takes the visitor somewhere
    href: { type: String, default: null },
})
const baseClass = 'border-2 text-sm font-bold py-3 px-6 rounded-xl transition-colors duration-200 focus:outline-none disabled:opacity-40 disabled:cursor-not-allowed'
const colorClass = {
    primary: 'bg-white border-brand-primary text-brand-primary hover:text-white hover:bg-brand-primary',
    accent: 'bg-brand-accent border-transparent hover:bg-white text-white hover:text-brand-primary hover:border-brand-primary'
}
</script>
<template>
    <!-- Plain elements, not <component :is>: 'button' would resolve to this globally registered component itself -->
    <a v-if="href" :href="href" :class="[baseClass, colorClass[color]]">
        <slot></slot>
    </a>
    <button v-else :class="[baseClass, colorClass[color]]">
        <slot></slot>
    </button>
</template>
