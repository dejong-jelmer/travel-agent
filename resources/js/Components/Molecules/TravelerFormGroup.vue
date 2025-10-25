<template>
    <div class="space-y-2 p-4 border rounded-lg">
        <p class="font-bold text-primary-default">{{ label }} {{ index + 1 }}</p>
        <div class="grid grid-cols-2 gap-6">
            <Input
                type="text"
                name="first_name[]"
                label="Eerste voornaam (zoals in paspoort)"
                :showLabel="true"
                :required="true"
                v-model="traveler.first_name"
                placeholder="Voornaam"
                :feedback="feedback?.first_name"
                @keyup="$emit('clearError', 'first_name')"
            />

            <Input
                type="text"
                name="last_name[]"
                label="Achternaam (zoals in paspoort)"
                :showLabel="true"
                placeholder="Achternaam"
                :required="true"
                v-model="traveler.last_name"
                :feedback="feedback?.last_name"
                @keyup="$emit('clearError', 'last_name')"
            />

            <Input
                type="text"
                name="birthdate[]"
                label="Geboortedatum"
                :showLabel="true"
                :required="true"
                v-model="traveler.birthdate"
                placeholder="DD-MM-JJJJ"
                :feedback="feedback?.birthdate"
                @keyup="$emit('clearError', 'birthdate')"
                @input="handleBirthdateInput"
                maxlength="10"
            />

            <Input
                type="text"
                name="nationality[]"
                label="Nationaliteit"
                placeholder="Nationaliteit"
                :showLabel="true"
                :required="true"
                v-model="traveler.nationality"
                :feedback="feedback?.nationality"
                @keyup="$emit('clearError', 'nationality')"
            />
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useDateFormatter } from '@/Composables/useDateFormatter.js';

const { initializeBirthdate, formatBirthDateInput } = useDateFormatter();

const props = defineProps({
    traveler: {
        type: Object,
        required: true
    },
    index: {
        type: Number,
        required: true
    },
    label: {
        type: String,
        required: true
    },
    feedback: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['clearError']);

onMounted(() => {
    props.traveler.birthdate = initializeBirthdate(props.traveler.birthdate);
});

const handleBirthdateInput = (event) => {
    formatBirthDateInput(event, (formatted) => {
        props.traveler.birthdate = formatted;
    });
};
</script>
