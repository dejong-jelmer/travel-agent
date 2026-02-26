<script setup>
import { ref, onMounted } from 'vue';
import heroVideo from '@/../videos/177314-857365374.mp4';


const videoRef = ref(null)
const visible = ref(false)

onMounted(() => {
    if (videoRef.value) {
        videoRef.value.playbackRate = 0.5
    }
    setTimeout(() => {
        visible.value = true
    }, 5000)
})

</script>

<template>
    <div class="relative h-[calc(100vh-140px)] flex px-6 overflow-hidden">
        <video ref="videoRef" class="absolute inset-0 w-full h-full object-cover scale-x-[-1]" :src="heroVideo" autoplay
            muted loop playsinline />

        <div class="relative max-w-screen-wide laptop:max-w-screen-desktop w-full mx-auto">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full">
                <h1 class="text-[#f4f1ec] font-caveat font-light text-5xl laptop:text-8xl transition-opacity duration-[5000ms] ease-in-out select-none"
                :class="visible ? 'opacity-100' : 'opacity-0'">
                    {{ $t('hero.title') }}
                </h1>
            </div>
        </div>
    </div>
</template>
