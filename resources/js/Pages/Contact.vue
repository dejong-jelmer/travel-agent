<script setup>
import Layout from '@/Pages/Layouts/Layout.vue';
import ContactForm from "@/Pages/Layouts/Components/ContactForm.vue";
const props = defineProps({
    contact: Object
});
</script>

<template>
  <Layout>
    <section class="section">
      <article>
        <ContactForm :contact="contact" />
      </article>
    </section>
  </Layout>
</template>
