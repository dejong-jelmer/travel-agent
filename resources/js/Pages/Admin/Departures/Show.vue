<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import DepartureCard from '@/Departures/DepartureCard.vue';
import { formatCents } from '@/Support/money.js';

// A departure with its participant list: every booking on it with its travelers.
const props = defineProps({
    departure: { type: Object, required: true },
    bookings: { type: Array, required: true },
    // Shape: [{ id, name, email, travelers, single_room, interest_only, status, status_label, created_at_formatted }],
    // the requests on the departure that are still to handle
    tripRequests: { type: Array, default: () => [] },
});

const locale = computed(() => usePage().props.locale);

const active = computed(() => props.bookings.filter((booking) => booking.occupies_seat));
const canceled = computed(() => props.bookings.filter((booking) => !booking.occupies_seat));

const singleRooms = computed(() => active.value.reduce((sum, booking) => sum + booking.single_rooms, 0));
const specialRequests = computed(() => active.value.flatMap((booking) => booking.travelers
    .filter((traveler) => traveler.special_requests)
    .map((traveler) => ({ ...traveler, reference: booking.reference }))));
</script>

<template>
    <Admin>
        <div class="max-w-wide mx-auto space-y-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-700">{{ $t('admin.departures.show.title') }}</h1>
                <p class="mt-1 text-sm text-gray-700/50">
                    <Link :href="route('admin.trips.departures.index', departure.trip)" class="text-brand-link underline">
                        {{ departure.trip.name }}
                    </Link>
                </p>
            </div>

            <DepartureCard :departure="departure" :actions="false" />

            <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="border-b border-gray-200 px-6 py-4 flex flex-wrap items-baseline justify-between gap-2">
                    <h2 class="text-lg font-semibold text-gray-700">{{ $t('admin.departures.show.participants') }}</h2>
                    <p class="text-sm text-gray-600" data-testid="totals">
                        {{ $t('admin.departures.show.totals', {
                            participants: departure.occupied_seats,
                            bookings: active.length,
                            rooms: singleRooms,
                        }) }}
                    </p>
                </div>

                <p v-if="active.length === 0" class="p-6 text-brand-text">{{ $t('admin.departures.show.empty') }}</p>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full text-sm" data-testid="participants-table">
                        <thead class="bg-gray-50 text-left text-gray-600">
                            <tr>
                                <th class="px-6 py-3 font-medium">{{ $t('admin.departures.show.columns.booking') }}</th>
                                <th class="px-6 py-3 font-medium">{{ $t('admin.departures.show.columns.travelers') }}</th>
                                <th class="px-6 py-3 font-medium">{{ $t('admin.departures.show.columns.contact') }}</th>
                                <th class="px-6 py-3 font-medium">{{ $t('admin.departures.show.columns.single_rooms') }}</th>
                                <th class="px-6 py-3 font-medium">{{ $t('admin.departures.show.columns.status') }}</th>
                                <th class="px-6 py-3 font-medium">{{ $t('admin.departures.show.columns.payment') }}</th>
                                <th class="px-6 py-3 font-medium text-right">{{ $t('admin.departures.show.columns.price') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="booking in active" :key="booking.id" class="align-top">
                                <td class="px-6 py-4">
                                    <Link :href="route('admin.bookings.show', booking.id)" class="font-semibold text-brand-primary hover:underline">
                                        {{ booking.reference }}
                                    </Link>
                                </td>
                                <td class="px-6 py-4">
                                    <ul class="space-y-1">
                                        <li v-for="traveler in booking.travelers" :key="traveler.id">
                                            <span :class="traveler.full_name === booking.main_booker ? 'font-semibold' : ''">{{ traveler.full_name }}</span>
                                            <span class="text-gray-700/50"> · {{ traveler.birthdate }}</span>
                                            <span v-if="traveler.type === 'child'" class="text-gray-700/50"> · {{ $t('admin.departures.show.child') }}</span>
                                        </li>
                                        <!-- An anonymized booking has no travelers left, only how many there were -->
                                        <li v-if="booking.travelers.length === 0" class="text-gray-700/50">
                                            {{ $t('admin.departures.show.anonymized', { count: booking.participants }) }}
                                        </li>
                                    </ul>
                                </td>
                                <td class="px-6 py-4">
                                    <a v-if="booking.email" :href="`mailto:${booking.email}`" class="block text-brand-link hover:underline">{{ booking.email }}</a>
                                    <a v-if="booking.phone" :href="`tel:${booking.phone}`" class="block text-brand-link hover:underline">{{ booking.phone }}</a>
                                </td>
                                <td class="px-6 py-4">{{ booking.single_rooms }}</td>
                                <td class="px-6 py-4">
                                    <BookingStatusBadge :status="booking.status">{{ booking.status_label }}</BookingStatusBadge>
                                </td>
                                <td class="px-6 py-4">
                                    <PaymentStatusBadge :status="booking.payment_status">{{ booking.payment_status_label }}</PaymentStatusBadge>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">{{ formatCents(booking.display_price, locale) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section v-if="tripRequests.length" data-testid="open-trip-requests"
                class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-700">{{ $t('admin.departures.show.open_requests') }}</h2>
                    <p class="mt-1 text-sm text-gray-700/50">{{ $t('admin.departures.show.open_requests_help') }}</p>
                </div>
                <ul class="divide-y divide-gray-100">
                    <li v-for="request in tripRequests" :key="request.id"
                        class="px-6 py-3 text-sm flex flex-wrap items-center justify-between gap-2">
                        <span>
                            <Link :href="route('admin.trip-requests.edit', request.id)" class="font-semibold text-brand-primary hover:underline">
                                {{ request.name }}
                            </Link>
                            <span class="text-gray-600">
                                · {{ $t('admin.departures.show.persons', { count: request.travelers }) }}
                                <template v-if="request.single_room"> · {{ $t('admin.departures.show.single_room') }}</template>
                                · {{ request.created_at_formatted }}
                            </span>
                        </span>
                        <span class="flex items-center gap-2">
                            <span v-if="request.interest_only"
                                class="rounded-full bg-brand-earth/45 px-2.5 py-0.5 text-xs font-medium text-brand-text">
                                {{ $t('admin.trip_request.interest_short') }}
                            </span>
                            <TripRequestStatusBadge :status="request.status">{{ request.status_label }}</TripRequestStatusBadge>
                        </span>
                    </li>
                </ul>
            </section>

            <section v-if="specialRequests.length" data-testid="special-requests"
                class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-700">{{ $t('admin.departures.show.special_requests') }}</h2>
                </div>
                <ul class="divide-y divide-gray-100">
                    <li v-for="traveler in specialRequests" :key="traveler.id" class="px-6 py-4 text-sm">
                        <p class="font-semibold text-brand-text">{{ traveler.full_name }}
                            <span class="font-normal text-gray-700/50">· {{ traveler.reference }}</span>
                        </p>
                        <p class="mt-1 text-brand-text whitespace-pre-line">{{ traveler.special_requests }}</p>
                    </li>
                </ul>
            </section>

            <section v-if="canceled.length" data-testid="canceled-bookings"
                class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-700">{{ $t('admin.departures.show.canceled') }}</h2>
                </div>
                <ul class="divide-y divide-gray-100">
                    <li v-for="booking in canceled" :key="booking.id" class="px-6 py-3 text-sm flex flex-wrap justify-between gap-2">
                        <Link :href="route('admin.bookings.show', booking.id)" class="text-brand-primary hover:underline">
                            {{ booking.reference }}
                        </Link>
                        <span class="text-gray-600">{{ booking.main_booker }} · {{ $t('admin.departures.show.persons', { count: booking.participants }) }}</span>
                    </li>
                </ul>
            </section>
        </div>
    </Admin>
</template>
