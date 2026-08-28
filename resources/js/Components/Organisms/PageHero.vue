<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const props = defineProps({
    trip: Object,
    overlayClass: {
        type: String,
        default: 'bg-gradient-to-b from-transparent via-transparent to-brand-text/85',
    },
})
const { t } = useI18n()

const tripMeta = computed(() => ({
    price: props.trip.is_expected
        ? t('trip_show.hero.expected')
        : `${t('trip_show.hero.from_price', { price: props.trip.price_formatted })} ${t('trip_show.hero.per_person')}`,
    data: [
        props.trip.destinations_formatted,
        t('trip_show.hero.days', {
            duration: props.trip.duration
        })
    ]
}))
</script>

<template>
    <section
        class="relative overflow-hidden h-[calc(100vh-theme(spacing.header-phone))] laptop:h-[calc(100vh-theme(spacing.header))] flex items-end"
        :style="`background-image: url(${trip.hero_image?.public_url}); background-size: cover; background-position: center;`">
        <div class="absolute inset-0" :class="overlayClass" role="presentation"></div>
        <div
            class="relative z-10 w-full max-w-screen-tablet laptop:max-w-screen-desktop mx-auto px-6 laptop:px-8 pb-12 laptop:pb-20">
            <h1 class="text-3xl tablet:text-4xl laptop:text-5xl font-poppins text-white leading-tight text-balance">
                {{ trip.name }}
            </h1>

            <div
                class="mt-2 flex flex-col gap-y-2 tablet:flex-row tablet:items-start tablet:justify-between tablet:gap-x-10">
                <p class="max-w-xl text-base tablet:text-xl font-poppins text-white">
                    {{ trip.intro }}
                </p>

                <div class="flex flex-col gap-y-1 tablet:shrink-0 tablet:items-end tablet:text-right">
                    <p v-if="tripMeta?.price" class="text-base tablet:text-xl font-poppins text-white">
                        {{ tripMeta.price }}
                    </p>

                    <ul v-if="tripMeta?.data?.length" class="flex flex-wrap gap-x-2 text-base font-poppins text-white">
                        <li v-for="(item, index) in tripMeta.data" :key="index"
                            class="after:ml-2 after:content-['•'] last:after:content-none">
                            {{ item }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <ScrollIndicator :delay="800" />
    </section>
</template>
