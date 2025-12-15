<script setup>
import { useBooking } from '@/Composables/useBooking.js';
import { computed } from 'vue';

const props = defineProps({
    db_booking: Object,
    statusOptions: Object,
    paymentStatusOptions: Object,
    required: true
})

const mainBookerIndex = props.db_booking.adults?.findIndex(
    (adult) => adult.id === props.db_booking.main_booker?.id
) ?? 0;

const { booking } = useBooking(
    props.db_booking.trip,
    props.db_booking,
    mainBookerIndex,
)

const bookingReference = computed(() => props.db_booking.reference || '[No reference]')

function submit() {
    booking.put(route("admin.bookings.update", props.db_booking));
}
</script>

<template>
    <Admin>
        <div class="max-w-wide mx-auto">
            <div class="grid grid-cols-1 laptop:grid-cols-3 gap-8">
                <div class="laptop:col-span-3 bg-white py-10">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-700">
                                {{ $t('admin.booking.edit.booking', { "reference": bookingReference }) }}
                            </h1>
                            <p class="mt-1 text-sm text-gray-700/50">
                                {{ db_booking.trip?.name || '[No trip]' }}
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <IconLink type="delete" icon="Trash2" v-tippy="$t('admin.booking.actions.delete')"
                                :href="route('admin.bookings.destroy', db_booking.id)" :showConfirm="true"
                                :prompt="$t('admin.booking.actions.delete_confirm')" />
                        </div>
                    </div>
                </div>

                <!-- Booking Section -->
                <div class="laptop:col-span-2 space-y-8">
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">{{ $t('admin.booking.show.details') }}</h2>
                            <p class="mt-1 text-sm text-gray-700/30">{{ $t('admin.booking.show.info') }}</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">{{ $t('admin.booking.show.reference')
                                    }}</label>
                                <p class="mt-1 text-gray-900">{{ bookingReference }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">{{ $t('admin.booking.show.trip')
                                    }}</label>
                                <p class="mt-1 text-gray-900">{{ db_booking.trip?.name || '[No trip]' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">{{
                                    $t('admin.booking.show.departure_date') }}</label>
                                <p class="mt-1 text-gray-900">{{ db_booking.departure_date_formatted || '[No date]' }}</p>
                            </div>
                        </div>
                    </section>
                    <!-- Contact Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700"> {{ $t('admin.booking.show.contact') }}</h2>
                            <p class="mt-1 text-sm text-gray-700/30">{{ $t('admin.booking.edit.edit_contact') }}</p>
                        </div>
                        <div class="p-6">
                            <Contact :booking="booking" />
                        </div>
                    </section>

                    <!-- Travelers Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700"> {{ $t('admin.booking.edit.travelers') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-700/30">{{ $t('admin.booking.edit.edit_travelers') }}</p>
                        </div>
                        <div class="p-6 space-y-6">
                            <Traveler :booking="booking" type="adults" label="Volwassene" />
                            <Traveler :booking="booking" type="children" label="Kind" />
                        </div>
                    </section>
                </div>

                <!-- Status -->
                <div class="laptop:col-start-3 space-y-8">
                    <!-- Status Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">{{ $t('admin.booking.show.booking_status') }}</h2>
                            <p class="mt-1 text-sm text-gray-700/30">{{ $t('admin.booking.edit.edit_booking_status') }}</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <Select v-model="booking.status" name="status"
                                :label="$t('admin.booking.edit.booking_status_label')" :options="statusOptions"
                                :placeholder="$t('admin.booking.edit.booking_status_placeholder')" />
                            <Select v-model="booking.payment_status" name="payment_status"
                                :label="$t('admin.booking.edit.payment_status_label')" :options="paymentStatusOptions"
                                :placeholder="$t('admin.booking.edit.payment_status_placeholder')" />
                        </div>
                    </section>
                </div>
            </div>

            <!-- Footer Actions -->
            <FormFooter :form="booking" :label="$t('admin.booking.submit.edit')" @submit="submit()" />
        </div>
    </Admin>
</template>
