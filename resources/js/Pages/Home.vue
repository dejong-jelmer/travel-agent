<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { useI18n } from 'vue-i18n'
import newsletterImage from '@/../images/verona.webp';
import contactImage from '@/../images/contact/ludo-photos-south-station-4927286_1920.webp';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    trips: Array,
});

const { t } = useI18n()

const newsletterRef = ref(null)
const contactRef = ref(null)
const newsletterLoaded = ref(false)
const contactLoaded = ref(false)

onMounted(async () => {
    await nextTick()

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                if (entry.target === newsletterRef.value) newsletterLoaded.value = true
                if (entry.target === contactRef.value) contactLoaded.value = true
                observer.unobserve(entry.target)
            }
        })
    }, { rootMargin: '200px' })

    if (newsletterRef.value) observer.observe(newsletterRef.value)
    if (contactRef.value) observer.observe(contactRef.value)
})
</script>

<template>
    <Layout>
        <template v-slot:hero>
            <Hero />
        </template>
        <main>
            <section class="relative py-12 tablet:py-24">
                <USP />
            </section>

            <!-- Trips -->
            <DecorativeLine />
            <section id="trips" class="relative overflow-hidden scroll-mt-12">
                <article class="relative py-12 tablet:py-24 phone:px-6 laptop:px-8">
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
                                            {{ t('home.no_trips.message_start') }} <DefaultLink :href="route('blog.index')">{{
                                                t('home.no_trips.blog_link') }}</DefaultLink>, {{
                                                    t('home.no_trips.message_middle') }} <DefaultLink :href="route('about')">{{
                                                t('home.no_trips.about_link') }}</DefaultLink> {{
                                                    t('home.no_trips.message_middle2') }} <DefaultLink :href="route('contact')">{{
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
                <article ref="newsletterRef" class="relative bg-no-repeat bg-center bg-cover bg-brand-secondary"
                    :style="newsletterLoaded ? `background-image: url(${newsletterImage})` : ''">
                    <Newsletter />
                </article>
            </section>

            <!-- Pullquote rustpunt -->
            <section class="relative bg-brand-secondary py-16 tablet:py-24 px-4 text-center">
                <div class="max-w-2xl mx-auto">
                    <div class="font-poppins text-6xl laptop:text-7xl leading-none text-brand-accent/30 select-none mb-2">"</div>
                    <p class="font-poppins text-2xl laptop:text-3xl text-brand-primary leading-snug -mt-8">
                        {{ t('home.pullquote') }}
                    </p>
                    <p class="mt-6 font-poppins text-sm text-brand-light tracking-widest italic">
                        — {{ t('home.pullquote_source') }}
                    </p>
                </div>
            </section>

            <!-- Contact CTA-banner -->
            <section ref="contactRef" id="contact" class="relative bg-no-repeat bg-center bg-cover bg-brand-text"
                :style="contactLoaded ? `background-image: url(${contactImage})` : ''">
                <div class="absolute inset-0 bg-brand-text/55"></div>
                <div
                    class="relative z-10 max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-4 py-20 tablet:py-28 text-center">
                    <h2 class="text-3xl laptop:text-4xl font-poppins text-white leading-tight mb-4">
                        {{ t('home.contact_cta.heading') }}
                    </h2>
                    <p class="text-white/80 font-poppins text-base laptop:text-lg max-w-xl mx-auto mb-8">
                        {{ t('home.contact_cta.body') }}
                    </p>
                    <Link :href="route('contact')">
                        <Button
                            class="">
                            {{ t('home.contact_cta.button') }} →
                        </Button>
                    </Link>
                </div>
            </section>
        </main>
    </Layout>
</template>
