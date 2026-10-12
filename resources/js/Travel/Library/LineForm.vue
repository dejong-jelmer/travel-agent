<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import FormActions from '@/Travel/FormActions.vue';

const props = defineProps({
    // Null for a new line.
    line: {
        type: Object,
        default: null,
    },
    transitModeOptions: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    mode: props.line?.mode ?? 'train',
    name: props.line?.name ?? '',
    color: props.line?.color ?? '',
    info_link: props.line?.info_link ?? '',
});

const swatch = computed(() => (/^#[0-9a-fA-F]{6}$/.test(form.color) ? form.color : null));

const submit = () => {
    const visit = { preserveScroll: true, preserveState: 'errors', onSuccess: () => emit('close') };

    if (props.line) {
        form.put(route('admin.transit-lines.update', props.line.id), visit);
    } else {
        form.post(route('admin.transit-lines.store'), visit);
    }
};
</script>

<template>
    <form class="space-y-6" @submit.prevent="submit">
        <h2 class="text-xl font-semibold text-gray-700">{{ line ? 'Lijn bewerken' : 'Lijn toevoegen' }}</h2>

        <div class="grid grid-cols-1 tablet:grid-cols-2 gap-4">
            <Select v-model="form.mode" name="mode" label="Vervoermiddel" :options="transitModeOptions"
                :feedback="form.errors.mode" required />
            <Input v-model="form.name" name="name" label="Naam" placeholder="Tram B" :feedback="form.errors.name" required />
        </div>
        <div class="grid grid-cols-1 tablet:grid-cols-2 gap-4">
            <div class="flex items-end gap-3">
                <Input v-model="form.color" name="color" label="Kleur" placeholder="#e4003f" :feedback="form.errors.color"
                    class="flex-1" />
                <span class="mb-2 h-6 w-6 flex-shrink-0 rounded-full border border-gray-200"
                    :class="{ 'bg-gray-100': !swatch }" :style="swatch ? { backgroundColor: swatch } : null"
                    aria-hidden="true" />
            </div>
            <Input v-model="form.info_link" type="url" name="info_link" label="Informatielink" placeholder="https://"
                :feedback="form.errors.info_link" />
        </div>

        <FormActions :processing="form.processing" @cancel="emit('close')" />
    </form>
</template>
