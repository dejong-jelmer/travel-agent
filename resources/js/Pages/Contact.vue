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
            <section class="relative overflow-hidden h-[calc(100vh-100px)] flex items-end"
                :style="`background-image: url(${heroImage}); background-size: cover; background-position: center;`">
                <div class="absolute inset-0" style="background: rgba(30, 45, 61, 0.25);"></div>

                <div
                    class="relative z-10 w-full max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-4 pb-12 laptop:pb-20">
                    <h1 class="text-2xl laptop:text-5xl font-poppins text-white leading-tight">
                        {{ t('forms.contact.heading') }}
                    </h1>
                    <p class="mt-2 text-base laptop:text-lg text-white/80 font-poppins max-w-xl">
                        {{ t('contact.hero_tagline') }}
                    </p>
                </div>
            </section>
        </template>

        <!-- Form section -->
        <section class="relative overflow-hidden">
            <article class="relative py-12 laptop:py-24">
                <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-4">
                    <ContactForm :contact="contact" />
                </div>
            </article>
        </section>

    </Layout>
</template>
