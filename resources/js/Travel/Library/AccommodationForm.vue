<script setup>
import { useForm } from '@inertiajs/vue3';
import FormActions from '@/Travel/FormActions.vue';
import CoordinateFields from '@/Travel/Library/CoordinateFields.vue';
import SearchSelect from '@/Travel/SearchSelect.vue';

const props = defineProps({
    // Null for a new accommodation.
    accommodation: {
        type: Object,
        default: null,
    },
    // [{ id, name, hint }]
    cityOptions: {
        type: Array,
        required: true,
    },
    accommodationTypeOptions: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    city_id: props.accommodation?.city_id ?? null,
    name: props.accommodation?.name ?? '',
    type: props.accommodation?.type ?? 'hotel',
    address: props.accommodation?.address ?? '',
    phone: props.accommodation?.phone ?? '',
    latitude: props.accommodation?.latitude ?? '',
    longitude: props.accommodation?.longitude ?? '',
    check_in: props.accommodation?.check_in ?? '',
    check_out: props.accommodation?.check_out ?? '',
    walk_directions: props.accommodation?.walk_directions ?? '',
});

const submit = () => {
    const visit = { preserveScroll: true, preserveState: 'errors', onSuccess: () => emit('close') };

    if (props.accommodation) {
        form.put(route('admin.accommodations.update', props.accommodation.id), visit);
    } else {
        form.post(route('admin.accommodations.store'), visit);
    }
};
</script>

<template>
    <form class="space-y-6" @submit.prevent="submit">
        <h2 class="text-xl font-semibold text-gray-700">{{ accommodation ? 'Accommodatie bewerken' : 'Accommodatie toevoegen' }}</h2>

        <div class="grid grid-cols-1 tablet:grid-cols-3 gap-4">
            <Input v-model="form.name" name="name" label="Naam" placeholder="Hotel Le Cro-Magnon" :feedback="form.errors.name"
                class="tablet:col-span-2" required />
            <Select v-model="form.type" name="type" label="Soort" :options="accommodationTypeOptions"
                :feedback="form.errors.type" required />
        </div>
        <div class="grid grid-cols-1 tablet:grid-cols-3 gap-4">
            <SearchSelect v-model="form.city_id" name="city_id" label="Plaats" placeholder="Zoek een plaats"
                :options="cityOptions" :feedback="form.errors.city_id" required />
            <Input v-model="form.address" name="address" label="Adres" :feedback="form.errors.address" class="tablet:col-span-2" />
        </div>
        <div class="grid grid-cols-1 tablet:grid-cols-3 gap-4">
            <Input v-model="form.phone" type="tel" name="phone" label="Telefoon" :feedback="form.errors.phone" />
            <Input v-model="form.check_in" type="time" name="check_in" label="Inchecken vanaf" :feedback="form.errors.check_in" />
            <Input v-model="form.check_out" type="time" name="check_out" label="Uitchecken voor" :feedback="form.errors.check_out" />
        </div>
        <CoordinateFields v-model:latitude="form.latitude" v-model:longitude="form.longitude" :errors="form.errors" />
        <TextArea v-model="form.walk_directions" name="walk_directions" label="Looproute"
            description="Van de laatste halte naar de deur, zoals het reisdocument hem toont." :rows="3"
            :feedback="form.errors.walk_directions" />

        <FormActions :processing="form.processing" @cancel="emit('close')" />
    </form>
</template>
