<script setup>
import { useForm } from '@inertiajs/vue3';

// Adds a departure to a trip, or edits the given one.
const props = defineProps({
    trip: { type: Object, required: true },
    departure: { type: Object, default: null },
});

const emit = defineEmits(['close']);

const euros = (cents) => (cents === null || cents === undefined ? '' : String(cents / 100));

const form = useForm({
    departs_on: props.departure?.departs_on ?? '',
    returns_on: props.departure?.returns_on ?? '',
    min_participants: props.departure?.min_participants ?? '',
    max_participants: props.departure?.max_participants ?? '',
    // In euros; the server stores cents
    price_per_person: euros(props.departure?.price_per_person),
    single_supplement: euros(props.departure?.single_supplement),
    decision_date: props.departure?.decision_date ?? '',
    guaranteed: false,
});

function submit() {
    // A save reloads the page, an error keeps the modal and what was typed
    const visit = { preserveScroll: true, preserveState: 'errors', onSuccess: () => emit('close') };

    if (props.departure) {
        form.put(route('admin.departures.update', props.departure.id), visit);
    } else {
        form.post(route('admin.trips.departures.store', props.trip), visit);
    }
}
</script>

<template>
    <form class="space-y-6" @submit.prevent="submit">
        <h2 class="text-xl font-bold text-gray-700">
            {{ departure ? $t('admin.departures.form.edit_title') : $t('admin.departures.form.create_title') }}
        </h2>

        <p v-if="departure && departure.occupied_seats > 0"
            class="text-sm text-brand-text bg-status-warning/10 border border-status-warning rounded-lg p-3">
            {{ $t('admin.departures.form.dates_move_bookings') }}
        </p>

        <div class="grid grid-cols-1 tablet:grid-cols-2 gap-4">
            <Input type="date" name="departs_on" v-model="form.departs_on" :required="true"
                :label="$t('admin.departures.form.departs_on')" :feedback="form.errors.departs_on" />
            <Input type="date" name="returns_on" v-model="form.returns_on" :required="true" :min="form.departs_on"
                :label="$t('admin.departures.form.returns_on')" :feedback="form.errors.returns_on" />

            <Input type="number" name="min_participants" v-model="form.min_participants" :required="true" min="1"
                :label="$t('admin.departures.form.min_participants')" :feedback="form.errors.min_participants" />
            <Input type="number" name="max_participants" v-model="form.max_participants" :required="true" min="1"
                :label="$t('admin.departures.form.max_participants')" :feedback="form.errors.max_participants" />

            <div>
                <Input type="number" name="price_per_person" v-model="form.price_per_person" min="0" step="0.01"
                    :label="$t('admin.departures.form.price_per_person')" :feedback="form.errors.price_per_person" />
                <p class="mt-1 text-xs text-gray-700/50">{{ $t('admin.departures.form.price_help') }}</p>
            </div>
            <div>
                <Input type="number" name="single_supplement" v-model="form.single_supplement" min="0" step="0.01"
                    :label="$t('admin.departures.form.single_supplement')" :feedback="form.errors.single_supplement" />
                <p class="mt-1 text-xs text-gray-700/50">{{ $t('admin.departures.form.supplement_help') }}</p>
            </div>

            <div class="tablet:col-span-2">
                <Input type="date" name="decision_date" v-model="form.decision_date" :max="form.departs_on"
                    :label="$t('admin.departures.form.decision_date')" :feedback="form.errors.decision_date" />
                <p class="mt-1 text-xs text-gray-700/50">{{ $t('admin.departures.form.decision_help') }}</p>
            </div>

            <div v-if="!departure" class="tablet:col-span-2 p-4 rounded-lg border border-gray-200">
                <Checkbox v-model="form.guaranteed" name="guaranteed">
                    <span class="font-medium text-gray-700">{{ $t('admin.departures.form.guaranteed') }}</span>
                    <span class="block text-xs text-gray-700/50 mt-1">
                        {{ $t('admin.departures.form.guaranteed_help') }}
                    </span>
                </Checkbox>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <Button type="button" color="primary" @click="emit('close')">
                {{ $t('admin.departures.form.close') }}
            </Button>
            <Button type="submit" :disabled="form.processing">{{ $t('admin.departures.form.save') }}</Button>
        </div>
    </form>
</template>
