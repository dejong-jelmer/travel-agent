<script setup>
import teaserImage from '@/../images/pexels-sonny-vermeer-505472791-17476670.jpg'

const props = defineProps({
    trips: Array,
    contact: Object
});
</script>

<template>
    <Layout>
        <template v-slot:hero>
            <Hero />
        </template>
        <main>
            <section class="relative overflow-hidden floating-elements">
                <DecorativeLine />
                <!-- Geometrische vormen -->
                <div class="absolute top-16 left-8 w-4 h-4 bg-brand-primary rounded-full"></div>
                <div class="absolute top-32 right-24 w-2 h-2 bg-brand-secondary rounded-full"></div>
                <div class="absolute bottom-24 left-16 w-3 h-3 bg-brand-tertiary rounded-full"></div>
                <!-- Trips -->
                <article class="relative py-8 desktop:py-24">
                    <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-4">
                        <div class="text-center">
                            <SectionHeader>Onze reizen</SectionHeader>
                        </div>
                        <template v-if="trips.length <= 0">
                            <div class="max-w-screen-laptop mx-auto px-4">
                                <div class="text-brand-primary max-w-4xl mx-auto px-4 pt-6 sm:px-6 desktop:px-8">
                                    <div class="text-center">
                                        <p
                                            class="text-base sm:text-lg desktop:text-xl text-brand-primary leading-relaxed">
                                            Omdat we hard aan het werk zijn om onze eerste reizen op te zetten, kunt u
                                            momenteel nog geen reizen boeken. Kijk op onze <DefaultLink :href="'/blog'">
                                                blog</DefaultLink>, voor informatie over recente activiteiten of bekijk
                                            de <DefaultLink :href="'/over-ons'">over ons</DefaultLink> pagina om meer te
                                            weten te komen over Omdat we reizen. U kunt ook contact opnemen via ons
                                            <DefaultLink :href="'/contact'">contactformulier</DefaultLink>.
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
                <article class="relative py-8 desktop:py-24 bg-brand-secondary">
                    <Newsletter />
                </article>
            </section>
            <!-- USP -->
            <section class="section relative px-4 laptop:px-16">
                <DecorativeLine />
                <!-- Geometrische vormen -->
                <div class="absolute top-16 left-8 w-4 h-4 bg-brand-primary/20 rounded-full"></div>
                <div class="absolute top-5 right-24 w-2 h-2 bg-brand-secondary/30 rounded-full"></div>
                <div class="absolute bottom-24 left-16 w-3 h-3 bg-brand-tertiary/15 rounded-full"></div>
                <USP></USP>
            </section>
            <section class="relative bg-gradient-reverse">
                <DecorativeLine />
                <!-- Subtiele geometrische vormen -->
                <div class="absolute top-16 left-8 w-4 h-4 bg-brand-primary/20 rounded-full"></div>
                <div class="absolute top-32 right-24 w-2 h-2 bg-brand-secondary/30 rounded-full"></div>
                <div class="absolute bottom-24 left-16 w-3 h-3 bg-brand-tertiary/15 rounded-full"></div>
                <!-- Decoratieve natuurlijke vormen -->
                <div class="absolute inset-0 overflow-hidden">
                    <div class="absolute top-1/4 -left-32 w-64 h-64 bg-brand-primary/20 rounded-full"></div>
                    <div class="absolute bottom-1/3 -right-24 w-48 h-48 bg-brand-tertiary/20 rounded-full"></div>
                </div>

                <!-- Subtiele textuur overlay -->
                <div class="absolute inset-0 opacity-5"
                    style="background-image: radial-gradient(circle at 1px 1px, #A3BCCB 1px, transparent 0); background-size: 20px 20px;">
                </div>
                <section id="contact" class="max-w-screen-wide laptop:max-w-screen-desktop scroll-mt-12 mx-auto h-auto py-24 px-1 laptop:px-8">
                    <ContactForm :contact="contact" />
                </section>
            </section>
        </main>
    </Layout>
</template>
