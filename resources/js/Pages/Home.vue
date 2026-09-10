<script setup>
import { useI18n } from 'vue-i18n'
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    trips: Array,
});

const { t } = useI18n()

</script>

<template>
    <Layout>
        <template v-slot:hero>
            <HomeHero />
        </template>
        <main>
            <USP />

            <!-- Trips -->
            <DecorativeLine />
            <section id="reizen" class="scroll-mt-24 py-12 tablet:py-24 px-6 laptop:px-8">
                <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto text-center">
                    <SectionHeader>{{ t('home.our_trips_heading') }}</SectionHeader>
                </div>
                <template v-if="trips.length <= 0">
                    <p class="text-brand-text text-sm laptop:text-lg text-left max-w-2xl mx-auto leading-relaxed">
                        {{ t('home.no_trips.message_start') }} <DefaultLink :href="route('blog.index')">{{
                            t('home.no_trips.blog_link') }}</DefaultLink>, {{
                                t('home.no_trips.message_middle') }} <DefaultLink :href="route('about')">{{
                            t('home.no_trips.about_link') }}</DefaultLink> {{
                                t('home.no_trips.message_middle2') }} <a class="default-link" href="#nieuwsbrief">{{
                            t('home.no_trips.newsletter_link') }}</a>.
                    </p>
                </template>
                <template v-else>
                    <Slider :items="trips" :visible="3" :internal-arrows="false">
                        <template #default="{ item, index }">
                            <TripCard :trip="item" :key="index" />
                        </template>
                    </Slider>
                </template>
            </section>

            <Newsletter />
            <Trust />
            <DecorativeLine />
            <PullQuote />
        </main>
    </Layout>
</template>
