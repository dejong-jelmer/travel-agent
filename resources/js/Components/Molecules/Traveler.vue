<template>
    <div v-for="(traveler, index) in travelers" :key="index" class="space-y-2 p-4 border rounded-lg">
                <p class="font-bold text-primary-default">{{ label }} {{ index + 1 }}</p>
                <div class="grid grid-cols-2 gap-6">
                    <Input type="text" name="first_name[]" label="Eerste voornaam (zoals in paspoort)" :showLabel="true"
                        :required="true" v-model="traveler.first_name" :placeholder="'Voornaam'"
                        :feedback="booking.errors[`travelers.${type}.${index}.first_name`]"
                        @keyup="booking.clearErrors(`travelers.${type}.${index}.first_name`)" />

                    <Input type="text" name="last_name[]" label="Achternaam (zoals in paspoort)" :showLabel="true"
                        :placeholder="'Achternaam'" :required="true" v-model="traveler.last_name"
                        :feedback="booking.errors[`travelers.${type}.${index}.last_name`]"
                        @keyup="booking.clearErrors(`travelers.${type}.${index}.last_name`)" />

                    <Input type="text" name="birthdate[]" label="Geboorte datum" :showLabel="true" :required="true"
                        v-model="traveler.birthdate" :placeholder="'DD-MM-JJJJ'"
                        :feedback="booking.errors[`travelers.${type}.${index}.birthdate`]"
                        @keyup="booking.clearErrors(`travelers.${type}.${index}.birthdate`)"
                        @input="formatBirthDate($event, index)" maxlength="10" />

                    <Input type="text" name="nationality[]" label="Nationaliteit" :placeholder="'NL of BE'"
                        :showLabel="true" :required="true" v-model="traveler.nationality"
                        :feedback="booking.errors[`travelers.${type}.${index}.nationality`]"
                        @keyup="booking.clearErrors(`travelers.${type}.${index}.nationality`)" />
                </div>
            </div>
</template>
<script setup>
import { toRef } from 'vue'

const props = defineProps({
    booking: { type: Object, required: true },
    type: { type: String, required: true },
    label: { type: String, required: true }
})
const travelers = toRef(props.booking.travelers, props.type)

const formatBirthDate = (event, index) => {
    let value = event.target.value.replace(/[^\d]/g, '');

    if (value.length >= 2) {
        value = value.slice(0, 2) + '-' + value.slice(2);
    }
    if (value.length >= 5) {
        value = value.slice(0, 5) + '-' + value.slice(5);
    }

    travelers.value[index].birthdate = value.slice(0, 10);
};
</script>
