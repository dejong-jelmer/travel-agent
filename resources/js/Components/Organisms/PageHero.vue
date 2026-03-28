<script setup>
defineProps({
    image: String,
    title: String,
    subtitle: String,
    overlayClass: {
        type: String,
        default: 'bg-brand-text/25',
    },
})
</script>

<template>
    <section class="relative overflow-hidden h-[calc(100vh-theme(spacing.header))] flex items-end"
        :style="`background-image: url(${image}); background-size: cover; background-position: center;`">
        <div class="absolute inset-0" :class="overlayClass"></div>

        <div
            class="relative z-10 w-full max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-4 pb-12 laptop:pb-20">
            <h1 class="text-2xl laptop:text-5xl font-poppins text-white leading-tight">
                {{ title }}
            </h1>
            <p class="mt-2 text-base laptop:text-lg text-white/80 font-poppins max-w-xl">
                {{ subtitle }}
            </p>
        </div>
    </section>
</template>
