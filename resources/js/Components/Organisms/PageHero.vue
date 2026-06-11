<script setup>
defineProps({
    image: String,
    title: String,
    subtitle: String,
    tripMeta: {
        type: Object,
        default: () => ({
            price: null,
            data: []
        })
    },
    overlayClass: {
        type: String,
        default: 'bg-gradient-to-b from-transparent to-brand-text/25',
    },
})
</script>

<template>
    <section class="relative overflow-hidden h-[calc(100vh-theme(spacing.header-phone))] laptop:h-[calc(100vh-theme(spacing.header))] flex items-end"
        :style="`background-image: url(${image}); background-size: cover; background-position: center;`">
        <div class="absolute inset-0" :class="overlayClass" role="presentation"></div>
        <div
            class="relative z-10 w-full max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-6 laptop:px-8 pb-12 laptop:pb-20">
            <h1 class="text-2xl laptop:text-5xl font-poppins text-white leading-tight flex justify-between items-center">
                <span>{{ title }}</span> <span class="text-xl" v-if="tripMeta?.price">
                    {{ tripMeta.price }}
                </span>
            </h1>
            <p class="mt-2 text-base laptop:text-lg text-white/80 font-poppins flex justify-between gap-x-10 laptop:gap-x-0">
                <span class="max-w-xl">{{ subtitle }}</span>
                <span v-if="tripMeta?.data?.length" class="text-right text-base text-white/80">
                    <span v-for="(item, index) in tripMeta?.data" :key="index">
                        {{ item }} <span v-if="index != tripMeta.data.length - 1"> • </span>
                    </span>
                </span>
            </p>
        </div>

        <ScrollIndicator :delay="800" />
    </section>
</template>
