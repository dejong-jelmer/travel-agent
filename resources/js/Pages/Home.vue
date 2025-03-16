<script setup>
import Layout from '@/Pages/Layouts/Layout.vue';
import Header from '@/Pages/Layouts/Components/Header.vue';
import Slider from '@/Pages/Layouts/Components/Slider.vue';

const props = defineProps({
    products: Array
});
console.log('Products: ', props.products);
</script>

<template>
    <Layout>
        <template v-slot:header>
            <Header></Header>
        </template>
        <article class="relative w-full h-full">
            <Slider class="absolute" :items="products" />
        </article>
  </Layout>
</template>
