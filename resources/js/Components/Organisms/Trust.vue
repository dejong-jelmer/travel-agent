<script setup>
// import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRevealEffect } from '@/Composables/useRevealEffect.js';
import { Package, ShieldCheck, UserRound } from 'lucide-vue-next';
import portrait from '@/../images/me.webp';
import vvkrLogo from '@/../images/vvkr.png';

const { t } = useI18n();
const { rootRef, visible, reveal } = useRevealEffect();

const points = [
    {
        icon: Package,
        titleKey: 'about_trust.points.arranged.title',
        lineKey: 'about_trust.points.arranged.line',
    },
    {
        icon: ShieldCheck,
        titleKey: 'about_trust.points.protected.title',
        lineKey: 'about_trust.points.protected.line',
    },
    {
        icon: UserRound,
        titleKey: 'about_trust.points.contact.title',
        lineKey: 'about_trust.points.contact.line',
    },
];


</script>

<template>
    <section ref="rootRef" class="bg-white py-16 tablet:py-24 px-6 laptop:px-8">
        <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto">

            <div class="grid grid-cols-1 laptop:grid-cols-2 gap-10 laptop:gap-16 items-center">
                <div v-bind="reveal(0)" :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                    class="transition-all duration-1000 ease-out flex flex-col tablet:flex-row items-start gap-6 laptop:gap-8 text-center tablet:text-left">
                    <figure class="shrink-0 -rotate-3 bg-white p-2 pb-6 rounded-sm self-center laptop:self-start
                        shadow-[0_14px_30px_-10px_rgba(30,45,61,0.4)]
                        motion-safe:transition-transform motion-safe:duration-300
                        motion-safe:hover:rotate-0 motion-safe:hover:scale-[1.02]">
                        <img :src="portrait" :alt="t('about_trust.portrait_alt')"
                            class="w-36 h-36 laptop:w-44 laptop:h-44 object-cover object-[65%_center] rounded-[2px]"
                            loading="lazy" />
                    </figure>

                    <div>
                        <div class="flex items-center gap-3 mb-5">
                            <span class="block w-10 h-0.5 bg-brand-accent"></span>

                            <p
                                class="text-xs font-semibold tracking-[0.22em] uppercase text-brand-accent">
                                {{ t('about_trust.eyebrow') }}
                            </p>
                        </div>
                        <h2 class="font-poppins text-3xl laptop:text-4xl text-brand-primary leading-tight mb-2">
                            {{ t('about.founder.name') }}
                        </h2>
                        <p class="font-poppins text-base text-brand-light mb-6">
                            {{ t('about.founder.role') }}
                        </p>
                        <p class="font-poppins text-brand-text text-base laptop:text-lg leading-relaxed">
                            {{ t('about_trust.body') }}
                        </p>
                        <DefaultLink :href="route('about')" class="inline-block mt-6">
                            {{ t('about_trust.about_link') }} →
                        </DefaultLink>
                    </div>
                </div>
                <div v-bind="reveal(150)" :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                    class="transition-all duration-1000 ease-out">
                    <div class="bg-brand-secondary rounded-2xl p-8 tablet:p-10">
                        <h3 class="font-poppins text-base tablet:text-lg font-medium text-brand-primary mb-8">
                            {{ t('about_trust.trust_heading') }}
                        </h3>

                        <div class="grid grid-cols-1 tablet:grid-cols-3 gap-6">
                            <div v-for="(point, index) in points" :key="index"
                                class="flex flex-col items-center text-center gap-3">
                                <component :is="point.icon" class="w-7 h-7 text-brand-primary" :stroke-width="1.75" />
                                <h4 class="font-poppins font-semibold text-brand-text">
                                    {{ t(point.titleKey) }}
                                </h4>
                                <p class="font-poppins text-sm text-brand-text/80 leading-relaxed">
                                    {{ t(point.lineKey) }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="mt-8 pt-6 border-t border-brand-primary/15 flex flex-col tablet:flex-row tablet:items-center tablet:justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <a href="https://www.vvkr.nl/" target="_blank" rel="noopener noreferrer"
                                    aria-label="VvKR - Vereniging van Kleinschalige Reisorganisaties"
                                    class="block transition hover:scale-110 ease-in-out duration-300">
                                    <img :src="vvkrLogo" alt="Logo VvKR — Vereniging van Kleinschalige Reisorganisaties"
                                        class="h-8" aria-hidden="true">
                                </a>
                                <a href="https://www.sto-garant.nl/" target="_blank" rel="noopener noreferrer"
                                    aria-label="STO Garant"
                                    class="block transition hover:scale-110 ease-in-out duration-300">
                                    <StoSmall class="h-8 w-8" aria-hidden="true" />
                                </a>
                            </div>
                            <div class="inline text-sm">
                                {{ t('about_trust.read_about') }}
                                <DefaultLink :href="route('guarantee')" class="text-sm">
                                    STO-Garant →
                                </DefaultLink>
                                {{ t('about_trust.and_about') }}
                                <DefaultLink :href="route('vvkr')" class="text-sm">
                                    de VvKR →
                                </DefaultLink>
                            </div>
                        </div>
                    </div>
                </div>



            </div>

        </div>
    </section>
</template>
