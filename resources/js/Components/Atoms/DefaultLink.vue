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
    <Link :href="href" :class="[$attrs.class, ' text-nature-terracotta hover:text-brand-dark font-medium underline decoration-ui-gold/30 hover:decoration-ui-gold transition-colors duration-300']">
        <slot></slot>
    </Link>
</template>
