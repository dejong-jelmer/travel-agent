<script setup>
import Layout from '../Layout/Layout.vue'
import { Head } from '@inertiajs/vue3'

defineProps({ product: Object })

</script>

<template>
  <Layout>
    <Head title="Product" />
    <h1>Product</h1>
    <p></p>
  </Layout>
</template>
<script>
export default {
  data() {
    return {
        products: [],
    };
  },
};
</script>
