<script setup>
import Layout from '@/Pages/Layouts/Layout.vue'
import { Link } from '@inertiajs/vue3';

defineProps({
    itineraries: Object,
});
</script>
<template>
    <Layout>
        {{ itineraries }}
    </Layout>
  </template>
