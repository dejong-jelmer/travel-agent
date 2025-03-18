<script setup>
import Nav from './Components/Nav.vue';
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import SideMenu from '@/Pages/Layouts/Components/SideMenu.vue';

const user = usePage().props.auth.user;
const props = defineProps({ title: String });

watchEffect(() => {
  document.title = usePage().props.title || `${window.appName} - Historische reizen met oog voor de toekomst`;
});
</script>

<template>
    <Head :title="title" />
    <main>
        <Nav></Nav>
        <SideMenu v-if="user"/>
        <slot name="header"></slot>
        <section class="p-40">
            <slot />
        </section>
        <footer>
            <slot name="footer"></slot>
        </footer>
    </main>
</template>
