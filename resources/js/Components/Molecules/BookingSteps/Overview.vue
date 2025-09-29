<script setup>
import { computed } from 'vue'

const props = defineProps({
    booking: {
        type: Object,
        required: true
    }
})

const formattedDate = (date, longMonth = true) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('nl-NL', {
        day: 'numeric',
        month: longMonth ? 'long' : 'numeric',
        year: 'numeric'
    })
}
</script>

<template>
    <div class="p-6 bg-neutral-50 rounded-2xl shadow-md space-y-6">
        <section>
            <h2 class="text-xl font-semibold text-primary-dark mb-2">Reis</h2>
            <p><strong>{{ booking.trip.name }}</strong></p>
            <p>Duur: <strong>{{ booking.trip.duration }}</strong> dagen</p>
            <p>Prijs: <strong>€ {{ booking.trip.price }},-</strong></p>
            <p>Vertrek: <strong>{{ formattedDate(booking.departure_date) }}</strong> </p>
        </section>
        <section>
            <h2 class="text-xl font-semibold text-primary-dark mb-2">Reizigers</h2>
            <div v-if="booking.travelers.adults.length" class="grid grid-cols-2">
                <div>
                    <h3 class="font-medium text-secondary-stone">Volwassenen (totaal: {{ booking.participants.adults }})
                    </h3>
                    <ul class="list-disc ml-5">
                        <li v-for="(adult, i) in booking.travelers.adults" :key="i">
                            {{ adult.full_name }}
                            ({{ formattedDate(adult.birthdate, false) }} – {{ adult.nationality }})
                        </li>
                    </ul>
                </div>
                <div v-if="booking.travelers.children.length" class="">
                    <h3 class="font-medium text-secondary-stone">Kinderen (totaal: {{ booking.participants.children }})
                    </h3>
                    <ul class="list-disc ml-5">
                        <li v-for="(child, i) in booking.travelers.children" :key="i">
                            {{ child.full_name }}
                            ({{ formattedDate(child.birthdate, false) }} – {{ child.nationality }})
                        </li>
                    </ul>
                </div>
            </div>


            <p v-if="!booking.travelers.adults.length && !booking.travelers.children.length" class="text-gray-500">
                Nog geen reizigers ingevoerd.
            </p>
        </section>

        <!-- Contact -->
        <section>
            <h2 class="text-xl font-semibold text-primary-dark mb-2">Contactgegevens</h2>
            <p>{{ booking.travelers.adults?.[booking.main_booker]?.full_name }} <span v-if="booking.participants.adults > 1">(hoofboeker)</span></p>
            <p>{{ booking.contact.street }}</p>
            <p>{{ booking.contact.postal }} {{ booking.contact.city }}</p>
            <p>{{ booking.contact.phone }}</p>
            <p>{{ booking.contact.email }}</p>
        </section>
    </div>
    <Checkbox v-model="booking.confirmed" :label="'Alle gegevens zijn juist ingevuld en ik wil mijn boekingsaanvraag definitief maken.'" :feedback="booking.errors.confirmed" />
</template>
