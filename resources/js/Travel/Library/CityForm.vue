<script setup>
import { useForm } from '@inertiajs/vue3';
import FormActions from '@/Travel/FormActions.vue';

const props = defineProps({
    // Null for a new city.
    city: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    name: props.city?.name ?? '',
    country: props.city?.country ?? '',
});

const submit = () => {
    const visit = { preserveScroll: true, preserveState: 'errors', onSuccess: () => emit('close') };

    if (props.city) {
        form.put(route('admin.cities.update', props.city.id), visit);
    } else {
        form.post(route('admin.cities.store'), visit);
    }
};
</script>

<template>
    <form class="space-y-6" @submit.prevent="submit">
        <h2 class="text-xl font-semibold text-gray-700">{{ city ? 'Plaats bewerken' : 'Plaats toevoegen' }}</h2>

        <div class="grid grid-cols-1 tablet:grid-cols-3 gap-4">
            <Input v-model="form.name" name="name" label="Naam" placeholder="Bordeaux" :feedback="form.errors.name"
                class="tablet:col-span-2" required />
            <Input v-model="form.country" name="country" label="Landcode" placeholder="FR" maxlength="2"
                :feedback="form.errors.country" class="uppercase" required />
        </div>

        <FormActions :processing="form.processing" @cancel="emit('close')" />
    </form>
</template>
