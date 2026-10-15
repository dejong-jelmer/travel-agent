<script setup>
import { useForm } from '@inertiajs/vue3';
import FormActions from '@/Travel/FormActions.vue';
import CoordinateFields from '@/Travel/Library/CoordinateFields.vue';
import SearchSelect from '@/Travel/SearchSelect.vue';

const props = defineProps({
    // Null for a new station.
    station: {
        type: Object,
        default: null,
    },
    // [{ id, name, hint }]
    cityOptions: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    city_id: props.station?.city_id ?? null,
    name: props.station?.name ?? '',
    latitude: props.station?.latitude ?? '',
    longitude: props.station?.longitude ?? '',
});

const submit = () => {
    const visit = { preserveScroll: true, preserveState: 'errors', onSuccess: () => emit('close') };

    if (props.station) {
        form.put(route('admin.stations.update', props.station.id), visit);
    } else {
        form.post(route('admin.stations.store'), visit);
    }
};
</script>

<template>
    <form class="space-y-6" @submit.prevent="submit">
        <h2 class="text-xl font-semibold text-gray-700">{{ station ? 'Station bewerken' : 'Station toevoegen' }}</h2>

        <div class="grid grid-cols-1 tablet:grid-cols-2 gap-4">
            <SearchSelect v-model="form.city_id" name="city_id" label="Plaats" placeholder="Zoek een plaats"
                :options="cityOptions" :feedback="form.errors.city_id" required />
            <Input v-model="form.name" name="name" label="Naam" placeholder="Bordeaux Saint-Jean"
                :feedback="form.errors.name" required />
        </div>
        <CoordinateFields v-model:latitude="form.latitude" v-model:longitude="form.longitude" :errors="form.errors" />

        <FormActions :processing="form.processing" @cancel="emit('close')" />
    </form>
</template>
