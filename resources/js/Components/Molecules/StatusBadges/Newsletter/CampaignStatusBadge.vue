<script setup>
import {
    PencilLine,
    CalendarClock,
    FileStack,
    Send,
    X,
} from 'lucide-vue-next';

const props = defineProps({
    status: {
        type: String,
        required: true,
        validator: (value) => [
            'draft',
            'scheduled',
            'queued',
            'sent',
            'failed',
        ].includes(value)
    }
})

const icons = {
    'draft': PencilLine,
    'scheduled': CalendarClock,
    'queued': FileStack,
    'sent': Send,
    'failed': X,
}

const pillType = {
    'draft': 'sage',
    'scheduled': 'info',
    'queued': 'accent',
    'sent': 'success',
    'failed': 'error',
}

</script>

<template>
    <Pill :type="pillType[status]" variant="transparent">
        <component :is="icons[status]" class="h-5 w-5 flex-shrink-0" />
        <span class="ml-2 w-full text-center text-gray-600">
            <slot></slot>
        </span>
    </Pill>
</template>
