<script setup>
const props = defineProps({
    booking: Object,
    required: true,
});
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
                                Boeking {{ booking.reference }}
                            </h1>
                            <p class="mt-1 text-sm text-gray-700/50">
                                {{ booking.trip?.name }}
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <IconLink type="info" icon="Pencil" :href="route('admin.bookings.edit', booking)"
                                v-tippy="'Bewerk boeking'" />
                            <IconLink type="delete" icon="Trash2" :href="route('admin.bookings.destroy', booking)"
                                method="delete" :showConfirm="true"
                                prompt="Weet je zeker dat je deze boeking wilt verwijderen?"
                                v-tippy="'Verwijder boeking'" />
                        </div>
                    </div>
                </div>

                <!-- Linkerkolom: Boekingsgegevens + Contactgegevens + Reizigers -->
                <div class="laptop:col-span-2 space-y-8">
                    <!-- Boekingsgegevens Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">Boekingsgegevens</h2>
                            <p class="mt-1 text-sm text-gray-700/30">Informatie over de boeking</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Boekingsreferentie</label>
                                <p class="mt-1 text-gray-900">{{ booking.reference }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Reis</label>
                                <DefaultLink :href="route('admin.trips.show', booking.trip)"
                                    class="mt-1 block text-gray-900 hover:text-accent-link">
                                    {{ booking.trip?.name }}
                                </DefaultLink>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Vertrekdatum</label>
                                <p class="mt-1 text-gray-900">{{ booking.departure_date_formatted }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- Contactgegevens Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">Contactgegevens</h2>
                            <p class="mt-1 text-sm text-gray-700/30">Contactpersoon informatie</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Naam</label>
                                <p class="mt-1 text-gray-900">{{ booking.contact?.name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">E-mail</label>
                                <p class="mt-1 text-gray-900">{{ booking.contact?.email }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Telefoon</label>
                                <p class="mt-1 text-gray-900">{{ booking.contact?.phone }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Adres</label>
                                <p class="mt-1 text-gray-900 whitespace-pre-line">{{ booking.contact?.address }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- Reizigers Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">Reizigers</h2>
                            <p class="mt-1 text-sm text-gray-700/30">Overzicht van alle reizigers</p>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 laptop:grid-cols-2 gap-6">
                                <div v-if="booking.adults?.length">
                                    <h3 class="text-sm font-medium text-gray-700 mb-3">Volwassenen</h3>
                                    <div class="space-y-2">
                                        <Booker v-for="(adult, index) in booking.adults" :key="index" :booker="adult"
                                            :index="index" />
                                    </div>
                                </div>
                                <div v-if="booking.children?.length">
                                    <h3 class="text-sm font-medium text-gray-700 mb-3">Kinderen</h3>
                                    <div class="space-y-2">
                                        <Booker v-for="(child, index) in booking.children" :key="index" :booker="child" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Rechterkolom: Status -->
                <div class="laptop:col-start-3 space-y-8">
                    <!-- Status Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">Status</h2>
                            <p class="mt-1 text-sm text-gray-700/30">Boekingsstatus</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">Boeking</span>
                                <BookingStatusBadge :status="booking.status">{{ booking.status }}</BookingStatusBadge>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">Betaling</span>
                                <PaymentStatusBadge :status="booking.payment_status">{{ booking.payment_status }}</PaymentStatusBadge>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </Admin>
</template>
