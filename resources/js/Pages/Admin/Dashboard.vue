<script setup>
import { usePage } from '@inertiajs/vue3';

const user = usePage().props.auth.user;
</script>
<template>
    <Admin>
        <section class="section">
            <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
            <p class="text-gray-600">Welcome to the admin dashboard</p>
            <p>Welkom, {{ user.name }}!</p>
        </section>
    </Admin>
</template>
