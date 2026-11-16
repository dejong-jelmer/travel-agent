<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { formatDate } from '@/Departures/format.js';
import { formatCents } from '@/Support/money.js';

// The departures of a group trip to book on, instead of free dates. Picking one sets the dates of the booking.
const props = defineProps({
    booking: { type: Object, required: true },
});

const page = usePage();
const locale = computed(() => page.props.locale);
const departures = computed(() => props.booking.trip?.departures ?? []);

const canBeBooked = (departure) => departure.available_seats > 0 && departure.booking_price_per_person !== null;

function pick(departure) {
    if (!canBeBooked(departure)) return;

    props.booking.departure_id = departure.id;
    props.booking.departure = {
        id: departure.id,
        departs_on: departure.departs_on,
        returns_on: departure.returns_on,
        available_seats: departure.available_seats,
        price_per_person: departure.booking_price_per_person,
        single_supplement: departure.booking_single_supplement,
    };
    props.booking.departure_date = new Date(`${departure.departs_on}T00:00:00`);
    props.booking.return_date = new Date(`${departure.returns_on}T00:00:00`);
    props.booking.clearErrors('departure_id');
}
</script>

<template>
    <div class="bg-brand-secondary/40 border border-brand-primary/10 rounded-xl p-4 space-y-3"
        :data-error="booking.errors['departure_id'] ? 'true' : null">
        <div class="flex justify-between items-center">
            <p class="text-xs font-semibold uppercase tracking-wide text-brand-light">
                {{ $t('booking_steps.trip.choose_departure') }}
            </p>
            <span class="text-sm text-brand-text">
                {{ booking.departure
                    ? `${formatDate(booking.departure.departs_on, locale)} – ${formatDate(booking.departure.returns_on, locale)}`
                    : $t('booking_steps.trip.no_departure_chosen') }}
            </span>
        </div>

        <p class="text-xs text-brand-light">{{ $t('booking_steps.trip.group_notice') }}</p>

        <div v-if="departures.length === 0" class="text-sm text-brand-text space-y-2" data-testid="no-departures">
            <p>{{ $t('booking_steps.trip.no_departures') }}</p>
            <a :href="route('admin.trips.departures.index', booking.trip)" class="text-brand-link underline">
                {{ $t('booking_steps.trip.manage_departures') }}
            </a>
        </div>

        <ul v-else class="grid gap-2" role="radiogroup" :aria-label="$t('booking_steps.trip.choose_departure')">
            <li v-for="departure in departures" :key="departure.id">
                <button type="button" role="radio" :aria-checked="booking.departure_id === departure.id"
                    :disabled="!canBeBooked(departure)" :data-testid="`pick-departure-${departure.id}`"
                    class="w-full text-left rounded-lg border bg-white px-4 py-3 flex flex-wrap items-center justify-between gap-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    :class="booking.departure_id === departure.id ? 'border-brand-primary ring-2 ring-brand-primary/30' : 'border-gray-200 hover:border-brand-primary'"
                    @click="pick(departure)">
                    <span>
                        <span class="block font-semibold text-brand-primary">
                            {{ formatDate(departure.departs_on, locale) }} – {{ formatDate(departure.returns_on, locale) }}
                        </span>
                        <span class="block text-xs text-brand-text/70">
                            {{ departure.status.label }} ·
                            {{ departure.available_seats > 0
                                ? $t('booking_steps.trip.seats_left', { count: departure.available_seats })
                                : $t('booking_steps.trip.full') }}
                        </span>
                    </span>
                    <span class="text-sm text-brand-text">
                        <template v-if="departure.booking_price_per_person !== null">
                            <strong>{{ formatCents(departure.booking_price_per_person, locale) }}</strong>
                            {{ $t('booking_steps.trip.per_person') }}
                        </template>
                        <template v-else>{{ $t('booking_steps.trip.no_price') }}</template>
                    </span>
                </button>
            </li>
        </ul>

        <FormFeedback v-if="booking.errors['departure_id']" :message="booking.errors['departure_id']" />
    </div>
</template>
