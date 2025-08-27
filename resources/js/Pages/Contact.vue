<script setup>
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
