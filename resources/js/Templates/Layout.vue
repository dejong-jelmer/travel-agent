<script setup>
import { watchEffect, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useToastWatcher } from '@/Composables/useToastWatcher.js';
import { emailLinks, phoneLinks } from '@/Composables/useAntiSpamLinks.js';


const props = defineProps({
    title: String,
    phone: Object
});
const page = usePage()
const flash = page.props.flash ?? {};
const contact = page.props.contact ?? {};

onMounted(() => {
    const encodedMail = contact?.mail?.link;
    const encodedPhone = contact?.phone;
    emailLinks(encodedMail, '.email-field');
    phoneLinks(encodedPhone, '.tel-field');
});

watchEffect(() => {
    document.title = page.props.title || window.appName
});

Object.entries(flash).forEach(([type, message]) => {
    useToastWatcher(message, type);
});
</script>

<template>

    <SeoHead />
    <main>
        <Topbar class="z-50" />
        <Nav class="z-50 sticky top-0 inset-x-0" />
        <slot name="hero"></slot>
        <slot></slot>
        <Footer />
    </main>
</template>
