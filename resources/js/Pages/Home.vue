<script setup>
import teaserImage from '@/../images/pexels-sonny-vermeer-505472791-17476670.jpg'
import { useForm } from '@inertiajs/inertia-vue3';

const props = defineProps({
    products: Array,
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
                <div class="absolute top-0 left-0 w-full h-2 decorative-line"></div>
                <!-- Geometrische vormen -->
                <div class="absolute top-16 left-8 w-4 h-4 bg-secondary-sage/20 rounded-full"></div>
                <div class="absolute top-32 right-24 w-2 h-2 bg-accent-gold/30 rounded-full"></div>
                <div class="absolute bottom-24 left-16 w-3 h-3 bg-accent-terracotta/15 rounded-full"></div>
                <article class="relative py-8 desktop:py-24">
                    <div class="max-w-screen-desktop mx-auto px-4">
                        <div class="text-center">
                            <SectionHeader>Over ons</SectionHeader>
                        </div>
                        <div class="max-w-screen-laptop mx-auto px-4">
                            <div class="max-w-4xl mx-auto px-4 pt-6 sm:px-6 desktop:px-8 grid gap-4 desktop:gap-6">
                                <div class="grid grid-cols-1 desktop:grid-cols-2 gap-4 desktop:gap-6 ">
                                    <div class="text-left space-y-6">
                                        <p class="text-lg sm:text-xl desktop:text-xl text-primary-default leading-relaxed">
                                            Elke reis vertelt een verhaal. Het verhaal van plekken waar geschiedenis tot
                                            leven komt
                                            — van mensen, culturen en sporen uit het verleden die nog altijd voelbaar
                                            zijn.
                                        </p>

                                        <p class="text-base sm:text-lg desktop:text-xl text-primary-default leading-relaxed">
                                            Binnenkort gaan we van start. <strong
                                                class="font-semibold">Blijf
                                                dichtbij, reis verder.</strong> Laat je meenemen op een reis met
                                            betekenis —
                                            <strong class="font-semibold">een reis met een
                                                verhaal.</strong>
                                        </p>
                                    </div>
                                    <div class="order-first desktop:order-last">
                                        <img :src="teaserImage"
                                            alt="Historische (trein)reizen in Europa"
                                            class="w-full h-auto rounded-lg shadow-lg object-cover" />
                                    </div>
                                </div>
                                <div>
                                    <p class="text-base sm:text-lg desktop:text-xl text-primary-default leading-relaxed">
                                            Wij geloven dat reizen meer mag zijn dan alleen verplaatsen. Het mag raken,
                                            inspireren
                                            en verbinden. Daarom creëren we <strong
                                                class="font-semibold">historische
                                                treinreizen door Europa</strong>, duurzaam en bewust, met oog voor mens,
                                            natuur en
                                            omgeving.
                                        </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
                <Newsletter :form="useForm({email:''})" />

                <article class="relative py-8 desktop:py-24">
                    <div class="max-w-screen-desktop mx-auto px-4">
                        <div class="text-center">
                            <SectionHeader>Onze reizen</SectionHeader>
                        </div>
                        <template v-if="products.length <= 0">
                            <div class="max-w-screen-laptop mx-auto px-4">
                                <div class="text-primary-default max-w-4xl mx-auto px-4 pt-6 sm:px-6 desktop:px-8">
                                    <div class="text-center">
                                        <p class="text-base sm:text-lg desktop:text-xl text-primary-default leading-relaxed">
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
                            <Slider :items="products">
                                <template #default="{ item, index }">
                                    <Card :product="item" :key="index" />
                                </template>
                            </Slider>
                        </template>
                    </div>
                </article>
            </section>
            <section class="section relative px-4 laptop:px-16">
                <div class="absolute top-0 left-0 w-full h-2 decorative-line"></div>
                <!-- Geometrische vormen -->
                <div class="absolute top-16 left-8 w-4 h-4 bg-secondary-sage/20 rounded-full"></div>
                <div class="absolute top-0 right-24 w-2 h-2 bg-accent-gold/30 rounded-full"></div>
                <div class="absolute bottom-24 left-16 w-3 h-3 bg-accent-terracotta/15 rounded-full"></div>
                <USP></USP>
            </section>
            <section class="relative bg-gradient-reverse">
                <div class="absolute top-0 left-0 w-full h-2 decorative-line"></div>
                <!-- Subtiele geometrische vormen -->
                <div class="absolute top-16 left-8 w-4 h-4 bg-secondary-sage/20 rounded-full"></div>
                <div class="absolute top-32 right-24 w-2 h-2 bg-accent-gold/30 rounded-full"></div>
                <div class="absolute bottom-24 left-16 w-3 h-3 bg-accent-terracotta/15 rounded-full"></div>
                <!-- Decoratieve natuurlijke vormen -->
                <div class="absolute inset-0 overflow-hidden">
                    <div class="absolute top-1/4 -left-32 w-64 h-64 bg-secondary-sage/20 rounded-full"></div>
                    <div class="absolute bottom-1/3 -right-24 w-48 h-48 bg-accent-earth/20 rounded-full"></div>
                </div>

                <!-- Subtiele textuur overlay -->
                <div class="absolute inset-0 opacity-5"
                    style="background-image: radial-gradient(circle at 1px 1px, #A3BCCB 1px, transparent 0); background-size: 20px 20px;">
                </div>
                <section id="contact" class="max-w-screen-desktop scroll-mt-12 mx-auto h-auto py-24 px-1 laptop:px-8">
                    <ContactForm :contact="contact" />
                </section>
            </section>
        </main>
    </Layout>
</template>
