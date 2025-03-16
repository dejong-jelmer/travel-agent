<script setup>
import Layout from '@/Pages/Layouts/Layout.vue';

defineProps({
        product: Object
    })
</script>
<template>
  <Layout>
    Showing prudct {{ product.name }}
  </Layout>
</template>
