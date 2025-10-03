<template>
    <div key="trip" class="space-y-4">
        <div class="space-y-2">
            <h2 class="text-xl font-bold text-primary-dark">
                Reis boeken - {{ booking.trip?.name }}
            </h2>
            <p class="text-primary-default leading-relaxed">
                Wat leuk dat je de reis <strong class="text-primary-dark">{{ booking.trip?.name }}</strong>
                wilt gaan boeken.
                We gaan een aantal stappen doorlopen om te zorgen dat de boeking goed doorkomt.
            </p>
            <div class="bg-accent-sand/20 border border-accent-sand rounded-lg p-4">
                <p class="text-sm text-primary-dark">
                    <strong>Let op:</strong> Dit is een <strong>boekingsaanvraag</strong>. We kijken eerst
                    of we aan
                    alle wensen kunnen voldoen en of er voldoende beschikbaarheid is. Na het verzenden nemen
                    we binnen
                    <strong>twee werkdagen</strong> contact met u op om de boeking te bevestigen.
                </p>
            </div>
        </div>

        <hr class="border-secondary-sage/20">

        <div class="grid grid-cols-3 gap-2">
            <p>Reis</p>
            <p><strong>{{ booking.trip.name }}</strong></p>
            <p class="ml-[150px]">Vanaf <strong>€ {{ booking.trip.price }},-</strong> p.p.</p>

            <p>Kies een datum voor vertrek</p>
            <DatePicker v-model="departure_date" :min-date="new Date()" :feedback="booking.errors['departure_date']"
                @mouseup="booking.clearErrors('departure_date')" />
            <p></p>

            <p>Kies het aantal reizigers</p>
            <PersonPicker v-model="participants" />
            <span></span>
        </div>
    </div>

</template>

<script setup>
import { toRef } from 'vue'

const props = defineProps({
    booking: { type: Object, required: true },
    constraints: Object
})

const departure_date = toRef(props.booking, 'departure_date')
const participants = toRef(props.booking, 'participants')

</script>
