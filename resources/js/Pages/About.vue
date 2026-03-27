<script setup>
import { useI18n } from 'vue-i18n'
import heroImage from '@/../images/about.webp'
import meImage from '@/../images/me.webp'

const { t, tm } = useI18n()

const myStory = tm('about.my_story')
const missionStatement = tm('about.mission_statement')
</script>

<template>
    <Layout>

        <template v-slot:hero>
            <section class="relative overflow-hidden h-[calc(100vh-100px)] flex items-end"
                :style="`background-image: url(${heroImage}); background-size: cover; background-position: center;`">
                <div class="absolute inset-0 bg-brand-accent/15"></div>

                <div
                    class="relative z-10 w-full max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-4 pb-12 laptop:pb-20">
                    <h1 class="text-2xl laptop:text-5xl font-poppins text-white leading-tight">
                        {{ t('about.hero.title') }}
                    </h1>
                    <p class="mt-2 text-base laptop:text-lg text-white/80 font-poppins">
                        {{ t('about.hero.sub_title') }}
                    </p>
                </div>
            </section>
        </template>

        <section class="relative overflow-hidden bg-white">
            <article class="relative py-12 laptop:py-24">
                <div class="max-w-3xl mx-auto px-4 text-center">
                    <SectionHeader>{{ t('about.title') }}</SectionHeader>
                    <p class="mt-6 text-base laptop:text-lg text-brand-text leading-relaxed">
                        {{ t('about.statement') }}
                    </p>
                </div>
            </article>
        </section>

        <section class="relative overflow-hidden bg-brand-secondary">
            <article class="relative py-12 laptop:py-24">
                <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-4">

                    <div class="mt-8 flex flex-col laptop:flex-row gap-10 laptop:gap-16 items-start">

                        <div class="relative w-full laptop:w-2/5 flex-shrink-0">
                            <img :src="meImage" :alt="t('about.founder.name')"
                                class="w-full max-w-sm mx-auto laptop:max-w-none rounded-2xl shadow-lg object-right object-cover aspect-[3/4]"
                                loading="lazy" />
                            <div
                                class="absolute inset-0 rounded-2xl bg-gradient-to-t from-black/60 via-transparent to-transparent">
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 z-10 px-6 pb-8">
                                <h2 class="text-xl laptop:text-2xl font-poppins font-bold text-white leading-tight">
                                    {{ t('about.founder.name') }}
                                </h2>
                                <p class="mt-1 text-sm laptop:text-base text-white/80 font-poppins">
                                    {{ t('about.founder.role') }}
                                </p>
                            </div>
                        </div>
                        <div class="w-full laptop:w-3/5 space-y-5">
                            <div class="flex items-center gap-2 mb-6">
                                <div class="w-4 h-px bg-brand-accent"></div>
                                <span class="text-xs font-medium tracking-widest uppercase text-brand-primary">
                                    {{ t('about.my_story_label') }}
                                </span>
                            </div>
                            <p v-for="(paragraph, i) in myStory" :key="i"
                                class="text-sm laptop:text-base text-brand-text leading-relaxed">
                                {{ paragraph }}
                            </p>
                        </div>
                    </div>

                </div>
            </article>
        </section>

        <!-- Pullquote -->
        <!-- <section class="relative overflow-hidden bg-white">
            <DecorativeLine />

            <article class="relative py-12 laptop:py-24">
                <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-4 text-center">

                </div>
            </article>
        </section> -->

        <!-- Mijn belofte -->
        <section class="relative overflow-hidden pt-12 laptop:pt-24">
            <!-- <DecorativeLine /> -->
            <div class="w-full text-center">
                <p class="text-2xl laptop:text-4xl font-poppins text-brand-primary leading-tight">
                    {{ t('about.pullquote') }}
                </p>
            </div>
            <article class="relative py-12 laptop:py-24">
                <div class="max-w-3xl mx-auto px-4">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-4 h-px bg-brand-accent"></div>
                        <span class="text-xs font-medium tracking-widest uppercase text-brand-primary">
                            {{ t('about.mission_label') }}
                        </span>
                    </div>
                    <div class="mt-6 space-y-5">
                        <p v-for="i in [0, 1]" :key="i"
                            class="text-sm laptop:text-base text-brand-text leading-relaxed">
                            {{ missionStatement[i] }}
                        </p>
                    </div>

                    <p class="mt-6 text-base laptop:text-lg font-semibold text-brand-primary leading-relaxed">
                        {{ missionStatement[2] }}
                    </p>

                </div>
            </article>
        </section>

        <!-- USP sectie -->
        <section class="relative overflow-hidden bg-white">
            <DecorativeLine />

            <article class="relative py-12 laptop:py-24">
                <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-4">
                    <USP />
                </div>
            </article>
        </section>

        <Newsletter />

    </Layout>
</template>
