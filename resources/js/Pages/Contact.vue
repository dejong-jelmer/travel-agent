<script setup>
import { useI18n } from 'vue-i18n'
import heroImage from '@/../images/contact.webp'

const { t } = useI18n()

const props = defineProps({
    contact: Object
});
</script>

<template>
    <Layout>

        <!-- Hero sectie -->
        <template v-slot:hero>
            <PageHero :image="heroImage" :title="t('forms.contact.heading')" :subtitle="t('contact.hero_tagline')" />
        </template>

        <!-- Form section -->
        <section class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-6 laptop:px-8 py-12 laptop:py-24">
            <ContactForm :contact="contact" />
        </section>

    </Layout>
</template>
