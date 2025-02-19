<script setup>
import Nav from './Components/Nav.vue'
import { Head } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import { watchEffect } from 'vue';
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
    <article>
        <slot />
    </article>
  </main>
</template>

<!-- <script>
export default {
  data() {
    return {
        title: "",
    };
  },
};
</script> -->
