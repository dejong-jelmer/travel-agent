<script setup>
import { reactive } from 'vue';
import { useBooking } from '@/Composables/useBooking.js'

const props = defineProps({
    trips: {
        type: Object,
        required: true
    }
})

// Reactive object ipv ref: door in-place te muteren blijven useBooking's
// computed properties (constraints, disabledDates) automatisch synchroon.
const selectedTrip = reactive({ ...props.trips[0] })

const booking = useBooking(selectedTrip)

function onTripChange(id) {
    const found = props.trips.find(t => t.id == id)
    if (found) {
        Object.assign(selectedTrip, found)
        booking.booking.trip = found
        booking.booking.departure_date = null
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
