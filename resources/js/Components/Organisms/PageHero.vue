<script setup>
defineProps({
    image: String,
    // Image model with WebP variants, rendered through ResponsiveImage instead of `image`
    responsiveImage: Object,
    sizes: String,
    // Object-position of the image, e.g. '50% 0%'; without it the browser centres the image
    imagePosition: String,
    title: String,
    subtitle: String,
    alt: { type: String, default: ''},
    width: Number,
    height: Number,
    overlayClass: {
        type: String,
        default: 'bg-gradient-to-b from-transparent via-transparent to-brand-text/85',
    },
    heightClass: {
        type: String,
        default: 'h-hero-page',
    }
})
</script>

<template>
    <section
        class="relative overflow-hidden flex items-end"
        :class="heightClass">
        <ResponsiveImage v-if="responsiveImage" :image="responsiveImage" :sizes="sizes" :alt="alt"
            fetchpriority="high" class="absolute inset-0 h-full w-full object-cover" :style="{ objectPosition: imagePosition }" />
        <img v-else-if="image" :src="image" :alt="alt" :width="width"
            :height="height" fetchpriority="high" class="absolute inset-0 h-full w-full object-cover"
            :style="{ objectPosition: imagePosition }" />
        <div class="absolute inset-0" :class="overlayClass" role="presentation"></div>
        <!-- Darkens the top so the transparent nav stays readable on light photos -->
        <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-brand-text/50 to-transparent" role="presentation"></div>
        <div
            class="relative z-10 w-full max-w-screen-tablet laptop:max-w-screen-desktop mx-auto px-6 laptop:px-8 pb-12 laptop:pb-10">
            <h1 class="text-3xl tablet:text-4xl laptop:text-5xl font-poppins text-white leading-tight text-balance font-bold">
                {{ title }}
            </h1>

            <div
                class="mt-2 flex flex-col gap-y-2 tablet:flex-row tablet:items-start tablet:justify-between tablet:gap-x-10">
                <p class="text-base tablet:text-xl font-poppins text-white">
                    {{ subtitle }}
                </p>

                <div v-if="$slots.meta" class="flex flex-col gap-y-1 tablet:shrink-0 tablet:items-end tablet:text-right">
                    <slot name="meta" />
                </div>
            </div>
        </div>

        <ScrollIndicator :delay="800" />
    </section>
</template>
