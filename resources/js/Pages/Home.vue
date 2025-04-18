<script setup>
import Layout from "@/Pages/Layouts/Layout.vue";
import Hero from "@/Pages/Layouts/Components/Hero.vue";
import Slider from "@/Pages/Layouts/Components/Slider.vue";
import ContactForm from "@/Pages/Layouts/Components/ContactForm.vue";

const props = defineProps({
    products: Array,
    contact: Object,
});
</script>

<template>
    <Layout>
        <template v-slot:hero>
            <Hero />
        </template>
        <main>
            <article class="h-auto">
                <Slider :items="products" />
            </article>
            <article id="contact" class="max-w-screen-desktop scroll-mt-12 mx-auto h-auto py-24 px-1 laptop:px-8">
                <div
                    class="px-6 tablet:px-24 py-10 tablet:py-24 border border-deep-blue rounded-2xl"
                >
                    <div class="grid gap-y-6 tablet:gap-y-10">
                        <h2
                            class="font-semibold text-2xl tablet:text-4xl text-warm-terracotta"
                        >
                            Omdat we graag contact hebben
                        </h2>
                        <p class="text-base tablet:text-xl text-deep-blue">
                            En we u met alle plezier helpen.
                            <br />
                            Bel
                            <span class="font-bold"
                                ><a :href="`tel:${contact.telephone.tel}`">{{
                                    contact.telephone.display
                                }}</a></span
                            >, stuur een mail naar <br />
                            <span class="font-bold"
                                ><a :href="`mailto:${contact.mail}`">{{
                                    contact.mail
                                }}</a></span
                            >
                            of gebruik het
                            <span class="font-bold">contactformulier</span>.
                        </p>
                        <ContactForm />
                    </div>
                </div>
            </article>
        </main>
    </Layout>
</template>
