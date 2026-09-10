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
</script>
<template>
    <PageHero :image="trip.hero_image?.public_url" :title="trip.name" :subtitle="trip.intro"
        :alt="`${trip.name} - ${trip.intro}`" :width="trip.hero_image?.width" :height="trip.hero_image?.height"
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
