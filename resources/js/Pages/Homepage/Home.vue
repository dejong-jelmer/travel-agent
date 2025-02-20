<script setup>
import Layout from '../Layouts/Layout.vue';
import Header from '../Layouts/Components/Header.vue';
import Slider from '../Layouts/Components/Slider.vue';

</script>

<template>
    <Layout>
        <template v-slot:header>
            <Header></Header>
        </template>
        <Slider :items="[
            { country: '1', days:'8', price:'895,-', title: 'Historische reizen', description: 'Ontdek de geschiedenis van de wereld', image: 'https://picsum.photos/400/225' },
            { country: '2', days:'4', price:'1295,-', title: 'Moderne reizen', description: 'Ontdek de wereld van morgen', image: 'https://picsum.photos/400/225' },
            { country: '3', days:'12', price:'1995,-', title: 'Duurzame reizen', description: 'Ontdek de wereld van morgen', image: 'https://picsum.photos/400/225' },
            { country: '4', days:'11', price:'995,-', title: 'Rondreizen', description: 'Ontdek de wereld van morgen', image: 'https://picsum.photos/400/225' },
        ]">
        </Slider>
  </Layout>
</template>
