<script setup>
import { useBooking } from '@/Composables/useBooking.js';

const props = defineProps({
    db_booking: Object,
    statusOptions: Object,
    paymentStatusOptions: Object,
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
        <div class="max-w-wide mx-auto">
            <div class="grid grid-cols-1 laptop:grid-cols-3 gap-8">
                <!-- Header Section -->
                <div class="laptop:col-span-3 bg-white py-10">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-700">
                                Boeking {{ db_booking.reference }} bewerken
                            </h1>
                            <p class="mt-1 text-sm text-gray-700/50">
                                {{ db_booking.product?.name }}
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <IconLink type="delete" icon="Trash2" v-tippy="'Verwijder boeking!'"
                                :href="route('admin.bookings.destroy', db_booking.id)"
                                :showConfirm="true" prompt="Weet je zeker dat je deze boeking wilt verwijderen?" />
                        </div>
                    </div>
                </div>

                <!-- Linkerkolom: Boekingsgegevens + Contactgegevens + Reisgezelschap -->
                <div class="laptop:col-span-2 space-y-8">
                    <!-- Boekingsgegevens Section (Read-only) -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">Boekingsgegevens</h2>
                            <p class="mt-1 text-sm text-gray-700/30">Informatie over de boeking</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Boekingsreferentie</label>
                                <p class="mt-1 text-gray-900">{{ db_booking.reference }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Reis</label>
                                <p class="mt-1 text-gray-900">{{ db_booking.product?.name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Vertrekdatum</label>
                                <p class="mt-1 text-gray-900">{{ db_booking.departure_date_formatted }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- Contactgegevens Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">Contactgegevens</h2>
                            <p class="mt-1 text-sm text-gray-700/30">Beheer contactpersoon</p>
                        </div>
                        <div class="p-6">
                            <Contact :booking="booking" />
                        </div>
                    </section>

                    <!-- Reisgezelschap Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">Reisgezelschap</h2>
                            <p class="mt-1 text-sm text-gray-700/30">Beheer reizigers</p>
                        </div>
                        <div class="p-6 space-y-6">
                            <Traveler :booking="booking" type="adults" label="Volwassene" />
                            <Traveler :booking="booking" type="children" label="Kind" />
                        </div>
                    </section>
                </div>

                <!-- Rechterkolom: Status -->
                <div class="laptop:col-start-3 space-y-8">
                    <!-- Status Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">Status</h2>
                            <p class="mt-1 text-sm text-gray-700/30">Beheer boekingsstatus</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <Select v-model="booking.status" name="status" label="Boeking" :options="statusOptions" placeholder="Selecteer de status van de boeking" />
                            <Select v-model="booking.payment_status" name="payment_status" label="Betaling" :options="paymentStatusOptions" placeholder="Selecteer een betaalstatus" />
                        </div>
                    </section>
                </div>
            </div>

            <!-- Footer Actions -->
           <FormFooter :form="booking" label="Wijzigingen opslaan" @submit="submit()" />
        </div>
    </Admin>
</template>
