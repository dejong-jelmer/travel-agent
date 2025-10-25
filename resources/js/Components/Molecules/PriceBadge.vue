<script setup>
import { computed } from 'vue';

const props = defineProps({
    price: {
        type: [Number, String],
        required: true
    },
    prefix: {
        type: String,
        default: 'Vanaf'
    },
    size: {
        type: String,
        default: 'medium',
        validator: (value) => ['small', 'medium', 'large'].includes(value)
    }
});

const sizeClasses = computed(() => {
    const sizes = {
        small: 'px-2 py-1',
        medium: 'px-3 py-1.5',
        large: 'px-4 py-2'
    };
    return sizes[props.size];
});

const textSizeClass = computed(() => {
    const textSizes = {
        small: 'text-xs',
        medium: 'text-sm',
        large: 'text-base'
    };
    return textSizes[props.size];
});
</script>

<template>
    <div
        class="bg-accent-gold text-white px-3 py-1.5 rounded-full shadow-lg"
        :class="sizeClasses"
    >
        <p class="font-bold" :class="textSizeClass">
            {{ prefix }} €{{ price }},-
        </p>
    </div>
</template>
