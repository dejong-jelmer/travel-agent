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

<template>
    <div class="space-y-2 p-4 border rounded-lg">
        <p class="font-bold text-brand-primary">{{ label }} {{ index + 1 }}</p>
        <div class="grid grid-cols-2 gap-6">
            <Input
                type="text"
                name="first_name[]"
                :label="$t('traveler_form.first_name_label')"
                :showLabel="true"
                :required="true"
                v-model="traveler.first_name"
                :placeholder="$t('traveler_form.first_name_placeholder')"
                :feedback="feedback?.first_name"
                @keyup="$emit('clearError', 'first_name')"
            />

            <Input
                type="text"
                name="last_name[]"
                :label="$t('traveler_form.last_name_label')"
                :showLabel="true"
                :placeholder="$t('traveler_form.last_name_placeholder')"
                :required="true"
                v-model="traveler.last_name"
                :feedback="feedback?.last_name"
                @keyup="$emit('clearError', 'last_name')"
            />

            <Input
                type="text"
                name="birthdate[]"
                :label="$t('traveler_form.birthdate')"
                :showLabel="true"
                :required="true"
                v-model="traveler.birthdate"
                :placeholder="$t('traveler_form.birthdate_placeholder')"
                :feedback="feedback?.birthdate"
                @keyup="$emit('clearError', 'birthdate')"
                @input="handleBirthdateInput"
                maxlength="10"
            />

            <Input
                type="text"
                name="nationality[]"
                :label="$t('traveler_form.nationality')"
                :placeholder="$t('traveler_form.nationality_placeholder')"
                :showLabel="true"
                :required="true"
                v-model="traveler.nationality"
                :feedback="feedback?.nationality"
                @keyup="$emit('clearError', 'nationality')"
            />
        </div>
    </div>
</template>
