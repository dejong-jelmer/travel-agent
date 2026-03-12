<script setup>
import { useI18n } from 'vue-i18n'
import newsletterImage from '@/../images/verona.jpg';
import contactImage from '@/../images/contact/ludo-photos-south-station-4927286_1920.jpg';

const props = defineProps({
    trips: Array,
    contact: Object,
});

const { t } = useI18n()
</script>

<template>
    <Layout>
        <template v-slot:hero>
            <Hero />
        </template>
        <main>
            <DecorativeLine />
            <section class="relative overflow-hidden">
                <!-- Trips -->
                <article class="relative py-12 table:py-24">
                    <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto">
                        <div class="text-center">
                            <SectionHeader>{{ t('home.our_trips_heading') }}</SectionHeader>
                        </div>
                        <template v-if="trips.length <= 0">
                            <div class="max-w-screen-laptop mx-auto px-4">
                                <div class="text-brand-primary max-w-4xl mx-auto px-4 pt-6 sm:px-6 desktop:px-8">
                                    <div class="text-center">
                                        <p
                                            class="text-base sm:text-lg desktop:text-xl text-brand-primary leading-relaxed">
                                            {{ t('home.no_trips.message_start') }} <DefaultLink :href="'/blog'">{{
                                                t('home.no_trips.blog_link') }}</DefaultLink>, {{
                                                    t('home.no_trips.message_middle') }} <DefaultLink :href="'/over-ons'">{{
                                                t('home.no_trips.about_link') }}</DefaultLink> {{
                                                    t('home.no_trips.message_middle2') }} <DefaultLink :href="'/contact'">{{
                                                t('home.no_trips.contact_link') }}</DefaultLink>.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <Slider :items="trips">
                                <template #default="{ item, index }">
                                    <TripCard :trip="item" :key="index" />
                                </template>
                            </Slider>
                        </template>
                    </div>
                </article>
                <!-- Newsletter -->
                <article class="relative bg-no-repeat bg-center bg-cover"  :style="`background-image: url(${newsletterImage})`">
                    <Newsletter />
                </article>
            </section>
            <!-- USP -->
            <DecorativeLine />
            <section class="relative py-12 table:py-24">
                <USP></USP>
            </section>
            <DecorativeLine />
            <section class="relative bg-no-repeat bg-center bg-cover tablet:py-24 px-4" :style="`background-image: url(${contactImage})`">
                <section id="contact"
                    class="max-w-screen-wide laptop:max-w-screen-desktop scroll-mt-12 mx-auto h-auto">
                    <ContactForm :contact="contact" />
                </section>
            </section>
        </main>
    </Layout>
</template>
