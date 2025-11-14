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

                <!-- Linkerkolom: Boekingsgegevens + Reisgezelschap -->
                <div class="laptop:col-span-2 space-y-8">
                    <!-- Boekingsgegevens Section (Read-only) -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">Boekingsgegevens</h2>
                            <p class="mt-1 text-sm text-gray-700/30">Informatie over de boeking</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="grid grid-cols-1 laptop:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">Boekingsreferentie</label>
                                        <p class="mt-1 text-gray-900">{{ db_booking.reference }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">Reis</label>
                                        <p class="mt-1 text-gray-900">{{ db_booking.product?.name }}</p>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">Vertrekdatum</label>
                                        <p class="mt-1 text-gray-900">{{ db_booking.departure_date_formatted }}</p>
                                    </div>
                                </div>
                            </div>
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

                <!-- Rechterkolom: Contactgegevens -->
                <div class="laptop:col-start-3 space-y-8">
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
                </div>
            </div>

            <!-- Footer Actions -->
            <div
                class="laptop:col-span-3 flex items-center justify-between border-t border-gray-200 bg-white rounded-lg mt-6 p-6 shadow-sm">
                <p class="text-sm text-gray-700/30">
                    <span v-if="booking.isDirty" class="text-status-warning font-medium">
                        Er zijn niet opgeslagen wijzigingen
                    </span>
                    <span v-else class="text-status-success">
                        Alles opgeslagen
                    </span>
                </p>
                <button type="button" @click="submit()"
                    class="inline-flex items-center px-6 py-3 bg-status-warning text-white font-semibold rounded-lg hover:bg-opacity-90 transition-all duration-200 shadow-md hover:shadow-lg"
                    :disabled="booking.processing">
                    <span v-if="!booking.processing">Boeking Opslaan</span>
                    <span v-else class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Bezig met opslaan...
                    </span>
                </button>
            </div>
        </div>
    </Admin>
</template>
