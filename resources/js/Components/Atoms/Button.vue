<script setup>
defineProps({
    text: String,
    color: {
        type: String,
        default: 'accent',
        validator: (value) => ['primary', 'accent'].includes(value)
    },
})
const colorClass = {
    primary: 'bg-brand-primary',
    accent: 'bg-accent-primary'
}
</script>
<template>
    <button :class="[
        'hover:bg-white text-white border-2 border-transparent hover:text-brand-primary hover:border-brand-primary font-bold py-3 px-6 rounded-lg transition-colors duration-200 focus:outline-none disabled:opacity-40 disabled:cursor-not-allowed',
        colorClass[color]
    ]">
        <slot></slot>
    </button>
</template>
