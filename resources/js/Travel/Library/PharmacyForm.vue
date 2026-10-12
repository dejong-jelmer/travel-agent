<script setup>
import { useForm } from '@inertiajs/vue3';
import FormActions from '@/Travel/FormActions.vue';

const props = defineProps({
    city: {
        type: Object,
        required: true,
    },
    // Null for a new pharmacy.
    pharmacy: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    name: props.pharmacy?.name ?? '',
    address: props.pharmacy?.address ?? '',
});

const submit = () => {
    const visit = { preserveScroll: true, preserveState: 'errors', onSuccess: () => emit('close') };

    if (props.pharmacy) {
        form.put(route('admin.pharmacies.update', props.pharmacy.id), visit);
    } else {
        form.post(route('admin.cities.pharmacies.store', props.city.id), visit);
    }
};
</script>

<template>
    <form class="space-y-6" @submit.prevent="submit">
        <h2 class="text-xl font-semibold text-gray-700">
            {{ pharmacy ? 'Apotheek bewerken' : 'Apotheek toevoegen' }} in {{ city.name }}
        </h2>

        <Input v-model="form.name" name="name" label="Naam" placeholder="Pharmacie de la Place Picard"
            :feedback="form.errors.name" required />
        <Input v-model="form.address" name="address" label="Adres" :feedback="form.errors.address" />

        <FormActions :processing="form.processing" @cancel="emit('close')" />
    </form>
</template>
