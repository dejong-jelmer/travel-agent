<script setup>
import { computed } from 'vue';

const props = defineProps({
    for: {
        type: String,
        required: true
    },
    required: {
        type: Boolean,
        default: false
    },
    size: {
        type: String,
        default: 'base',
        validator: (value) => ['sm', 'base', 'lg'].includes(value)
    },
    weight: {
        type: String,
        default: 'bold',
        validator: (value) => ['medium', 'semibold', 'bold'].includes(value)
    }
});

const labelClasses = computed(() => {
    const sizeClasses = {
        'sm': 'text-sm',
        'base': 'text-base',
        'lg': 'text-lg'
    };

    const weightClasses = {
        'medium': 'font-medium',
        'semibold': 'font-semibold',
        'bold': 'font-bold'
    };

    return [
        'block',
        sizeClasses[props.size],
        weightClasses[props.weight]
    ].join(' ');
});
</script>

<template>
    <label :for="for" :class="labelClasses">
        <slot /><span v-if="required" class="ml-0.5">*</span>
    </label>
</template>
