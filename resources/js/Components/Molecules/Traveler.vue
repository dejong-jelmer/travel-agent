<script setup>
import { computed } from 'vue';

const props = defineProps({
    booking: { type: Object, required: true },
    type: { type: String, required: true },
    label: { type: String, required: true },
    readonly: { type: Boolean, default: false }
});

const travelers = computed(() => props.booking.travelers?.[props.type] || []);
</script>

<template>
    <div class="grid gap-y-4">
        <TravelerFormGroup v-for="(traveler, index) in travelers" :key="index" :traveler="traveler" :index="index"
            :label="label" :readonly="readonly" :feedback="{
                first_name: booking.errors[`travelers.${type}.${index}.first_name`],
                last_name: booking.errors[`travelers.${type}.${index}.last_name`],
                birthdate: booking.errors[`travelers.${type}.${index}.birthdate`],
                nationality: booking.errors[`travelers.${type}.${index}.nationality`],
                special_requests: booking.errors[`travelers.${type}.${index}.special_requests`],
                special_requests_consent: booking.errors[`travelers.${type}.${index}.special_requests_consent`]
            }" @clear-error="(field) => booking.clearErrors(`travelers.${type}.${index}.${field}`)" />
    </div>
</template>
