<script setup>
import { Link } from "@inertiajs/inertia-vue3";

const props = defineProps({
    href: {
        type: String,
        required: false,
        default: '#'
    },
});

</script>
<template>
    <Link :href="href" :class="$attrs.class + ' text-accent-terracotta hover:text-primary-dark font-medium underline decoration-accent-gold/30 hover:decoration-accent-gold transition-colors duration-300'">
        <slot></slot>
    </Link>
</template>
