<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import SideMenu from '@/Pages/Layouts/Components/SideMenu.vue';
import Topbar from '@/Pages/Layouts/Components/Topbar.vue';
import FlashMessage from '@/Pages/Layouts/Components/FlashMessage.vue';
// import Footer from '@/Pages/Layouts/Components/Footer.vue';
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

<template v-if="!!user.id">

    <Head :title="title" />
    <main>
        <Topbar />
        <Breadcrumbs :breadcrumbs="breadcrumbs" />
        <SideMenu />
        <section class="my-[100px] px-6 tablet:px-8 laptop:px-14 desktop:px-18">
            <slot></slot>
        </section>
    </main>
</template>
