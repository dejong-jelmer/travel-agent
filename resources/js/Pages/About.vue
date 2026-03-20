<script setup>
import { useI18n } from 'vue-i18n'
import heroImage from '@/../images/about.webp'
import meImage from '@/../images/me.webp'

const { t, tm } = useI18n()

const missionParagraphs = tm('about.mission_statement')
const missionIntro = missionParagraphs.slice(0, 3)
const missionInspire = missionParagraphs[3]
const missionPromise = missionParagraphs[4]
</script>

<template>
    <Layout>

        <!-- Hero sectie -->
        <template v-slot:hero>
            <section class="relative overflow-hidden min-h-[420px] laptop:min-h-[560px] flex items-end"
                :style="`background-image: url(${heroImage}); background-size: cover; background-position: center;`">
                <!-- <div class="absolute inset-0 bg-brand-text/60"></div> -->
                <div class="absolute inset-0" style="background: rgba(245, 158, 11, 0.15);"></div>

                <div
                    class="relative z-10 w-full max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-4 pb-12 laptop:pb-20">
                    <h1 class="text-3xl laptop:text-5xl font-cormorant font-bold text-white leading-tight">
                        {{ t('about.hero.title') }}

                    </h1>
                    <p class="mt-2 text-base laptop:text-lg text-white/80 font-poppins">
                        {{ t('about.hero.sub_title') }}
                    </p>
                </div>
            </section>
        </template>

        <!-- Missie sectie: tagline + waarom -->
        <section class="relative overflow-hidden">
            <article class="relative py-12 laptop:py-24">
                <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-4">

                    <div class="text-center">
                        <SectionHeader>{{ t('about.page_title') }}</SectionHeader>
                        <h2
                            class="text-xl laptop:text-2xl font-bold text-brand-primary max-w-2xl mx-auto leading-tight">
                            {{ t('about.tagline') }}
                        </h2>
                    </div>

                    <!-- Waarom: featured blok -->
                    <blockquote class="mt-10 laptop:mt-14 max-w-3xl mx-auto border-l-4 border-brand-accent pl-6 py-2">
                        <p class="text-base laptop:text-lg text-brand-text leading-relaxed italic">
                            {{ t('about.why_statement') }}
                        </p>
                        <footer class="mt-4 text-sm font-semibold text-brand-accent not-italic">
                            — {{ t('about.team.member.name') }}
                        </footer>
                    </blockquote>

                </div>
            </article>
        </section>

        <!-- Twee kolommen: foto + duurzaamheidstekst -->
        <section class="relative overflow-hidden bg-brand-secondary">
            <DecorativeLine />

            <article class="relative py-12 laptop:py-24">
                <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-4">
                    <div class="flex flex-col laptop:flex-row gap-10 laptop:gap-16 items-center">

                        <!-- Foto -->
                        <div class="relative w-full laptop:w-2/5 flex-shrink-0">
                            <img :src="meImage" :alt="t('about.team.member.name')"
                                class="w-full max-w-sm mx-auto laptop:max-w-none rounded-2xl shadow-lg object-right object-cover aspect-[3/4]"
                                loading="lazy" />
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                            <div class="absolute bottom-0 left-0 right-0 z-10 px-6 pb-8">
                                <h1 class="text-2xl laptop:text-3xl font-cormorant font-bold text-white leading-tight">
                                    {{ t('about.team.member.name') }}
                                </h1>
                                <p class="mt-1 text-sm laptop:text-base text-white/80 font-poppins">
                                    {{ t('about.team.member.role') }}
                                </p>
                            </div>
                        </div>

                        <!-- Tekst: paragrafen 1-3 -->
                        <div class="w-full laptop:w-3/5 space-y-5">
                            <p v-for="(paragraph, i) in missionIntro" :key="i"
                                class="text-sm laptop:text-base text-brand-text leading-relaxed">
                                {{ paragraph }}
                            </p>
                        </div>

                    </div>
                </div>
            </article>
        </section>

        <!-- Pullquote -->
        <section class="relative overflow-hidden">
            <DecorativeLine />

            <article class="relative py-12 laptop:py-24">
                <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-4 text-center">
                    <p class="text-4xl laptop:text-6xl font-cormorant italic text-brand-primary leading-tight">
                        "{{ t('about.pullquote') }}"
                    </p>
                </div>
            </article>
        </section>

        <!-- Slottekst: inspiratie + belofte -->
        <section class="relative overflow-hidden bg-brand-secondary">
            <DecorativeLine />

            <article class="relative py-12 laptop:py-24">
                <div class="max-w-3xl mx-auto px-4">
                    <p class="text-sm laptop:text-base text-brand-text leading-relaxed">
                        {{ missionInspire }}
                    </p>
                    <p class="mt-6 text-base laptop:text-lg font-semibold text-brand-accent leading-relaxed">
                        {{ missionPromise }}
                    </p>
                </div>
            </article>
        </section>

        <!-- USP sectie -->
        <section class="relative overflow-hidden">
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
