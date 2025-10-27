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
    },
    customClass: {
        type: [String, Array, Object],
        default: ''
    }
});

const variants = {
    desktop: 'px-4 py-2 text-sm laptop:text-base font-semibold...',
    mobile: 'block px-6 py-3 text-primary-dark...'
};

const baseClass = computed(() => variants[props.variant]);


const emit = defineEmits(['click']);

const handleClick = (event) => {
    emit('click', event);
};
</script>

<template>
    <Link
        :href="href"
        :class="[baseClass, customClass]"
        @click="handleClick"
    >
        <slot>{{ label }}</slot>
    </Link>
</template>
