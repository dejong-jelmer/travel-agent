<script setup>
import { watchEffect, onMounted } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { useToastWatcher } from '@/Composables/useToastWatcher.js';
import { emailLinks, phoneLinks } from '@/Composables/useAntiSpamLinks.js';


const props = defineProps({
    title: String,
    phone: Object,
});
const page = usePage()
const user = page.props.auth?.user ?? {};
const flash = page.props.flash ?? {};
const contact = page.props.contact ?? {};
const breadcrumbs = page.props.breadcrumbs ?? {};

onMounted(() => {
    const encodedMail = contact?.mail?.link;
    const encodedPhone = contact?.phone;
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
