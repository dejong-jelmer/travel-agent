<script setup>
const props = defineProps({
    booking: { type: Object, required: true },
})
</script>

<template>
    <div key="travelers" class="space-y-6">
        <h2 class="text-xl font-bold text-primary-dark">Reisgezelschap</h2>
        <hr class="border-secondary-sage/20">
        <div v-for="(traveler, index) in [{ type: 'adults', label: 'Volwassene' }, { type: 'children', label: 'Kind' }]"
            :key="index">
            <Traveler :booking="booking" :type="traveler.type" :label="traveler.label" />
        </div>
    </div>

</template>
