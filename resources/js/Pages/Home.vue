<script setup>
import Layout from '../Layout/Layout.vue'
import Section from '../Layout/Section.vue'
import Header from '../Layout/Components/Header.vue'
import Slider from '../Layout/Components/Slider.vue'

</script>

<template>
    <Layout>
        <Header></Header>
        <Section>
            <Slider :items="[
                { country: '1', days:'8', price:'895,-', title: 'Historische reizen', description: 'Ontdek de geschiedenis van de wereld', image: 'https://picsum.photos/400/225' },
                { country: '2', days:'4', price:'1295,-', title: 'Moderne reizen', description: 'Ontdek de wereld van morgen', image: 'https://picsum.photos/400/225' },
                { country: '3', days:'12', price:'1995,-', title: 'Duurzame reizen', description: 'Ontdek de wereld van morgen', image: 'https://picsum.photos/400/225' },
                { country: '4', days:'11', price:'995,-', title: 'Rondreizen', description: 'Ontdek de wereld van morgen', image: 'https://picsum.photos/400/225' },
            ]">
            </Slider>
        </Section>
  </Layout>
</template>
