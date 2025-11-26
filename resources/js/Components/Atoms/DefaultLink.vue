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
    <Link :href="href" :class="[
        $attrs.class,
        'default-link'
        ]">
        <slot></slot>
    </Link>
</template>
