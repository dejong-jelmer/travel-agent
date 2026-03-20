<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { FileText, User, Users, Activity, Receipt } from 'lucide-vue-next';

const { t } = useI18n();

const props = defineProps({
    booking: Object,
    required: true,
});

const totalTravelers = computed(() =>
    (props.booking.adults?.length ?? 0) + (props.booking.children?.length ?? 0)
)

function formatPrice(cents) {
    return new Intl.NumberFormat('nl-NL', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(cents / 100)
}
</script>

<template>
    <Admin>
        <div class="max-w-wide mx-auto">
            <div class="grid grid-cols-1 laptop:grid-cols-3 gap-8">
                <!-- Header Section -->
                <div class="laptop:col-span-3 bg-white py-10 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-700">
                                {{ t('admin.booking.show.booking') }} {{ booking.reference }}
                            </h1>
                            <div class="mt-2 flex items-center gap-3">
                                <p class="text-sm text-gray-700/50">{{ booking.trip?.name }}</p>
                                <BookingStatusBadge :status="booking.status">{{ booking.status_label }}</BookingStatusBadge>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <IconLink type="info" icon="Pencil" :href="route('admin.bookings.edit', booking)"
                                v-tippy="t('admin.booking.actions.edit')" />
                            <IconLink type="delete" icon="Trash2" :href="route('admin.bookings.destroy', booking)"
                                method="delete" :showConfirm="true" :prompt="t('admin.booking.actions.delete_confirm')"
                                v-tippy="t('admin.booking.actions.delete')" />
                        </div>
                    </div>
                </div>

                <div class="laptop:col-span-2 space-y-8">
                    <!-- Booking Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <div class="flex items-center gap-2">
                                <FileText class="h-4 w-4 text-gray-400 flex-shrink-0" />
                                <h2 class="text-lg font-semibold text-gray-700">{{ t('admin.booking.show.details') }}</h2>
                            </div>
                            <p class="mt-1 text-sm text-gray-700/30 pl-6">{{ t('admin.booking.show.info') }}</p>
                        </div>
                        <dl class="divide-y divide-gray-100">
                            <div class="px-6 py-4 grid grid-cols-3 gap-4 items-start">
                                <dt class="text-sm font-medium text-gray-500">{{ t('admin.booking.show.reference') }}</dt>
                                <dd class="text-sm text-gray-900 col-span-2">{{ booking.reference }}</dd>
                            </div>
                            <div class="px-6 py-4 grid grid-cols-3 gap-4 items-start">
                                <dt class="text-sm font-medium text-gray-500">{{ t('admin.booking.show.trip') }}</dt>
                                <dd class="text-sm col-span-2">
                                    <DefaultLink :href="route('admin.trips.show', booking.trip)"
                                        class="text-gray-900 hover:text-brand-link">
                                        {{ booking.trip?.name }} - {{ booking.trip?.destinations_formatted }}
                                    </DefaultLink>
                                </dd>
                            </div>
                            <div class="px-6 py-4 grid grid-cols-3 gap-4 items-start">
                                <dt class="text-sm font-medium text-gray-500">{{ t('admin.booking.show.departure_date') }}</dt>
                                <dd class="text-sm text-gray-900 col-span-2">{{ booking.departure_date_formatted }}</dd>
                            </div>
                            <div class="px-6 py-4 grid grid-cols-3 gap-4 items-start">
                                <dt class="text-sm font-medium text-gray-500">{{ t('admin.booking.show.created_at') }}</dt>
                                <dd class="text-sm text-gray-900 col-span-2">{{ booking.created_at_formatted }}</dd>
                            </div>
                        </dl>
                    </section>

                    <!-- Contact Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <div class="flex items-center gap-2">
                                <User class="h-4 w-4 text-gray-400 flex-shrink-0" />
                                <h2 class="text-lg font-semibold text-gray-700">{{ t('admin.booking.show.contact') }}</h2>
                            </div>
                            <p class="mt-1 text-sm text-gray-700/30 pl-6">{{ t('admin.booking.show.contact_info') }}</p>
                        </div>
                        <dl class="divide-y divide-gray-100">
                            <div class="px-6 py-4 grid grid-cols-3 gap-4 items-start">
                                <dt class="text-sm font-medium text-gray-500">{{ t('admin.booking.show.contact_name') }}</dt>
                                <dd class="text-sm text-gray-900 col-span-2">{{ booking.contact?.name }}</dd>
                            </div>
                            <div class="px-6 py-4 grid grid-cols-3 gap-4 items-start">
                                <dt class="text-sm font-medium text-gray-500">{{ t('admin.booking.show.contact_email') }}</dt>
                                <dd class="text-sm text-gray-900 col-span-2">{{ booking.contact?.email }}</dd>
                            </div>
                            <div class="px-6 py-4 grid grid-cols-3 gap-4 items-start">
                                <dt class="text-sm font-medium text-gray-500">{{ t('admin.booking.show.contact_telephone') }}</dt>
                                <dd class="text-sm text-gray-900 col-span-2">{{ booking.contact?.phone }}</dd>
                            </div>
                            <div class="px-6 py-4 grid grid-cols-3 gap-4 items-start">
                                <dt class="text-sm font-medium text-gray-500">{{ t('admin.booking.show.contact_address') }}</dt>
                                <dd class="text-sm text-gray-900 col-span-2 whitespace-pre-line">{{ booking.contact?.address }}</dd>
                            </div>
                        </dl>
                    </section>

                    <!-- Travelers Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-2">
                                    <Users class="h-4 w-4 text-gray-400 flex-shrink-0" />
                                    <h2 class="text-lg font-semibold text-gray-700">{{ t('admin.booking.show.travelers') }}</h2>
                                </div>
                                <span class="text-xs font-medium bg-gray-100 text-gray-600 rounded-full px-2 py-0.5">
                                    {{ totalTravelers }}
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-gray-700/30 pl-6">{{ t('admin.booking.show.travelers_subheader') }}</p>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 laptop:grid-cols-2 gap-6">
                                <div v-if="booking.adults?.length">
                                    <h3 class="text-sm font-medium text-gray-700 mb-3">{{ t('booker.adults', booking.adults.length) }}</h3>
                                    <div class="space-y-2">
                                        <Booker v-for="(adult, index) in booking.adults" :key="index" :booker="adult"
                                            :index="index" />
                                    </div>
                                </div>
                                <div v-if="booking.children?.length">
                                    <h3 class="text-sm font-medium text-gray-700 mb-3">{{ t('booker.children', booking.children.length) }}</h3>
                                    <div class="space-y-2">
                                        <Booker v-for="(child, index) in booking.children" :key="index"
                                            :booker="child" />
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
                            <div class="flex items-center gap-2">
                                <Activity class="h-4 w-4 text-gray-400 flex-shrink-0" />
                                <h2 class="text-lg font-semibold text-gray-700">{{ t('admin.booking.show.booking_status') }}</h2>
                            </div>
                            <p class="mt-1 text-sm text-gray-700/30 pl-6">{{ t('admin.booking.show.status_info') }}</p>
                        </div>
                        <dl class="divide-y divide-gray-100">
                            <div class="flex items-center justify-between px-6 py-4">
                                <dt class="text-sm font-medium text-gray-500">{{ t('admin.booking.show.booking_status') }}</dt>
                                <dd>
                                    <BookingStatusBadge :status="booking.status">{{ booking.status_label }}</BookingStatusBadge>
                                </dd>
                            </div>
                            <div class="flex items-center justify-between px-6 py-4">
                                <dt class="text-sm font-medium text-gray-500">{{ t('admin.booking.show.payment_status') }}</dt>
                                <dd>
                                    <PaymentStatusBadge :status="booking.payment_status">{{ booking.payment_status_label }}</PaymentStatusBadge>
                                </dd>
                            </div>
                        </dl>
                    </section>

                    <!-- Pricing Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <div class="flex items-center gap-2">
                                <Receipt class="h-4 w-4 text-gray-400 flex-shrink-0" />
                                <h2 class="text-lg font-semibold text-gray-700">{{ t('admin.booking.show.pricing.title') }}</h2>
                            </div>
                            <p class="mt-1 text-sm text-gray-700/30 pl-6">{{ t('admin.booking.show.pricing.subtitle') }}</p>
                        </div>
                        <div class="p-6 space-y-3">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">{{ t('admin.booking.show.pricing.price_per_person') }}</span>
                                <span class="font-semibold text-gray-900">{{ formatPrice(booking.price_per_person) }}</span>
                            </div>
                            <div v-if="totalTravelers === 1" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">{{ t('admin.booking.show.pricing.single_supplement') }}</span>
                                <span class="font-semibold text-gray-900">{{ formatPrice(booking.single_supplement) }}</span>
                            </div>
                            <div v-for="(amount, key) in booking.fees_and_funds" :key="key"
                                class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">{{ t(`booking_steps.overview.${key}`) }}</span>
                                <span class="font-semibold text-gray-900">{{ formatPrice(amount) }}</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-primary-default/5 rounded-lg border border-primary-default/20">
                                <span class="text-sm font-semibold text-gray-700">{{ t('admin.booking.show.pricing.total') }}</span>
                                <span class="text-lg font-bold text-primary-default">{{ formatPrice(booking.total_price) }}</span>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </Admin>
</template>
