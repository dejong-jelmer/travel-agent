<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import SideMenu from '@/Pages/Layouts/Components/SideMenu.vue';
import Header from '@/Pages/Layouts/Components/Header.vue';
import FlashMessage from '@/Pages/Layouts/Components/FlashMessage.vue';
import Footer from '@/Pages/Layouts/Components/Footer.vue';
import { useToastWatcher } from '@/Composables/toastWatcher.js';
import { watchEffect, onMounted } from 'vue';
import { emailLinks, phoneLinks } from '@/Composables/antiSpamLinks.js';
import Breadcrumbs from '@/Pages/Layouts/Components/Breadcrumbs.vue';


const props = defineProps({
    title: String,
    telephone: Object,
});

const user = usePage().props.auth?.user ?? {};
const flash = usePage().props.flash ?? {};
const contact = usePage().props.contact ?? {};
const breadcrumbs = usePage().props.breadcrumbs ?? {};

onMounted(() => {
    const encodedMail = contact?.mail?.link;
    const encodedPhone = contact?.telephone;
    emailLinks(encodedMail);
    phoneLinks(encodedPhone);
});

watchEffect(() => {
    document.title = usePage().props.title || `${window.appName} - Historische reizen met oog voor de toekomst`;
});

Object.entries(flash).forEach(([type, message]) => {
    useToastWatcher(message, type);
});
</script>

<template>
    <Head :title="title" />
    <main>
        <slot name="hero"></slot>
        <Header />
        <SideMenu v-if="!!user.id" />
        <slot></slot>
        <Footer />
    </main>
</template>
