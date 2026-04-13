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
    primary: 'bg-white border-brand-primary text-brand-primary hover:text-white hover:bg-brand-primary',
    accent: 'bg-brand-accent border-transparent hover:bg-white text-white hover:text-brand-primary hover:border-brand-primary'
}
</script>
<template>
    <button :class="[
        'border-2 text-sm font-bold py-3 px-6 rounded-xl transition-colors duration-200 focus:outline-none disabled:opacity-40 disabled:cursor-not-allowed',
        colorClass[color]
    ]">
        <slot></slot>
    </button>
</template>
