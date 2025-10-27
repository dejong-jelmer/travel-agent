<script setup>
import { useBooking } from '@/Composables/useBooking.js';

const props = defineProps({
    db_booking: Object,
    required: true
})

const mainBookerIndex = props.db_booking.adults.findIndex((adult) => adult.id === props.db_booking.main_booker?.id)

const { booking } = useBooking(
    props.db_booking.product,
    props.db_booking,
    mainBookerIndex,
)

function submit() {
    booking.put(route("admin.bookings.update", props.db_booking));
}
</script>

<template>
    <Admin>
        <div class="max-w-7xl mx-auto grid gap-8">
            <div class="w-full flex justify-end">
                <IconLink icon="Save" @click="submit()" v-tippy="'Sla wijzigingen in deze boeking op'" class="mx-0"
                    :class="{ 'animate-bounce': booking.isDirty }" :showConfirm="true"
                    prompt="Weet je zeker dat je deze boeking wilt aanpassen?" />
            </div>
            <!-- Trip details -->
            <div class="bg-white p-6 tablet:p-8 rounded-2xl shadow-lg border border-secondary-stone/40">
                <h2 class="text-2xl font-bold text-primary-dark">
                    Boekingsgegevens
                </h2>
                <div class="grid grid-cols-2">
                    <div class="grid">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold">Boekingsreferentie:</span>
                            <span class="">{{ props.db_booking.reference }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-semibold">Reis:</span>
                            <span class="">{{ props.db_booking.product?.name }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-semibold">Vertrekdatum:</span>
                            <span class="">{{ props.db_booking.departure_date_formatted }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="grid">
                            <span class="font-semibold">Contact gegevens:</span>
                            <span class="">{{ props.db_booking.contact?.name }}</span>
                            <span class="whitespace-pre-line">{{ props.db_booking.contact?.address }}</span>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Reisgezelschap & Contact -->
            <div class="grid gap-6 tablet:grid-cols-2">

                <!-- Reisgezelschap -->
                <div class="bg-white p-8 rounded-2xl shadow-lg space-y-6">
                    <h2 class="text-2xl font-bold text-primary-dark">Reisgezelschap</h2>
                    <Traveler :booking="booking" type="adults" label="Volwassene" />
                    <Traveler :booking="booking" type="children" label="Kind" />
                </div>

                <!-- Contactgegevens -->
                <div class="bg-white p-8 rounded-2xl shadow-lg space-y-6">
                    <h2 class="text-2xl font-bold text-primary-dark">Contactgegevens</h2>
                    <Contact :booking="booking" />
                </div>
            </div>
        </div>
    </Admin>

</template>
