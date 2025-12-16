<script setup>
import { useI18n } from 'vue-i18n';
import { computed } from 'vue';
import heroImage from '@/../images/hero-image.jpg';

const { tm } = useI18n();

const subtitles = computed(() => {
    const subs = tm('hero.subtitles');
    return Array.isArray(subs) ? subs : [];
});

</script>

<template>
    <div class="h-[calc(100vh-140px)] bg-cover bg-top flex text-white px-6" :style="{ backgroundImage: `url(${heroImage})` }">
        <div class="relative max-w-screen-wide laptop:max-w-screen-desktop w-full mx-auto">
            <div class="absolute top-[35%] tablet:top-[45%]">
                <h1
                    class="text-4xl tablet:text-6xl font-normal leading-10 tablet:leading-[72px] font-elite select-none">
                    {{ $t('hero.title') }}
                    <br>
                    <TypewriterText :texts="subtitles" />
                </h1>
            </div>
        </div>
    </div>
</template>
