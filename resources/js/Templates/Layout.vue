<script setup>
import { watchEffect, onMounted } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { useToastWatcher } from '@/Composables/toastWatcher.js';
import { emailLinks, phoneLinks } from '@/Composables/antiSpamLinks.js';


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
    emailLinks(encodedMail, '.email-field');
    phoneLinks(encodedPhone, '.tel-field');
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
