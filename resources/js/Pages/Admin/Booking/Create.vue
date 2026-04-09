<script setup>
import { ref, computed, toRef, watch } from 'vue';
import { useBooking } from '@/Composables/useBooking.js'
const props = defineProps({
    trip: {
        type: Object,
        required: true
    }
})

// Booking data
const booking = useBooking(props.trip)

const departure_date = toRef(booking.booking, 'departure_date')
const participants = toRef(booking.booking, 'participants')
watch(
    () => booking.booking.hasErrors,
    (newValue) => {
        if (newValue) {
            bookingModalOpen.value = true
        }
    }
)
</script>
<template>
    <Admin>
        <div class="max-w-wide mx-auto">

            <BookingForm :booking="booking.booking" :constraints="booking.constraints"
                :disabled-dates="booking.disabledDates.value" />

        </div>
    </Admin>

</template>
