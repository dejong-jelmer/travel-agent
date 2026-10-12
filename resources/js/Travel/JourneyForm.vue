<script setup>
import { useForm } from '@inertiajs/vue3';
import FormActions from '@/Travel/FormActions.vue';

const props = defineProps({
    bookingId: {
        type: Number,
        required: true,
    },
    // Null for a new journey.
    journey: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    label: props.journey?.label ?? '',
    date: props.journey?.date ?? '',
});

const submit = () => {
    const options = { preserveScroll: true, preserveState: 'errors', onSuccess: () => emit('close') };

    if (props.journey) {
        form.put(route('admin.journeys.update', props.journey.id), options);
    } else {
        form.post(route('admin.bookings.journeys.store', props.bookingId), options);
    }
};
</script>

<template>
    <form class="space-y-6" @submit.prevent="submit">
        <h2 class="text-xl font-semibold text-gray-700">{{ journey ? 'Reis bewerken' : 'Reis toevoegen' }}</h2>

        <Input v-model="form.label" name="label" label="Naam" placeholder="Heenreis" :feedback="form.errors.label" required />
        <Input v-model="form.date" type="date" name="date" label="Datum" :feedback="form.errors.date" />
        <p v-if="journey?.legs.length" class="text-sm text-gray-500">
            Een andere datum verschuift de tijden van de etappes niet: pas die zo nodig zelf aan.
        </p>

        <FormActions :processing="form.processing" @cancel="emit('close')" />
    </form>
</template>
