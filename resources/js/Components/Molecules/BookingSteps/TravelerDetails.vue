<template>
    <div v-for="(traveler, index) in travelers" :key="index" class="space-y-2 p-4 border rounded-lg">
        <p>{{ label }} {{ index + 1 }}</p>
        <Input type="text" name="first_name[]" label="Eerste voornaam (zoals in paspoort)" :showLabel="true"
            :required="true" v-model="traveler.first_name" :feedback="errorMessage(type, index, 'first_name')"
            :force-show="hasError(type, index, 'first_name')" />

        <Input type="text" name="last_name[]" label="Achternaam (zoals in paspoort)" :showLabel="true" :required="true"
            v-model="traveler.last_name" :feedback="errorMessage(type, index, 'last_name')"
            :force-show="hasError(type, index, 'last_name')" />

        <DatePicker v-model="traveler.birthdate" :max-date="new Date()" :min-date="minDate" @mouseup="booking.clearErrors(`travelers.${type}.${index}.birthdate`)"
            :feedback="errorMessage(type, index, 'birthdate')" :force-show="hasError(type, index, 'birthdate')" />

        <FormFeedback v-if="index == booking.main_booker && type === 'adults'" :message="booking.errors.main_booker" :class="'font-bold'" />

        <Input type="text" name="nationality[]" label="Nationaliteit" :showLabel="true" :required="true"
            v-model="traveler.nationality" :feedback="errorMessage(type, index, 'nationality')"
            :force-show="hasError(type, index, 'nationality')" />
    </div>
</template>

<script setup>
import { toRef } from 'vue'

const props = defineProps({
    booking: { type: Object, required: true },
    validator: { type: Array, required: true },
    type: { type: String, required: true },
    label: { type: String, required: true },
    maxDate: { type: [Date, String], default: null },
    minDate: { type: [Date, String], default: null },
})

const travelers = toRef(props.booking.travelers, props.type)

const errorMessage = (type, index, field) => {
    return props.booking.errors[`travelers.${type}.${index}.${field}`] ||
        props.validator[index]?.[field]?.message()
}

const hasError = (type, index, field) => {
    return !!props.booking.errors[`travelers.${type}.${index}.${field}`] ||
        props.validator[index]?.[field]?.hasError()
}

</script>
