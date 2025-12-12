<script setup>
const props = defineProps({
    booking: { type: Object, required: true },
})
</script>

<template>
    <div key="travelers" class="space-y-6">
        <h2 class="text-xl font-bold text-brand-primary">{{ $t('booking_steps.travelers.heading') }}</h2>
        <hr class="border-accent-sage/20">
        <div v-for="(traveler, index) in [{ type: 'adults', label: $t('booking_steps.travelers.adult_label') }, { type: 'children', label: $t('booking_steps.travelers.child_label') }]"
            :key="index">
            <Traveler :booking="booking" :type="traveler.type" :label="traveler.label" />
        </div>
    </div>

</template>
