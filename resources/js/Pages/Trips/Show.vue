<script setup>
import Layout from '@/Pages/Layouts/Layout.vue';

defineProps({
        trip: Object
    })
</script>
<template>
    <div class="w-full h-[450px] bg-cover bg-center" :style="`background-image: url(${trip.featured_image?.path})`"></div>
  <Layout>
    <h1 class="">{{ trip.countries_list }}</h1>
    {{ trip.name }}
  </Layout>
</template>
