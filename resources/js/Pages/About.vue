<script setup>
import { useI18n } from 'vue-i18n'
import teaserImage from '@/../images/loggawiggler-train-43371_1280.jpg'
import meImage from '@/../images/me.jpg'

const { t, tm } = useI18n()
</script>

<template>
    <Layout>

        <!-- Intro section -->
        <section class="section relative overflow-hidden floating-elements">
            <div class="absolute top-0 left-0 w-full h-2 decorative-line"></div>
            <div class="absolute top-16 left-8 w-4 h-4 bg-accent-sage/20 rounded-full"></div>
            <div class="absolute top-32 right-24 w-2 h-2 bg-accent-primary/30 rounded-full"></div>
            <div class="absolute bottom-24 left-16 w-3 h-3 bg-accent-terracotta/15 rounded-full"></div>

            <article class="relative">
                <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-4">

                    <div class="text-center">
                        <SectionHeader>{{ t('about.page_title') }}</SectionHeader>
                        <h1 class="text-3xl laptop:text-5xl font-bold text-brand-primary max-w-2xl mx-auto leading-tight">
                            {{ t('about.tagline') }}
                        </h1>
                    </div>

                    <div class="grid grid-cols-1 laptop:grid-cols-2 gap-8 laptop:gap-16 items-center mt-12 laptop:mt-16">
                        <div class="order-last laptop:order-first">
                            <div class="space-y-4">
                                <p v-for="(paragraph, i) in tm('about.intro_paragraph')" :key="i"
                                    class="text-lg laptop:text-xl text-accent-text leading-relaxed">
                                    {{ paragraph }}
                                </p>
                            </div>
                        </div>
                        <div class="order-first laptop:order-last">
                            <img :src="teaserImage" :alt="t('about.image_alt')"
                                class="w-full h-auto rounded-2xl shadow-lg object-cover" />
                        </div>
                    </div>

                </div>
            </article>
        </section>

        <!-- Values section -->
        <section class="section relative overflow-hidden bg-brand-secondary">
            <div class="absolute top-0 left-0 w-full h-2 decorative-line"></div>
            <div class="absolute top-20 right-12 w-4 h-4 bg-accent-sage/20 rounded-full"></div>
            <div class="absolute bottom-20 left-12 w-3 h-3 bg-accent-primary/20 rounded-full"></div>

            <article class="relative py-12 desktop:py-24">
                <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-4">
                    <USP />
                </div>
            </article>
        </section>

        <!-- Team section -->
        <section class="section relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-2 decorative-line"></div>
            <div class="absolute top-16 right-16 w-4 h-4 bg-accent-sage/20 rounded-full"></div>
            <div class="absolute top-40 left-8 w-2 h-2 bg-accent-primary/30 rounded-full"></div>
            <div class="absolute bottom-16 right-8 w-3 h-3 bg-accent-terracotta/15 rounded-full"></div>

            <article class="relative py-12 desktop:py-24">
                <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-4">

                    <div class="text-center">
                        <SectionHeader>{{ t('about.team.section_header') }}</SectionHeader>
                        <h2 class="text-2xl laptop:text-4xl font-bold text-brand-primary max-w-2xl mx-auto leading-tight">
                            {{ t('about.team.heading') }}
                        </h2>
                    </div>

                    <div class="flex justify-center mt-12 laptop:mt-16">
                        <Card class="max-w-sm w-full">
                            <img :src="meImage" :alt="t('about.team.member.name')"
                                class="w-full h-72 object-cover rounded-t-2xl" />
                            <div class="p-6">
                                <div class="flex items-center gap-2.5 mb-1">
                                    <div class="w-1.5 h-1.5 rounded-full bg-accent-primary"></div>
                                    <span class="text-sm font-medium text-accent-primary">{{ t('about.team.member.role') }}</span>
                                </div>
                                <h3 class="text-xl font-bold text-brand-primary mb-3 group-hover:text-accent-primary transition-colors duration-300">
                                    {{ t('about.team.member.name') }}
                                </h3>
                                <p class="text-brand-primary leading-relaxed text-sm">{{ t('about.team.member.bio') }}</p>
                            </div>
                        </Card>
                    </div>

                </div>
            </article>
        </section>

        <Newsletter />

    </Layout>
</template>
