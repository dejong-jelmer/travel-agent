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

// Same as the sizes of the preload link in the head (TripController), so the browser picks the same variant for both
// and fetches it only once. On screens taller than the image, object-cover renders it wider than the viewport, which
// makes the chosen variant a little less sharp there.
const HERO_SIZES = '100vw'
</script>
<template>
    <PageHero :responsive-image="trip.hero_image" :sizes="HERO_SIZES" :image-position="trip.hero_focus"
        :title="trip.name" :subtitle="trip.subtitle"
        :alt="`${trip.name} - ${trip.subtitle}`">
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
