<script setup>
const props = defineProps({
    trip: Object
});
</script>

<template>
  <Layout>
    <section class="section">
      <article>
        <div class="text-black">
            <h1 class="title">{{ trip.name }}</h1>
            <card-modal></card-modal>
        </div>
        <!-- <div v-html="trip.content"></div> -->
        <!-- <ContactForm :contact="contact" /> -->
      </article>
    </section>
  </Layout>
</template>
