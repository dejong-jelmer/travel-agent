<script setup>
import { ref, computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import heroVideo from '@/../videos/home-hero.mp4';
import heroImage from '@/../images/hero-poster.webp';
import { useMq } from 'vue3-mq';
import { ArrowDown } from 'lucide-vue-next';

const { t } = useI18n();
const mq = useMq();

const videoRef = ref(null)
const visibleTitle = ref(false)
const visibleSubTitle = ref(false)
const visibleCta = ref(false)
const prefersReducedMotion = ref(false)

onMounted(() => {
    prefersReducedMotion.value = window.matchMedia('(prefers-reduced-motion: reduce)').matches

    if (videoRef.value && !prefersReducedMotion.value) {
        videoRef.value.playbackRate = 0.6
    }
    setTimeout(() => {
        visibleTitle.value = true
    }, 50)
    setTimeout(() => {
        visibleSubTitle.value = true
    }, 1200)
    setTimeout(() => {
        visibleCta.value = true
    }, 2200)
})
</script>

<template>
    <div class="relative h-[calc(100vh-theme(spacing.header))] flex px-6 overflow-hidden ">
        <video ref="videoRef" :poster="heroImage" class="absolute inset-0 w-full h-full object-cover scale-x-[-1]"
            preload="none" :src="heroVideo" :autoplay="!prefersReducedMotion" muted loop playsinline />
        <div class="relative max-w-screen-wide laptop:max-w-screen-desktop w-fit mx-auto">
            <div
                class="absolute top-[40%] laptop:top-[50%] left-1/2 -translate-x-1/2 -translate-y-1/2 flex flex-col items-center gap-6">
                <h1
                    class="text-brand-secondary font-poppins text-nowrap font-light text-4xl laptop:text-5xl select-none text-center [text-shadow:_0_2px_4px_rgb(0_0_0_/_0.4)]">
                    <span :class="visibleTitle ? 'opacity-100' : 'opacity-0'"
                        class="block tablet:inline transition-opacity duration-[1200ms] ease-in-out tracking-wider">{{
                        t('hero.title') }}</span>
                    <span class="hidden tablet:inline">&nbsp;</span>
                    <span :class="visibleSubTitle ? 'opacity-100' : 'opacity-0'"
                        class="drop-shadow-2xl block tablet:inline transition-opacity duration-[1200ms] ease-in-out tracking-wider">{{
                            t('hero.sub_title') }}</span>
                </h1>
                <a href="#over-de-reizen" :class="visibleCta ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                    class="transition-all duration-[1200ms] ease-out inline-flex items-center bg-brand-accent hover:bg-brand-accent/90 text-white font-poppins font-medium text-sm laptop:text-base px-7 py-3 rounded-full shadow-lg">
                    {{ t('hero.cta') }}
                    <ArrowDown class="h-4" />
                </a>
            </div>
        </div>

        <ScrollIndicator />
    </div>
</template>
