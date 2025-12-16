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

                <div class="laptop:col-span-2 space-y-8">
                    <!-- Booking Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">{{ $t('admin.booking.show.details') }}</h2>
                            <p class="mt-1 text-sm text-gray-700/30">{{ $t('admin.booking.show.info') }}</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">{{ $t('admin.booking.show.reference') }}</label>
                                <p class="mt-1 text-gray-900">{{ booking.reference }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">{{ $t('admin.booking.show.trip') }}</label>
                                <DefaultLink :href="route('admin.trips.show', booking.trip)"
                                    class="mt-1 block text-gray-900 hover:text-accent-link">
                                    {{ booking.trip?.name }}
                                </DefaultLink>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">{{ $t('admin.booking.show.departure_date') }}</label>
                                <p class="mt-1 text-gray-900">{{ booking.departure_date_formatted }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- Contact Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">{{ $t('admin.booking.show.contact') }}</h2>
                            <p class="mt-1 text-sm text-gray-700/30">{{ $t('admin.booking.show.contact_info') }}</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">{{ $t('admin.booking.show.contact_name') }}</label>
                                <p class="mt-1 text-gray-900">{{ booking.contact?.name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">{{ $t('admin.booking.show.contact_email') }}</label>
                                <p class="mt-1 text-gray-900">{{ booking.contact?.email }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">{{ $t('admin.booking.show.contact_telephone') }}</label>
                                <p class="mt-1 text-gray-900">{{ booking.contact?.phone }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">{{ $t('admin.booking.show.contact_address') }}</label>
                                <p class="mt-1 text-gray-900 whitespace-pre-line">{{ booking.contact?.address }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- Travelers Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">{{ $t('admin.booking.show.travelers') }}</h2>
                            <p class="mt-1 text-sm text-gray-700/30">{{ $t('admin.booking.show.travelers_subheader') }}</p>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 laptop:grid-cols-2 gap-6">
                                <div v-if="booking.adults?.length">
                                    <h3 class="text-sm font-medium text-gray-700 mb-3">{{ $t('booker.adults', booking.adults.length) }}</h3>
                                    <div class="space-y-2">
                                        <Booker v-for="(adult, index) in booking.adults" :key="index" :booker="adult"
                                            :index="index" />
                                    </div>
                                </div>
                                <div v-if="booking.children?.length">
                                    <h3 class="text-sm font-medium text-gray-700 mb-3">{{ $t('booker.children', booking.children.length) }}</h3>
                                    <div class="space-y-2">
                                        <Booker v-for="(child, index) in booking.children" :key="index" :booker="child" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="laptop:col-start-3 space-y-8">
                    <!-- Status Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">{{ $t('admin.booking.show.booking_status') }}</h2>
                            <p class="mt-1 text-sm text-gray-700/30">{{ $t('admin.booking.show.contact_address') }}</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">{{ $t('admin.booking.show.booking_status') }}</span>
                                <BookingStatusBadge :status="booking.status">{{ booking.status_label }}</BookingStatusBadge>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">{{ $t('admin.booking.show.payment_status') }}</span>
                                <PaymentStatusBadge :status="booking.payment_status">{{ booking.payment_status_label }}</PaymentStatusBadge>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </Admin>
</template>
