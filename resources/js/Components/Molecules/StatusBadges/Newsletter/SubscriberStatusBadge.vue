<script setup>
import {
    Check,
    Clock,
    AlertCircle,
    X,
} from 'lucide-vue-next';

const props = defineProps({
    status: {
        type: String,
        required: true,
        validator: (value) => [
            'active',
            'pending',
            'expired',
            'unsubscribed',
        ].includes(value)
    }
})

const icons = {
    'active': Check,
    'pending': Clock,
    'expired': AlertCircle,
    'unsubscribed': X,
}

const pillType = {
    'active': 'success',
    'pending': 'accent',
    'expired': 'accent',
    'unsubscribed': 'error',
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
