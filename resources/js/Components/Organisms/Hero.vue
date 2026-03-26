<script setup>
import { ref, computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import heroVideo from '@/../videos/home-hero.mp4';
import heroImage from '@/../images/hero-poster.webp';
import { useMq } from 'vue3-mq';

const { t } = useI18n();
const mq = useMq();

const videoRef = ref(null)
const visibleTitle = ref(false)
const visibleSubTitle = ref(false)
const visibleCta = ref(false)
const prefersReducedMotion = ref(false)

const showVideo = computed(() => mq.tablet || mq.laptop || mq.desktop || mq.wide)

onMounted(() => {
    prefersReducedMotion.value = window.matchMedia('(prefers-reduced-motion: reduce)').matches

    if (videoRef.value && !prefersReducedMotion.value) {
        videoRef.value.playbackRate = 0.8
    }
    setTimeout(() => {
        visibleTitle.value = true
    }, 50)
    setTimeout(() => {
        visibleSubTitle.value = true
    }, 2500)
    setTimeout(() => {
        visibleCta.value = true
    }, 4000)
})
</script>

<template>
    <div class="relative h-[calc(100vh-100px)] flex px-6 overflow-hidden">
        <video v-if="showVideo" ref="videoRef" :poster="heroImage" class="absolute inset-0 w-full h-full object-cover scale-x-[-1]" preload="none" :src="heroVideo" :autoplay="!prefersReducedMotion"
            muted loop playsinline />
        <img v-else :src="heroImage" class="absolute inset-0 w-full h-full object-cover" alt="" />

        <!-- Overlay -->
        <div class="absolute inset-0 bg-brand-text/30"></div>

        <div class="relative max-w-screen-wide laptop:max-w-screen-desktop w-fit mx-auto">
<<<<<<< HEAD
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 flex flex-col items-center gap-6">
=======
            <div class="absolute top-[80%] tablet:top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 flex flex-col items-center gap-6">
>>>>>>> b9e884b3fa401a1a668de43a24b0f92b9502b33e
                <h1 class="text-brand-secondary font-nunito text-nowrap font-light text-4xl tablet:text-5xl laptop:text-7xl select-none text-center drop-shadow-lg">
                    <span :class="visibleTitle ? 'opacity-100' : 'opacity-0'" class="block tablet:inline transition-opacity duration-[2000ms] ease-in-out">{{ t('hero.title') }}</span>
                    <span class="hidden tablet:inline">&nbsp;</span>
                    <span :class="visibleSubTitle ? 'opacity-100' : 'opacity-0'" class="block tablet:inline transition-opacity duration-[2000ms] ease-in-out tracking-wider">{{ t('hero.sub_title') }}</span>
                </h1>
                <a
                    href="#trips"
                    :class="visibleCta ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                    class="transition-all duration-[1500ms] ease-out inline-block bg-brand-accent hover:bg-brand-accent/90 text-white font-poppins font-medium text-sm laptop:text-base px-7 py-3 rounded-full shadow-lg"
                >
                    {{ t('hero.cta') }}
                </a>
            </div>
        </div>
    </div>
</template>
