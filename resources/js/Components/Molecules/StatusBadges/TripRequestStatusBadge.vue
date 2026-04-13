<script setup>
import {
    Sparkles,
    PhoneCall,
    ArrowRightLeft,
    Archive,
} from 'lucide-vue-next';

const props = defineProps({
    status: {
        type: String,
        required: true,
        validator: (value) => [
            'new',
            'contacted',
            'converted',
            'archived',
        ].includes(value)
    }
})

const icons = {
    'new': Sparkles,
    'contacted': PhoneCall,
    'converted': ArrowRightLeft,
    'archived': Archive,
}

const pillType = {
    'new': 'success',
    'contacted': 'accent',
    'converted': 'info',
    'archived': 'sage',
}
</script>

<template>
    <Pill :type="pillType[props.status]" variant="transparent">
        <component :is="icons[props.status]" class="h-5 w-5 flex-shrink-0" />
        <span class="ml-2 w-full text-center text-gray-600">
            <slot></slot>
        </span>
    </Pill>
</template>
