<script setup>
import {
    PencilLine,
    CalendarClock,
    LoaderCircle,
    Send,
} from 'lucide-vue-next';

const props = defineProps({
    status: {
        type: String,
        required: true,
        validator: (value) => [
            'draft',
            'scheduled',
            'sending',
            'sent',
        ].includes(value)
    }
})

const icons = {
    'draft': PencilLine,
    'scheduled': CalendarClock,
    'sending': LoaderCircle,
    'sent': Send,
}

const pillType = {
    'draft': 'sage',
    'scheduled': 'info',
    'sending': 'accent',
    'sent': 'success',
}

</script>

<template>
    <Pill :type="pillType[status]" variant="transparent">
        <component :is="icons[status]" class="h-5 w-5 flex-shrink-0" :class="{'animate-spin': props.status === 'sending'}" />
        <span class="ml-2 w-full text-center text-gray-600">
            <slot></slot>
        </span>
    </Pill>
</template>
