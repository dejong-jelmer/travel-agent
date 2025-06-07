<script setup>
import AdminLayout from '@/Pages/Layouts/AdminLayout.vue';
import { usePage } from '@inertiajs/vue3';

const user = usePage().props.auth.user;
</script>
<template>
    <AdminLayout>
        <section class="section">
            <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
            <p class="text-gray-600">Welcome to the admin dashboard</p>
            <p>Welkom, {{ user.name }}!</p>
        </section>
    </AdminLayout>
</template>
