<script setup>
const props = defineProps({
    type: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'accent', 'sage', 'success', 'error', 'warning', 'link'].includes(value)
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
        accent: 'bg-accent-primary/20',
        sage: 'bg-accent-sage/20',
        success: 'bg-status-success',
        error: 'bg-status-error',
        warning: 'bg-status-warning',
        link: 'bg-accent-link/20',
    },
    transparent: {
        primary: 'border border-brand-primary',
        accent: 'border border-accent-primary',
        sage: 'border border-accent-sage',
        success: 'border border-status-success',
        error: 'border border-status-error',
        warning: 'border border-status-warning',
        link: 'border border-accent-link',
    }
}
const spanClass = {
    filled: {
        primary: 'text-brand-primary',
        accent: 'text-accent-primary',
        sage: 'text-accent-sage',
        success: 'text-white',
        error: 'text-white',
        warning: 'text-white',
        link: 'text-accent-link',
    },
    transparent: {
        primary: 'text-brand-primary',
        accent: 'text-accent-primary',
        sage: 'text-accent-sage',
        success: 'text-status-success',
        error: 'text-status-error',
        warning: 'text-status-warning',
        link: 'text-accent-link',
    }
}
</script>
<template>
    <div :class="['flex px-3 py-1 rounded-full min-w-fit items-center', divClass[props.variant][props.type]]">
        <span :class="['text-xs font-medium inline-flex mx-auto', spanClass[props.variant][props.type]]">
            <slot></slot>
        </span>
    </div>
</template>
