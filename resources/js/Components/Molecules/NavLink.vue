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
    desktop: 'px-4 py-2 text-sm laptop:text-base font-semibold text-center rounded-lg border-2 border-transparent hover:border-accent-gold transform hover:scale-105 transition-all duration-300 ease-out shadow-sm hover:shadow-md',
    mobile: 'block px-6 py-3 text-primary-dark hover:bg-secondary-sage hover:text-white transition-colors duration-200 font-medium'
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
