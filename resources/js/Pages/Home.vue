<script setup>
import Layout from "@/Pages/Layouts/Layout.vue";
import Hero from "@/Pages/Layouts/Components/Hero.vue";
import Slider from "@/Pages/Layouts/Components/Slider.vue";
import Card from "@/Pages/Layouts/Components/Card.vue";
import ContactForm from "@/Pages/Layouts/Components/ContactForm.vue";

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
            <!-- Slider Section met organische achtergrond -->
            <section class="pb-[100px] relative overflow-hidden bg-gradient-organic organic-shape floating-elements">
                <!-- Decoratieve elementen -->
                <div class="absolute top-0 left-0 w-full h-2 decorative-line opacity-30"></div>

                <!-- Subtiele geometrische vormen -->
                <div class="absolute top-16 left-8 w-4 h-4 bg-secondary-sage rounded-full opacity-20"></div>
                <div class="absolute top-32 right-24 w-2 h-2 bg-accent-gold rounded-full opacity-30"></div>
                <div class="absolute bottom-24 left-16 w-3 h-3 bg-accent-terracotta rounded-full opacity-15"></div>

                <article class="relative z-10 py-16 lg:py-24">
                    <div class="max-w-screen-desktop mx-auto px-4">
                        <div class="text-center mb-12">
                            <div class="inline-flex items-center gap-3 mb-6">
                                <span class="w-12 h-0.5 bg-accent-gold"></span>
                                <div class="w-3 h-3 bg-accent-gold rounded-full"></div>
                                <span class="w-12 h-0.5 bg-accent-gold"></span>
                            </div>
                            <h2 class="text-3xl lg:text-5xl font-bold text-primary-dark mb-4">
                                Ontdek onze reizen
                            </h2>
                            <p class="text-lg text-primary-default max-w-2xl mx-auto">
                                Duurzame avonturen die je leven verrijken
                            </p>
                        </div>
                        <Slider :items="products">
                            <template #default="{ item, index }">
                                <Card :product="item" :key="index" />
                            </template>
                        </Slider>

                        <!-- <Slider :items="products" /> -->
                    </div>
                </article>
            </section>

            <!-- Contact Section met subtiele achtergrond -->
            <section class="relative bg-gradient-subtle reverse-organic-shape">
                <!-- Decoratieve natuurlijke vormen -->
                <div class="absolute inset-0 overflow-hidden">
                    <div class="absolute top-1/4 -left-32 w-64 h-64 bg-secondary-sage/5 rounded-full"></div>
                    <div class="absolute bottom-1/3 -right-24 w-48 h-48 bg-accent-earth/8 rounded-full"></div>
                </div>

                <!-- Subtiele textuur overlay -->
                <div class="absolute inset-0 opacity-5"
                    style="background-image: radial-gradient(circle at 1px 1px, #A3BCCB 1px, transparent 0); background-size: 20px 20px;">
                </div>

                <ContactForm :contact="contact" />
            </section>
        </main>
    </Layout>
</template>
