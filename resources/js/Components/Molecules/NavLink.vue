<script setup>
import { Link } from "@inertiajs/vue3";
import { computed } from 'vue';

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
    linkClass: {
        type: [String, Array, Object],
        default: ''
    }
});

const emit = defineEmits(['click']);

const desktopClasses = computed(() =>
    'px-4 py-2 text-sm laptop:text-base font-semibold text-center rounded-lg border-2 border-transparent hover:border-accent-gold transform hover:scale-105 transition-all duration-300 ease-out shadow-sm hover:shadow-md'
);

const mobileClasses = computed(() =>
    'block px-6 py-3 text-primary-dark hover:bg-secondary-sage hover:text-white transition-colors duration-200 font-medium'
);

const handleClick = (event) => {
    emit('click', event);
};
</script>

<template>
    <Link
        :href="href"
        :class="[linkClass, variant === 'desktop' ? desktopClasses : mobileClasses]"
        @click="handleClick"
    >
        <slot>{{ label }}</slot>
    </Link>
</template>
