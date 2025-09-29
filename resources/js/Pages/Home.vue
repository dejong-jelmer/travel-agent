<script setup>
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

                <article class="relative py-16 lg:py-24">
                    <div class="max-w-screen-desktop mx-auto px-4">
                        <div class="text-center mb-4">
                            <SectionHeader>Onze reizen</SectionHeader>
                        </div>
                        <template v-if="products.length <= 0">
                            <div class="max-w-screen-tablet mx-auto px-4">
                                <p
                                    class="text-primary-default text-lg laptop:text-xl max-w-2xl mx-auto leading-relaxed">
                                    Omdat we zijn hard aan het werk om onze eerste reizen op te zetten, kunt u momenteel
                                    nog
                                    geen
                                    reizen boeken. Kijk op onze <DefaultLink :href="'/blog'">blog</DefaultLink>,
                                    voor informatie over recente ativitetien of bekijk de <DefaultLink
                                        :href="'/over-ons'">over
                                        ons</DefaultLink>
                                    pagina om meer te weten te komen over Omdat we reizen.
                                    U kunt ook contact opnemen via ons <DefaultLink :href="'/contact'">contactformulier
                                    </DefaultLink> met ons op.
                                </p>

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
