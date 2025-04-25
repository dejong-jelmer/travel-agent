<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { watchEffect, watch } from 'vue';
import SideMenu from '@/Pages/Layouts/Components/SideMenu.vue';
import Header from '@/Pages/Layouts/Components/Header.vue';
import FlashMessage from '@/Pages/Layouts/Components/FlashMessage.vue';
import Back from '@/Pages/Layouts/Components/Back.vue';
import Footer from '@/Pages/Layouts/Components/Footer.vue';
import { useToastWatcher } from '@/Composables/toastWatcher.js';

const props = defineProps({
     title: String,
     telephone: Object
    });

const user = usePage().props.auth?.user ?? {};
const flash = usePage().props.flash ?? {};
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
        <Header></Header>
        <SideMenu v-if="!!user.id"/>
        <section class="pt-40 px-2 wide:px-40">
            <!-- <Back v-if="!!user.id"/> -->
            <slot></slot>
        </section>
        <Footer />
    </main>
</template>
