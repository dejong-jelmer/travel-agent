<script setup>
import { computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { TriangleAlert } from '@lucide/vue';
import { formatDate } from './format.js';

// Cancels a departure, with its bookings, for a reason. It cannot be undone, so it says what it does first.
const props = defineProps({
    departure: { type: Object, required: true },
});

const emit = defineEmits(['close']);

const locale = computed(() => usePage().props.locale);
const form = useForm({ reason: '' });

// Past the decision date the minimum can no longer be relied on to cancel
const decisionPassed = computed(() => props.departure.decision_date < new Date().toLocaleDateString('sv-SE'));

function submit() {
    form.post(route('admin.departures.cancel', props.departure.id), {
        preserveScroll: true,
        preserveState: 'errors',
        onSuccess: () => emit('close'),
    });
}
</script>

<template>
    <form class="space-y-5" @submit.prevent="submit">
        <h2 class="text-xl font-bold text-gray-700">{{ $t('admin.departures.cancel.title') }}</h2>

        <p class="text-brand-text">
            {{ $t('admin.departures.cancel.intro', { date: formatDate(departure.departs_on, locale) }) }}
        </p>

        <p v-if="departure.is_guaranteed" data-testid="warning-guaranteed"
            class="flex gap-3 text-sm text-brand-text bg-status-error/10 border border-status-error rounded-lg p-3">
            <TriangleAlert class="h-5 w-5 flex-shrink-0 text-status-error" />
            {{ $t('admin.departures.cancel.warning_guaranteed') }}
        </p>
        <p v-if="decisionPassed" data-testid="warning-decision-passed"
            class="flex gap-3 text-sm text-brand-text bg-status-warning/10 border border-status-warning rounded-lg p-3">
            <TriangleAlert class="h-5 w-5 flex-shrink-0 text-status-warning" />
            {{ $t('admin.departures.cancel.warning_decision_passed', { date: formatDate(departure.decision_date, locale) }) }}
        </p>

        <p class="text-sm text-brand-text">
            {{ departure.occupied_seats > 0
                ? $t('admin.departures.cancel.bookings', { seats: departure.occupied_seats })
                : $t('admin.departures.cancel.no_bookings') }}
        </p>

        <TextArea name="reason" v-model="form.reason" :required="true" :rows="3"
            :label="$t('admin.departures.cancel.reason')"
            :placeholder="$t('admin.departures.cancel.reason_placeholder')" :feedback="form.errors.reason" />

        <div class="flex justify-end gap-3">
            <Button type="button" color="primary" @click="emit('close')">
                {{ $t('admin.departures.cancel.back') }}
            </Button>
            <Button type="submit" :disabled="form.processing">{{ $t('admin.departures.cancel.submit') }}</Button>
        </div>
    </form>
</template>
