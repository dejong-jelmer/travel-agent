<script setup>
import Layout from '../Layout/Layout.vue'
import Section from '../Layout/Section.vue'
import Header from '../Layout/Components/Header.vue'
import Card from '../Layout/Components/Card.vue'
import { Head } from '@inertiajs/vue3'

</script>

<template>
    <Layout>
        <Head title="Home" />
        <Header></Header>
        <Section>
            <Card />
        </Section>
  </Layout>
</template>
