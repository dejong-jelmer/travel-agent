<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { Pencil, ShieldCheck, Ban, Trash2, TriangleAlert, CalendarClock, Users } from '@lucide/vue';
import DepartureProgress from './DepartureProgress.vue';
import DepartureStatusBadge from './DepartureStatusBadge.vue';
import { formatDate } from './format.js';
import { formatCents } from '@/Support/money.js';

// One departure: its dates, occupancy, what needs attention, and the actions it can take.
const props = defineProps({
    departure: { type: Object, required: true },
    // Name the trip, on pages that list the departures of several trips
    showTrip: { type: Boolean, default: false },
    // Leave the actions and the link to the participant list out, on the page of the departure itself
    actions: { type: Boolean, default: true },
});

defineEmits(['edit', 'guarantee', 'cancel', 'destroy']);

const { t } = useI18n();
const locale = computed(() => usePage().props.locale);

const date = (value) => formatDate(value, locale.value);
const price = (cents) => (cents === null ? t('admin.departures.price.from_trip') : formatCents(cents, locale.value));
</script>

<template>
    <article :data-testid="`departure-${departure.id}`" class="bg-white rounded-lg border shadow-sm p-6 space-y-4"
        :class="departure.needs_attention ? 'border-status-warning' : 'border-gray-200'">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <p v-if="showTrip && departure.trip" class="text-sm font-semibold text-brand-primary">
                    <Link :href="route('admin.trips.departures.index', departure.trip)" class="hover:underline">
                        {{ departure.trip.name }}
                    </Link>
                </p>
                <h2 class="text-lg font-semibold text-gray-700">
                    {{ date(departure.departs_on) }} – {{ date(departure.returns_on) }}
                </h2>
                <p class="text-sm text-gray-700/50">
                    {{ $t('admin.departures.days', { count: departure.duration_in_days }) }}
                </p>
            </div>
            <DepartureStatusBadge :status="departure.status" :is-guaranteed="departure.is_guaranteed" />
        </div>

        <DepartureProgress :occupied="departure.occupied_seats" :confirmed="departure.confirmed_participants"
            :min="departure.min_participants" :max="departure.max_participants" />

        <!-- What needs the admin's attention -->
        <p v-if="departure.decision_overdue" data-testid="decision-overdue"
            class="flex gap-3 text-sm text-brand-text bg-status-warning/10 border border-status-warning rounded-lg p-3">
            <TriangleAlert class="h-5 w-5 flex-shrink-0 text-status-warning" />
            {{ $t('admin.departures.decision.overdue') }}
        </p>
        <p v-else-if="departure.awaits_guarantee" data-testid="awaits-guarantee"
            class="flex gap-3 text-sm text-brand-text bg-status-success/10 border border-status-success rounded-lg p-3">
            <ShieldCheck class="h-5 w-5 flex-shrink-0 text-status-success" />
            {{ $t('admin.departures.decision.awaits_guarantee') }}
        </p>
        <p v-else-if="departure.decision_due_soon" data-testid="decision-due-soon"
            class="flex gap-3 text-sm text-brand-text bg-status-warning/10 border border-status-warning rounded-lg p-3">
            <TriangleAlert class="h-5 w-5 flex-shrink-0 text-status-warning" />
            <!-- The count picks the singular or the plural -->
            {{ $t('admin.departures.decision.due_soon', {
                count: departure.days_until_decision,
                missing: departure.min_participants - departure.confirmed_participants,
            }) }}
        </p>

        <dl class="grid grid-cols-1 tablet:grid-cols-3 gap-4 text-sm">
            <div>
                <dt class="text-gray-700/50">{{ $t('admin.departures.price.label') }}</dt>
                <dd class="text-brand-text">{{ price(departure.price_per_person) }}</dd>
            </div>
            <div>
                <dt class="text-gray-700/50">{{ $t('admin.departures.price.supplement') }}</dt>
                <dd class="text-brand-text">{{ price(departure.single_supplement) }}</dd>
            </div>
            <div>
                <dt class="text-gray-700/50">{{ $t('admin.departures.decision.label') }}</dt>
                <dd class="text-brand-text inline-flex items-center gap-2">
                    <CalendarClock class="h-4 w-4 text-brand-light" /> {{ date(departure.decision_date) }}
                </dd>
            </div>
        </dl>

        <p v-if="departure.guaranteed_at" class="text-sm text-gray-600">
            {{ $t('admin.departures.guaranteed_on', { date: date(departure.guaranteed_at) }) }}
        </p>
        <div v-if="departure.cancelled_at" class="text-sm text-gray-600">
            <p>{{ $t('admin.departures.cancelled_on', { date: date(departure.cancelled_at) }) }}</p>
            <p><span class="text-gray-700/50">{{ $t('admin.departures.reason') }}:</span>
                {{ departure.cancellation_reason }}</p>
        </div>

        <div v-if="actions" class="flex flex-wrap gap-2 pt-2 border-t border-gray-100">
            <Link :href="route('admin.departures.show', departure.id)" class="departure-action" data-testid="participants">
                <Users class="h-4 w-4" /> {{ $t('admin.departures.actions.participants') }}
            </Link>
            <button v-if="departure.can_be_edited" type="button" class="departure-action" @click="$emit('edit')">
                <Pencil class="h-4 w-4" /> {{ $t('admin.departures.actions.edit') }}
            </button>
            <button v-if="departure.can_be_guaranteed" type="button" data-testid="guarantee" class="departure-action"
                @click="$emit('guarantee')">
                <ShieldCheck class="h-4 w-4" /> {{ $t('admin.departures.actions.guarantee') }}
            </button>
            <button v-if="departure.can_be_cancelled" type="button" data-testid="cancel" class="departure-action"
                @click="$emit('cancel')">
                <Ban class="h-4 w-4" /> {{ $t('admin.departures.actions.cancel') }}
            </button>
            <button type="button" class="departure-action hover:!border-status-error hover:!text-status-error"
                @click="$emit('destroy')">
                <Trash2 class="h-4 w-4" /> {{ $t('admin.departures.actions.delete') }}
            </button>
        </div>
    </article>
</template>

<style scoped>
.departure-action {
    @apply inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm text-brand-primary transition-colors hover:border-brand-primary;
}
</style>
