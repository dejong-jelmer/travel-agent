<script setup>
import { GripVertical, Pencil, Trash2 } from '@lucide/vue';

// The edit and delete buttons of a row, and the drag handle of a row in an ordered list. The handle class tells the
// lists apart when they nest.
defineProps({
    handle: {
        type: String,
        default: null,
    },
    // What the row is, for the screen reader: 'Etappe', 'Reis Heenreis', ...
    subject: {
        type: String,
        required: true,
    },
});

defineEmits(['edit', 'delete']);
</script>

<template>
    <div class="flex items-start gap-0.5 flex-shrink-0">
        <button v-if="handle" type="button" :class="[handle, 'p-1 rounded text-gray-400 hover:text-gray-600 hover:bg-gray-100 cursor-grab active:cursor-grabbing']"
            :aria-label="`${subject} verslepen`" v-tippy="'Verslepen'">
            <GripVertical class="h-4 w-4" aria-hidden="true" />
        </button>
        <button type="button" class="p-1 rounded text-gray-400 hover:text-brand-primary hover:bg-gray-100"
            :aria-label="`${subject} bewerken`" v-tippy="'Bewerken'" @click="$emit('edit')">
            <Pencil class="h-4 w-4" aria-hidden="true" />
        </button>
        <button type="button" class="p-1 rounded text-gray-400 hover:text-status-error hover:bg-gray-100"
            :aria-label="`${subject} verwijderen`" v-tippy="'Verwijderen'" @click="$emit('delete')">
            <Trash2 class="h-4 w-4" aria-hidden="true" />
        </button>
    </div>
</template>
