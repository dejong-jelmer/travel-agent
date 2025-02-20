<script setup>
import Nav from './Components/Nav.vue';
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import SideMenu from '../Layouts/Components/SideMenu.vue';
import Section from '../Layouts/Section.vue';

const user = usePage().props.auth.user;
defineProps({ title: String });

watchEffect(() => {
  document.title = usePage().props.title || 'TussenTijd Reizen - Historische reizen met oog voor de toekomst';
});

</script>

<template>
    <Head :title="title" />
  <main>
    <header>
        <Nav></Nav>
    </header>

    <SideMenu v-if="user"/>

    <header>
        <slot name="header"></slot>
    </header>
    <article class="">
        <Section>
            <slot></slot>
        </Section>
    </article>
    <footer>
        <slot name="footer"></slot>
    </footer>
  </main>
</template>
