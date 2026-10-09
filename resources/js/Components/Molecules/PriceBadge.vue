<script setup>
import { computed } from 'vue';

const props = defineProps({
    price: {
        type: [Number, String],
        default: null
    },
    prefix: {
        type: String,
        default: 'Vanaf'
    },
    expected: {
        type: Boolean,
        default: false
    },
    expectedLabel: {
        type: String,
        default: 'Verwacht'
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
        class="bg-white text-brand-text rounded-full shadow-sm"
        :class="sizeClasses"
    >
        <p class="font-semibold select-none" :class="textSizeClass">
            <template v-if="expected">{{ expectedLabel }}</template>
            <template v-else>{{ prefix }} €{{ price }},-</template>
        </p>
    </div>
</template>
