<template>
    <div class="grid grid-cols-3 gap-2">
        <p>Reis</p>
        <p><strong>{{ booking.trip.name }}</strong></p>
        <p class="ml-[150px]">Vanaf <strong>€ {{ booking.trip.price }},-</strong> p.p.</p>

        <p>Kies een datum voor vertrek</p>
        <DatePicker
            v-model="departure_date"
            :min-date="new Date()"
            :max-date="booking.constrains.maxDate"
            :feedback="validator.departure_date.message()"
            :forceShow="validator.departure_date.hasError()" />
        <p></p>

        <p>Kies het aantal reizigers</p>
        <PersonPicker v-model="participants" />
        <span></span>
    </div>
</template>

<script setup>
import { toRef } from 'vue'

const props = defineProps({
    booking: { type: Object, required: true },
    validator: { type: Object, required: true },
})

const departure_date = toRef(props.booking, 'departure_date')
const participants = toRef(props.booking, 'participants')

</script>
