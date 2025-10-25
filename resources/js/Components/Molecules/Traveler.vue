<template>
    <TravelerFormGroup
        v-for="(traveler, index) in travelers"
        :key="index"
        :traveler="traveler"
        :index="index"
        :label="label"
        :feedback="{
            first_name: booking.errors[`travelers.${type}.${index}.first_name`],
            last_name: booking.errors[`travelers.${type}.${index}.last_name`],
            birthdate: booking.errors[`travelers.${type}.${index}.birthdate`],
            nationality: booking.errors[`travelers.${type}.${index}.nationality`]
        }"
        @clear-error="(field) => booking.clearErrors(`travelers.${type}.${index}.${field}`)"
    />
</template>

<script setup>
import { toRef } from 'vue';

const props = defineProps({
    booking: { type: Object, required: true },
    type: { type: String, required: true },
    label: { type: String, required: true }
});

const travelers = toRef(props.booking.travelers, props.type);
</script>
