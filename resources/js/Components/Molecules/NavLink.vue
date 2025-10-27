<script setup>
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    href: {
        type: String,
        required: true
    },
    label: {
        type: String,
        required: false
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

const emit = defineEmits(['click']);

const handleClick = (event) => {
    emit('click', event);
};
</script>

<template>
    <Link
        v-if="variant === 'desktop'"
        :href="href"
        :class="customClass"
        class="px-4 py-2 text-sm laptop:text-base font-semibold text-center rounded-lg border-2 border-transparent hover:border-accent-gold transform hover:scale-105 transition-all duration-300 ease-out shadow-sm hover:shadow-md"
        @click="handleClick"
    >
        <slot>{{ label }}</slot>
    </Link>
    <Link
        v-else
        :href="href"
        class="block px-6 py-3 text-primary-dark hover:bg-secondary-sage hover:text-white transition-colors duration-200 font-medium"
        @click="handleClick"
    >
        <slot>{{ label }}</slot>
    </Link>
</template>
