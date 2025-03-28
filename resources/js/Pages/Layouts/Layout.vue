<script setup>
import Nav from './Components/Nav.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import SideMenu from '@/Pages/Layouts/Components/SideMenu.vue';
import FlashMessage from '@/Pages/Layouts/Components/FlashMessage.vue';
import Back from '@/Pages/Layouts/Components/Back.vue';

const props = defineProps({ title: String });
const user = usePage().props.auth?.user ?? {};
const flash = usePage().props.flash ?? {};

watchEffect(() => {
  document.title = usePage().props.title || `${window.appName} - Historische reizen met oog voor de toekomst`;
});
</script>

<template>
    <Head :title="title" />
    <main>
        <Nav></Nav>
        <SideMenu v-if="!!user.id"/>
        <slot name="header"></slot>
        <section class="pt-40 px-2 desktop:px-40">
            <Back v-if="!!user.id"/>
            <FlashMessage v-if="flash.success" type="success" :message="flash.success" />
            <FlashMessage v-if="flash.error" type="error" :message="flash.error" />
            <slot></slot>
        </section>
        <footer>
            <slot name="footer"></slot>
        </footer>
    </main>
</template>
