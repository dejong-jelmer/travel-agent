<script setup>
import { LoaderCircle } from 'lucide-vue-next';

const props = defineProps({
    form: {
        type: Object,
        required: true
    },
    label: {
        type: String,
        default: 'Opslaan'
    },
    processingLabel: {
        type: String,
        default: 'Bezig met opslaan...'
    },
    type: {
        type: String,
        default: 'button',
        validator: (value) => ['button', 'submit'].includes(value)
    }
});

</script>
<template>
    <button
        :type="type"
        class="inline-flex items-center px-6 py-3 bg-status-info text-white font-semibold rounded-lg hover:bg-opacity-60 transition-all duration-200 shadow-md hover:shadow-lg"
        :disabled="form.processing"
    >
        <span v-if="!form.processing">{{ label }}</span>
        <span v-else class="flex items-center">
            <LoaderCircle class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" />
            {{ processingLabel }}
        </span>
    </button>
</template>
