<script setup>
import { useI18n } from 'vue-i18n'
import teaserImage from '@/../images/loggawiggler-train-43371_1280.jpg'
import meImage from '@/../images/me.jpg'

const { t, tm } = useI18n()
</script>

<template>
    <Layout>

        <!-- Intro section -->
        <section class="relative overflow-hidden">
            <article class="relative py-12 table:py-24">
                <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-4">

                    <div class="text-center">
                        <SectionHeader>{{ t('about.page_title') }}</SectionHeader>

                    </div>

                    <div class="mt-6 laptop:mt-8 overflow-hidden">
                        <!-- <img :src="teaserImage" :alt="t('about.image_alt')"
                            class="w-full laptop:w-1/2 laptop:float-right laptop:ml-8 laptop:mb-4 mb-6 h-auto rounded-2xl shadow-lg object-cover" /> -->
                        <div class="space-y-4">
                            <h1
                                class="text-base laptop:text-xl font-bold text-brand-primary leading-tight">
                                {{ t('about.tagline') }}
                            </h1>
                            <p v-for="(paragraph, i) in tm('about.mission_statement')" :key="i"
                                class="text-sm laptop:text-base text-brand-text leading-relaxed">
                                {{ paragraph }}
                            </p>
                            <h1 class="text-base laptop:text-xl text-left font-bold text-brand-primary leading-tight">
                                {{ t('about.why_title') }}
                            </h1>
                            <p class="text-sm laptop:text-base text-brand-text leading-relaxed">
                                {{ $t('about.why_statement') }}
                            </p>
                        </div>
                    </div>

                </div>
            </article>
        </section>

        <!-- Values section -->
        <section class="relative overflow-hidden bg-brand-secondary">
            <DecorativeLine />

            <article class="relative py-12 table:py-24">
                <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-4">
                    <USP />
                </div>
            </article>
        </section>

        <!-- Team section -->
        <section class="relative overflow-hidden">
            <DecorativeLine />

            <article class="relative py-12 table:py-24">
                <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-4">

                    <div class="text-center">
                        <SectionHeader>{{ t('about.team.section_header') }}</SectionHeader>
                        <h2
                            class="text-xl laptop:text-2xl font-bold text-brand-primary max-w-2xl mx-auto leading-tight">
                            {{ t('about.team.heading') }}
                        </h2>
                    </div>

                    <div class="flex justify-center mt-12 laptop:mt-16">
                        <Card class="max-w-sm w-full">
                            <img :src="meImage" :alt="t('about.team.member.name')"
                                class="w-full h-72 object-cover rounded-t-2xl" />
                            <div class="p-6">
                                <div class="flex items-center gap-2.5 mb-1">
                                    <div class="w-1.5 h-1.5 rounded-full bg-brand-accent"></div>
                                    <span class="text-sm font-medium text-brand-accent">{{ t('about.team.member.role')
                                    }}</span>
                                </div>
                                <h3
                                    class="text-xl font-bold text-brand-primary mb-3 group-hover:text-brand-accent transition-colors duration-300">
                                    {{ t('about.team.member.name') }}
                                </h3>
                                <p class="text-brand-primary leading-relaxed text-sm">{{ t('about.team.member.bio') }}
                                </p>
                            </div>
                        </Card>
                    </div>

                </div>
            </article>
        </section>

        <Newsletter />

    </Layout>
</template>
