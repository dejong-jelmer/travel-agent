<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import FormActions from '@/Travel/FormActions.vue';
import SearchSelect from '@/Travel/SearchSelect.vue';

const props = defineProps({
    bookingId: {
        type: Number,
        required: true,
    },
    // Null for a new stay.
    stay: {
        type: Object,
        default: null,
    },
    defaults: {
        type: Object,
        default: () => ({}),
    },
    // [{ id, name, city }]
    accommodations: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    accommodation_id: props.stay?.accommodation.id ?? null,
    arrival: props.stay?.arrival ?? props.defaults.arrival ?? '',
    departure: props.stay?.departure ?? '',
});

const options = computed(() =>
    props.accommodations.map((accommodation) => ({ id: accommodation.id, name: accommodation.name, hint: accommodation.city })),
);

const submit = () => {
    const visit = { preserveScroll: true, preserveState: 'errors', onSuccess: () => emit('close') };

    if (props.stay) {
        form.put(route('admin.stays.update', props.stay.id), visit);
    } else {
        form.post(route('admin.bookings.stays.store', props.bookingId), visit);
    }
};
</script>

<template>
    <form class="space-y-6" @submit.prevent="submit">
        <h2 class="text-xl font-semibold text-gray-700">{{ stay ? 'Verblijf bewerken' : 'Verblijf toevoegen' }}</h2>

        <SearchSelect v-model="form.accommodation_id" name="accommodation_id" label="Accommodatie"
            placeholder="Zoek een accommodatie" :options="options" :feedback="form.errors.accommodation_id" required />
        <div class="grid grid-cols-1 tablet:grid-cols-2 gap-4">
            <Input v-model="form.arrival" type="date" name="arrival" label="Aankomst" :feedback="form.errors.arrival" />
            <Input v-model="form.departure" type="date" name="departure" label="Vertrek" :feedback="form.errors.departure" />
        </div>

        <FormActions :processing="form.processing" @cancel="emit('close')" />
    </form>
</template>
