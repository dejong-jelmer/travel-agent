<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useMq } from 'vue3-mq'
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    // Every trip comes with travel_mode, see TripCard
    trips: Array,
});

const { t } = useI18n()
const mq = useMq()

// From tablet on up to three trips sit side by side in a centred row; with more trips, and always on phones, they
// go in a slider
const showTripsRow = computed(() => mq.tabletPlus && props.trips.length <= 3)

</script>

<template>
    <Layout>
        <template v-slot:hero>
            <HomeHero />
        </template>
        <main>
            <!-- Intro, where the scroll link in the hero leads to -->
            <section id="over-de-reizen" class="scroll-mt-36 px-6 py-12 tablet:py-[72px]">
                <p class="max-w-[720px] mx-auto text-center text-xl tablet:text-2xl leading-[1.6] text-brand-text">
                    {{ t('usp.intro') }}
                </p>
            </section>

            <!-- Trips -->
            <section id="reizen" class="scroll-mt-24 pb-14 tablet:pb-20 px-6 laptop:px-8">
                <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto">
                    <div class="text-center">
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
                        <!-- The cards come into view one after the other -->
                        <div v-if="showTripsRow" class="flex flex-wrap justify-center gap-8">
                            <TripCard v-for="(trip, index) in trips" :key="trip.id" :trip="trip"
                                v-reveal="{ delay: index * 80 }" class="grow basis-[280px] max-w-[400px]" />
                        </div>
                        <!-- Comes into view as a whole: cards outside the scroll area count as out of view, so revealing
                             them one by one would only fade them in once scrolled into view -->
                        <HorizontalSlider v-else v-reveal :items="trips" :label="t('home.our_trips_heading')"
                            :tablet-visible="2" :gap="24">
                            <template #default="{ item }">
                                <!-- Room for the card shadow, which the scroll area would cut off -->
                                <div class="h-full py-2">
                                    <TripCard :trip="item" class="h-full" />
                                </div>
                            </template>
                        </HorizontalSlider>

                        <div class="mt-8 text-center">
                            <Link :href="route('trips.index')"
                                class="inline-flex items-center min-h-[44px] px-2 font-semibold text-brand-primary underline-offset-4 hover:underline">
                                {{ t('home.all_trips_link') }}&nbsp;→
                            </Link>
                        </div>
                    </template>
                </div>
            </section>

            <USP v-reveal />
            <Newsletter v-reveal />
            <Trust />
            <DecorativeLine />
            <PullQuote />
        </main>
    </Layout>
</template>
