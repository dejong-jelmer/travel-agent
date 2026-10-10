<script setup>
import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRevealEffect } from '@/Composables/useRevealEffect.js';
import heroVideo from '@/../videos/home-hero.mp4';
import heroImage from '@/../images/hero-poster.jpg';
import { useMq } from 'vue3-mq';
import { ArrowDown } from '@lucide/vue';

const { t } = useI18n();
const mq = useMq();

const { rootRef, visible, reveal } = useRevealEffect();

const videoRef = ref(null)
const prefersReducedMotion = ref(false)

onMounted(() => {
    prefersReducedMotion.value = window.matchMedia('(prefers-reduced-motion: reduce)').matches

    if (videoRef.value && !prefersReducedMotion.value) {
        videoRef.value.playbackRate = 0.8
    }
})
</script>

<template>
    <div ref="rootRef" class="relative h-hero-home flex px-6 overflow-hidden">
        <video ref="videoRef" :poster="heroImage" class="absolute inset-0 w-full h-full object-cover" preload="none"
            :src="heroVideo" :autoplay="!prefersReducedMotion" muted loop playsinline />
        <!-- Darkens the top so the transparent nav stays readable on light footage -->
        <div
            class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-brand-text/50 to-transparent pointer-events-none">
        </div>

        <div class="relative max-w-screen-wide laptop:max-w-screen-desktop w-fit mx-auto">
            <div
                class="absolute top-[60%] laptop:top-[50%] left-1/2 -translate-x-1/2 -translate-y-1/2 flex flex-col items-center gap-12 laptop:gap-6">
                <h1
                    class="text-brand-secondary font-poppins text-nowrap font-bold text-4xl tablet:text-5xl laptop:text-6xl select-none text-center [text-shadow:_0_0_16px_rgb(0_0_0_/_0.55),_0_1px_3px_rgb(0_0_0_/_0.6)]">
                    <span v-bind="reveal(50)" :class="visible ? 'opacity-100' : 'opacity-0'"
                        class="block tablet:inline transition-opacity duration-[1200ms] ease-in-out tracking-wider">{{
                            t('hero.title') }}</span>
                    <span class="hidden tablet:inline">&nbsp;</span>
                    <span v-bind="reveal(1000)" :class="visible ? 'opacity-100' : 'opacity-0'"
                        class="drop-shadow-2xl block tablet:inline transition-opacity duration-[1200ms] ease-in-out tracking-wider">{{
                            t('hero.sub_title') }}</span>
                </h1>
                <p v-bind="reveal(1950)" :class="visible ? 'opacity-100' : 'opacity-0'"
                    class="text-brand-secondary font-poppins font-normal text-xl laptop:text-3xl max-w-xl text-center text-balance tracking-wide transition-opacity duration-[1200ms] ease-in-out [text-shadow:_0_0_16px_rgb(0_0_0_/_0.55),_0_1px_3px_rgb(0_0_0_/_0.6)]">
                    {{ t('hero.tagline') }}
                </p>
                <a href="#over-de-reizen" v-bind="reveal(2400)" :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                    class="transition-all duration-[1200ms] ease-out inline-flex items-center bg-brand-accent hover:bg-brand-accent/90 text-white font-poppins font-bold text-base laptop:text-xl px-7 py-3 rounded-full shadow-lg">
                    {{ t('hero.cta') }}
                    <ArrowDown class="h-4" />
                </a>
            </div>
        </div>

        <ScrollIndicator />
    </div>
</template>
