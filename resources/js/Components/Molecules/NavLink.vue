<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    href: {
        type: String,
        required: true
    },
    label: {
        type: String,
        required: true
    },
    variant: {
        type: String,
        default: 'desktop',
        validator: (value) => ['desktop', 'mobile'].includes(value)
    }
});

const defaultClass = 'relative transition-all duration-200 after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-accent-primary after:transition-all after:duration-300 hover:after:w-full';

const variants = {
    desktop: 'text-brand-primary text-sm laptop:text-base',
    mobile: 'block px-4 py-3 text-brand-primary text-base font-medium rounded-lg mx-auto my-1.5 max-w-[140px] text-center'
};

const variantClass = computed(() => variants[props.variant]);

const emit = defineEmits(['click']);

const handleClick = (event) => {
    emit('click', event);
};
</script>

<template>
    <Link
        :href="href"
        :class="[defaultClass, variantClass]"
        @click="handleClick"
    >
        <slot>{{ label }}</slot>
    </Link>
</template>
