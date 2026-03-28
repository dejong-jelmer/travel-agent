<script setup>
import { useI18n } from 'vue-i18n'
import heroImage from '@/../images/about.webp'
import meImage from '@/../images/me.webp'

const { t, tm } = useI18n()

const myStory = tm('about.my_story')
const missionStatement = tm('about.promise_statement')
</script>

<template>
    <Layout>
        <!-- Hero -->
        <template v-slot:hero>
            <PageHero :image="heroImage" :title="t('about.hero.title')" :subtitle="t('about.hero.sub_title')"
                overlay-class="bg-brand-accent/15" />
        </template>
        <!-- Story -->
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
                                    {{ t('about.story_label') }}
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



        <section class="relative overflow-hidden py-12 laptop:py-24">
            <!-- Conviction -->
            <article class="">
                <div class="max-w-3xl mx-auto px-4">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-4 h-px bg-brand-accent"></div>
                        <span class="text-xs font-medium tracking-widest uppercase text-brand-primary">
                            {{ t('about.conviction_label') }}
                        </span>
                    </div>
                    <div class="mt-6 space-y-5">
                        <p class="text-sm laptop:text-base text-brand-text leading-relaxed">
                            {{ t('about.conviction') }}
                        </p>
                    </div>
                </div>
            </article>
             <div class="w-full text-center my-12 laptop:my-24">
                <p class="text-2xl laptop:text-4xl font-poppins text-brand-primary leading-tight">
                    {{ t('about.pullquote') }}
                </p>
            </div>
            <!-- Promise -->
            <article class="mt-12 laptop:mt-24">
                <div class="max-w-3xl mx-auto px-4">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-4 h-px bg-brand-accent"></div>
                        <span class="text-xs font-medium tracking-widest uppercase text-brand-primary">
                            {{ t('about.promise_label') }}
                        </span>
                    </div>
                    <div class="mt-6 space-y-5">
                        <p v-for="(paragraph, i) in missionStatement" :key="i"
                            :class="i === missionStatement.length - 1
                                ? 'text-base laptop:text-lg font-semibold text-brand-primary leading-relaxed mt-6'
                                : 'text-sm laptop:text-base text-brand-text leading-relaxed'">
                            {{ paragraph }}
                        </p>
                    </div>

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
