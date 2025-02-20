<script setup>
import Layout from '../Layouts/Layout.vue';
import Section from '../Layouts/Section.vue';
import { usePage } from '@inertiajs/vue3';
const user = usePage().props.auth.user;
</script>
<template>
    <Layout>
        <Section>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
                <p class="text-gray-600">Welcome to the admin dashboard</p>
                <p>Welkom, {{ user.name }}!</p>
            </div>
        </Section>
    </Layout>
</template>
