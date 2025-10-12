<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { useToastWatcher } from '@/Composables/useToastWatcher.js';
import { watchEffect, onMounted } from 'vue';
import { emailLinks, phoneLinks } from '@/Composables/useAntiSpamLinks.js';


const props = defineProps({
    title: String,
    phone: Object,
    links: {
        type: Object,
        required: false
    }
});

const user = usePage().props.auth?.user ?? {};
const flash = usePage().props.flash ?? {};
const contact = usePage().props.contact ?? {};
const breadcrumbs = usePage().props.breadcrumbs ?? {};

onMounted(() => {
    const encodedMail = contact?.mail?.link;
    const encodedPhone = contact?.phone;
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
            <div class="my-5 w-full flex justify-center">
                <Pagination v-if="typeof links !== 'undefined'" :links="links" />
            </div>
        </section>
    </main>
</template>
