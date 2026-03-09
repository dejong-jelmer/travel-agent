<script setup>
import { ref, onMounted } from 'vue';
import heroVideo from '@/../videos/177314-857365374.mp4';


const videoRef = ref(null)
const visibleTitle = ref(false)
const visibleSubTitle = ref(false)

onMounted(() => {
    if (videoRef.value) {
        videoRef.value.playbackRate = 0.5
    }
    setTimeout(() => {
        visibleTitle.value = true
    }, 3000)
    setTimeout(() => {
        visibleSubTitle.value = true
    }, 8000)
})

</script>

<template>
    <div class="relative h-[calc(100vh-100px)] flex px-6 overflow-hidden">
        <video ref="videoRef" class="absolute inset-0 w-full h-full object-cover scale-x-[-1]" :src="heroVideo" autoplay
            muted loop playsinline />

        <div class="relative max-w-screen-wide laptop:max-w-screen-desktop w-fit mx-auto">
            <div class="absolute top-[80%] tablet:top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
                <!-- <h1 class="text-brand-secondary font-nunito text-nowrap font-light text-4xl tablet:text-5xl laptop:text-7xl select-none">
                    <span :class="visibleTitle ? 'opacity-100' : 'opacity-0'" class="block tablet:inline transition-opacity duration-[5000ms] ease-in-out">{{ $t('hero.title') }}</span>
                    <span class="hidden tablet:inline">&nbsp;</span>
                    <span :class="visibleSubTitle ? 'opacity-100' : 'opacity-0'" class="block tablet:inline transition-opacity duration-[5000ms] ease-in-out tracking-wider">{{ $t('hero.sub_title') }}</span>
                </h1> -->
            </div>
        </div>
    </div>
</template>
