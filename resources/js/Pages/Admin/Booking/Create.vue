<script setup>
import { reactive } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useBooking } from '@/Composables/useBooking.js'
import { applyDeparture } from '@/Composables/useDeparturePrice.js'

const props = defineProps({
    trips: {
        type: Object,
        required: true
    },
    // The request the booking is made from: { id, trip_id, departure_id, travelers_count, single_room, name, email,
    // phone }, or null
    fromRequest: {
        type: Object,
        default: null
    }
})

const MAX_TRAVELERS = 6

// Reactive object ipv ref: door in-place te muteren blijven useBooking's
// computed properties (constraints, disabledDates) automatisch synchroon.
const selectedTrip = reactive({ ...(props.trips.find(t => t.id === props.fromRequest?.trip_id) ?? props.trips[0]) })

const booking = useBooking(selectedTrip)

// Fill in what the request already tells: the departure, the travelers, a single room and the contact details
if (props.fromRequest) {
    const form = booking.booking
    const request = props.fromRequest

    form.trip_request_id = request.id
    form.participants.adults = Math.min(Math.max(request.travelers_count || 1, 1), MAX_TRAVELERS)

    const departure = selectedTrip.departures?.find(d => d.id === request.departure_id)
    if (departure) applyDeparture(form, departure)
    if (request.single_room) form.single_rooms = 1

    form.contact.email = request.email ?? ''
    form.contact.phone = request.phone ?? ''

    // The name goes to the first adult, split at the first space; the travelers are watched into place above
    const [firstName, ...lastName] = (request.name ?? '').trim().split(/\s+/)
    const mainBooker = form.travelers.adults[0]
    if (mainBooker) {
        mainBooker.first_name = firstName ?? ''
        mainBooker.last_name = lastName.join(' ')
    }
}

function onTripChange(id) {
    const found = props.trips.find(t => t.id == id)
    if (found) {
        Object.assign(selectedTrip, found)
        booking.booking.trip = found
        booking.booking.departure_date = null
        booking.booking.return_date = null
        // The departure belongs to the trip that was selected before
        booking.booking.departure_id = null
        booking.booking.departure = null
        booking.booking.single_rooms = null
    }
}
</script>
<template>
    <Admin>
        <div class="max-w-wide mx-auto space-y-8">
            <div class="bg-white py-10">
                <h1 class="text-3xl font-bold text-gray-700">{{ $t('admin.booking.create.title') }}</h1>
                <p class="mt-1 text-sm text-gray-700/50">{{ $t('admin.booking.create.subtitle') }}</p>
            </div>

            <p v-if="fromRequest" data-testid="from-request"
                class="text-sm text-brand-text bg-brand-secondary/60 border border-brand-primary/10 rounded-lg px-4 py-3">
                {{ $t('admin.booking.create.from_request', { name: fromRequest.name }) }}
                <Link :href="route('admin.trip-requests.edit', fromRequest.id)" class="ml-1 text-brand-link underline">
                    {{ $t('admin.booking.create.view_request') }}
                </Link>
            </p>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-700">{{ $t('admin.booking.create.select_trip') }}</h2>
                </div>
                <div class="p-6">
                    <Select
                        :options="trips"
                        optionValue="name"
                        :modelValue="String(selectedTrip.id)"
                        @update:modelValue="onTripChange"
                    />
                </div>
            </div>

            <BookingForm
                :booking="booking.booking"
                :constraints="booking.constraints"
                :disabled-dates="booking.disabledDates.value"
            />
        </div>
    </Admin>
</template>
