<script setup>
import { TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    title: {
        type: String,
        default: 'Verwijderen'
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    showOnHover: {
        type: Boolean,
        default: true
    },
    disabled: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['delete']);

const sizeClasses = {
    sm: 'p-1',
    md: 'p-2',
    lg: 'p-3'
};

const iconSizeClasses = {
    sm: 'h-4 w-4',
    md: 'h-5 w-5',
    lg: 'h-6 w-6'
};
</script>

<template>
    <button
        type="button"
        @click="emit('delete')"
        :disabled="disabled"
        :title="title"
        v-tippy="$t('forms.actions.delete')"
        :class="[
            sizeClasses[size],
            'text-gray-400 hover:text-red-500 transition-opacity',
            showOnHover ? 'opacity-0 group-hover:opacity-100' : 'opacity-100',
            disabled ? 'cursor-not-allowed opacity-50' : ''
        ]"
    >
        <TrashIcon :class="iconSizeClasses[size]" />
    </button>
</template>
