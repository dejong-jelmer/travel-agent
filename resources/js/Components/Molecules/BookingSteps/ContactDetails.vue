<template>
    <div class="space-y-2 p-4 border rounded-lg">
        <div v-if="bookerOptions.length > 1">
            <h2 class="text-base font-bold text-primary-dark mb-2">Wie wordt de hoofboeker?</h2>
            <RadioGroup v-model="booking.main_booker" name="main_booker" :options="bookerOptions" />
            <p class="mt-2">
                Gekozen hoofboeker: {{ formatBookerDetails(props.booking.travelers.adults[props.booking.main_booker]) }}
            </p>
        </div>
        <div class="grid grid-cols-3 gap-6">
            <Input type="text" name="street" label="Straatnaam" :showLabel="true" :required="true"
                v-model="contact.street" :feedback="validator.street.message()"
                :force-show="validator.street.hasError()" />
            <Input type="number" name="house_number" label="Huisnummer" :showLabel="true" :required="true"
                v-model="contact.house_number" :feedback="validator.house_number.message()"
                :force-show="validator.house_number.hasError()" />
            <Input type="text" name="addition" label="Toevoeging (optioneel)" :showLabel="true" :required="false"
                v-model="contact.addition" :feedback="validator.addition.message()"
                :force-show="validator.addition.hasError()" />
        </div>


        <Input type="text" name="postal" label="Postcode" :showLabel="true" :required="true" v-model="contact.postal"
            :feedback="validator.postal.message()" :force-show="validator.postal.hasError()" />

        <Input type="text" name="city" label="Plaatsnaam" :showLabel="true" :required="true" v-model="contact.city"
            :feedback="validator.city.message()" :force-show="validator.city.hasError()" />

        <Input type="tel" name="phone" label="Telefoonnummer" :showLabel="true" :required="true" v-model="contact.phone"
            :feedback="validator.phone.message()" :force-show="validator.phone.hasError()" />

        <Input type="text" name="email" label="Email" :showLabel="true" :required="true" v-model="contact.email"
            :feedback="validator.email.message()" :force-show="validator.email.hasError()" />
    </div>
</template>

<script setup>
import { computed, toRef } from 'vue'

const props = defineProps({
    booking: { type: Object, required: true },
    validator: { type: Object, required: true },
})

const contact = toRef(props.booking, 'contact')

const formatBookerDetails = (booker) => {
    return `${booker?.full_name} (${new Date(booker?.birthdate).toLocaleDateString("nl-NL")})`
}

const bookerOptions = computed(() => {
    if (!props.booking?.travelers?.adults) return []
    return props.booking.travelers.adults.map((adult, index) => ({
        label: formatBookerDetails(adult),
        value: adult
    }))
})


</script>
