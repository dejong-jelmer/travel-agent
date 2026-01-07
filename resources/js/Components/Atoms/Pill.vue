<script setup>
const props = defineProps({
    type: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'accent', 'sage', 'success', 'error', 'warning', 'info'].includes(value)
    },
    variant: {
        type: String,
        default: 'filled',
        validator: (value) => ['filled', 'transparent'].includes(value)
    }
})
const divClass = {
    filled: {
        primary: 'bg-brand-primary/20',
        accent: 'bg-accent-primary',
        sage: 'bg-accent-sage',
        success: 'bg-status-success',
        error: 'bg-status-error',
        warning: 'bg-status-warning',
        info: 'bg-status-info',
    },
    transparent: {
        primary: 'border border-brand-primary bg-brand-primary/10',
        accent: 'border border-accent-primary bg-accent-primary/10',
        sage: 'border border-accent-sage bg-accent-sage/10',
        success: 'border border-status-success bg-status-success/10',
        error: 'border border-status-error bg-status-error/10',
        warning: 'border border-status-warning bg-status-warning/10',
        info: 'border border-status-info bg-status-info/10',
    }
}
const spanClass = {
    filled: {
        primary: 'text-brand-primary',
        accent: 'text-white',
        sage: 'text-white',
        success: 'text-white',
        error: 'text-white',
        warning: 'text-white',
        info: 'text-white',
    },
    transparent: {
        primary: 'text-brand-primary',
        accent: 'text-accent-primary',
        sage: 'text-accent-sage',
        success: 'text-status-success',
        error: 'text-status-error',
        warning: 'text-status-warning',
        info: 'text-status-info',
    }
}
</script>
<template>
    <div :class="['flex px-4 py-1 rounded-full min-w-fit items-center', divClass[props.variant][props.type]]">
        <span
            :class="['text-xs mx-auto font-medium flex items-center', spanClass[props.variant][props.type]]">
            <slot></slot>
        </span>
    </div>
</template>
