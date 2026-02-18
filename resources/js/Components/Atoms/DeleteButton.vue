<script setup>
import { TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    title: {
        type: String,
        default: 'Verwijderen'
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

</script>

<template>
    <button
        type="button"
        @click="emit('delete')"
        :disabled="disabled"
        :title="title"
        v-tippy="$t('forms.actions.delete')"
        :class="[
            'p-2 text-gray-400 hover:text-red-500 transition-opacity',
            showOnHover ? 'opacity-0 group-hover:opacity-100' : 'opacity-100',
            disabled ? 'cursor-not-allowed opacity-50' : ''
        ]"
    >
        <TrashIcon class="h-5 w-5" />
    </button>
</template>
