<script setup>
import teaserImage from '@/../images/pexels-sonny-vermeer-505472791-17476670.jpg'

</script>
<template>
    <Layout>
        <section class="section relative overflow-hidden floating-elements">
            <div class="absolute top-0 left-0 w-full h-2 decorative-line"></div>
            <!-- Geometrische vormen -->
            <div class="absolute top-16 left-8 w-4 h-4 bg-nature-sage/20 rounded-full"></div>
            <div class="absolute top-32 right-24 w-2 h-2 bg-ui-gold/30 rounded-full"></div>
            <div class="absolute bottom-24 left-16 w-3 h-3 bg-nature-terracotta/15 rounded-full"></div>
            <article class="relative py-8 desktop:py-24">
                <div class="max-w-screen-desktop mx-auto px-4">
                    <div class="text-center">
                        <SectionHeader>Over ons</SectionHeader>
                    </div>
                    <div class="max-w-screen-laptop mx-auto px-4">
                        <div class="max-w-4xl mx-auto px-4 pt-6 sm:px-6 desktop:px-8 grid gap-4 desktop:gap-6">
                            <div class="grid grid-cols-1 desktop:grid-cols-2 gap-4 desktop:gap-6 ">
                                <div class="text-left space-y-6">
                                    <p class="text-lg sm:text-xl desktop:text-xl text-brand-primary leading-relaxed">
                                        Elke reis vertelt een verhaal. Het verhaal van plekken waar geschiedenis tot
                                        leven komt
                                        — van mensen, culturen en sporen uit het verleden die nog altijd voelbaar
                                        zijn.
                                    </p>

                                    <p
                                        class="text-base sm:text-lg desktop:text-xl text-brand-primary leading-relaxed">
                                        Binnenkort gaan we van start. <strong class="font-semibold">Blijf
                                            dichtbij, reis verder.</strong> Laat je meenemen op een reis met
                                        betekenis —
                                        <strong class="font-semibold">een reis met een
                                            verhaal.</strong>
                                    </p>
                                </div>
                                <div class="order-first desktop:order-last">
                                    <img :src="teaserImage" alt="Historische (trein)reizen in Europa"
                                        class="w-full h-auto rounded-lg shadow-lg object-cover" />
                                </div>
                            </div>
                            <div>
                                <p class="text-base sm:text-lg desktop:text-xl text-brand-primary leading-relaxed">
                                    Wij geloven dat reizen meer mag zijn dan alleen verplaatsen. Het mag raken,
                                    inspireren
                                    en verbinden. Daarom creëren we <strong class="font-semibold">historische
                                        treinreizen door Europa</strong>, duurzaam en bewust, met oog voor mens,
                                    natuur en
                                    omgeving.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
            <Newsletter />
        </section>
    </Layout>
</template>
