<script setup>
import { watchEffect, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useToastWatcher } from '@/Composables/useToastWatcher.js';
import { emailLinks, phoneLinks } from '@/Composables/useAntiSpamLinks.js';
import appName from '@/appName.js';


const props = defineProps({
    title: String,
    phone: Object,
    showContact: {type: Boolean, default: true }
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

// Flush 'post' keeps this out of server-side rendering, where there is no document; the title of the first page
// comes from the <title> tag in seo.blade.php
watchEffect(() => {
    document.title = page.props.title || appName
}, { flush: 'post' });

Object.entries(flash).forEach(([type, message]) => {
    useToastWatcher(message, type);
});
</script>

<template>
    <main class="overflow-x-clip bg-brand-background">
        <Topbar class="z-50" />
        <!-- With a hero the wrapper has no height, so the nav floats over the hero image instead of pushing it down -->
        <div class="z-50 sticky top-0 inset-x-0" :class="{ 'h-0': $slots.hero }">
            <Nav :over-hero="!!$slots.hero" />
        </div>
        <slot name="hero"></slot>
        <slot></slot>
        <Footer :showContact="showContact"/>
    </main>
</template>
