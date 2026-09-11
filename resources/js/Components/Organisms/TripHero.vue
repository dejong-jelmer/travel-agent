<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const props = defineProps({
    trip: { type: Object, required: true }
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

// The image covers the whole hero (object-cover), so on screens that are tall relative to the image it is
// rendered wider than the viewport. The hero heights match height-class below (hero-offset in tailwind.config.js).
// Media queries on the aspect ratio are used instead of max(), which Firefox ignores in sizes. They leave out the
// hero offset, so close to the tipping point the browser may pick one variant smaller.
const heroSizes = computed(() => {
    const source = props.trip.hero_image?.fallback_source ?? props.trip.hero_image
    const ratio = source?.width && source?.height ? source.width / source.height : 1
    const tallerThanImage = `(max-aspect-ratio: ${Math.round(ratio * 1000)}/1000)`
    const factor = ratio.toFixed(3)

    return [
        `(min-width: 900px) and ${tallerThanImage} calc((100vh - 140px) * ${factor})`,
        '(min-width: 900px) 100vw',
        `${tallerThanImage} calc((100vh - 250px) * ${factor})`,
        '100vw',
    ].join(', ')
})
</script>
<template>
    <PageHero :responsive-image="trip.hero_image" :sizes="heroSizes" :title="trip.name" :subtitle="trip.intro"
        :alt="`${trip.name} - ${trip.intro}`"
        height-class="h-[calc(100vh-theme(hero-offset.phone.trip))] laptop:h-[calc(100vh-theme(hero-offset.laptop))]">
        <template #meta>
            <p class="text-base tablet:text-xl font-poppins text-white">
                {{ tripMeta.price }}
            </p>

            <ul v-if="tripMeta.data.length" class="flex flex-wrap gap-x-2 text-base font-poppins text-white">
                <li v-for="(item, index) in tripMeta.data" :key="index"
                    class="after:ml-2 after:content-['•'] last:after:content-none">
                    {{ item }}
                </li>
            </ul>
        </template>
    </PageHero>
</template>
