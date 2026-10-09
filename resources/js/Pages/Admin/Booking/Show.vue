<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { usePage } from '@inertiajs/vue3';
import { FileText, User, Users, Activity, Receipt } from '@lucide/vue';
import { feesAndFundsEntries, sumCostItems, sumFeesAndFunds } from '@/Support/bookingPrice.js';
import { formatCents } from '@/Support/money.js';

const { t } = useI18n();
const page = usePage();

const formatPrice = (cents) => formatCents(cents, page.props.locale);

const props = defineProps({
    booking: Object,
    required: true,
});

const totalTravelers = computed(() =>
    (props.booking.adults?.length ?? 0) + (props.booking.children?.length ?? 0)
)

const costCategories = computed(() => page.props.cost_categories ?? []);

const categoryLabel = (id) =>
    costCategories.value.find((c) => c.id === id)?.name ?? id;

const costItems = computed(() => props.booking.cost_items ?? []);

// Stored cost items are already in cents.
const totalCostCents = computed(() => sumCostItems(costItems.value));

const marginPercentage = computed(
    () => (props.booking.margin_basis_points ?? 0) / 100,
);

const feesAndFundsRows = computed(() =>
    feesAndFundsEntries(props.booking.fees_and_funds),
);

const feesAndFundsTotalCents = computed(() =>
    sumFeesAndFunds(props.booking.fees_and_funds),
);

// Derived from the stored price rather than recalculated, so this page always
// shows what the server actually persisted.
const marginAmountCents = computed(
    () =>
        (props.booking.calculated_price ?? 0) -
        feesAndFundsTotalCents.value -
        totalCostCents.value,
);

const feeTotalCents = computed(
    () => (props.booking.fee_per_person ?? 0) * (props.booking.total_adults ?? 0),
);

const hasOverride = computed(() => props.booking.final_price !== null && props.booking.final_price !== undefined);
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
                                        class="text-gray-900 hover:text-brand-link underline">
                                        {{ booking.trip?.name }} - {{ booking.trip?.destinations_formatted }}
                                    </DefaultLink>
                                </dd>
                            </div>
                            <div class="px-6 py-4 grid grid-cols-3 gap-4 items-start">
                                <dt class="text-sm font-medium text-gray-500">{{ t('admin.booking.show.departure_date') }}</dt>
                                <dd class="text-sm text-gray-900 col-span-2">{{ booking.departure_date_formatted }}</dd>
                            </div>
                            <div class="px-6 py-4 grid grid-cols-3 gap-4 items-start">
                                <dt class="text-sm font-medium text-gray-500">{{ t('admin.booking.show.return_date') }}</dt>
                                <dd class="text-sm text-gray-900 col-span-2">{{ booking.return_date_formatted }}</dd>
                            </div>
                            <div class="px-6 py-4 grid grid-cols-3 gap-4 items-start">
                                <dt class="text-sm font-medium text-gray-500">{{ t('admin.booking.show.created_at') }}</dt>
                                <dd class="text-sm text-gray-900 col-span-2">{{ booking.created_at_formatted }}</dd>
                            </div>
                            <div v-if="booking.trip_request" class="px-6 py-4 grid grid-cols-3 gap-4 items-start">
                                <dt class="text-sm font-medium text-gray-500">{{ t('admin.booking.show.trip_request') }}</dt>
                                <dd class="text-sm col-span-2">
                                    <DefaultLink :href="route('admin.trip-requests.edit', booking.trip_request)"
                                        class="text-gray-900 hover:text-brand-link underline">
                                        {{ booking.trip_request.name }}
                                    </DefaultLink>
                                </dd>
                            </div>
                            <div v-if="booking.internal_notes" class="px-6 py-4 grid grid-cols-3 gap-4 items-start">
                                <dt class="text-sm font-medium text-gray-500">{{ t('admin.booking.show.internal_notes') }}</dt>
                                <dd class="text-sm text-gray-900 col-span-2 whitespace-pre-line">{{ booking.internal_notes }}</dd>
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
                            <div v-if="costItems.length === 0" class="text-sm text-gray-500 italic">
                                {{ t('admin.booking.show.pricing.no_cost_items') }}
                            </div>

                            <ul v-else class="divide-y divide-gray-100 border border-gray-100 rounded-lg overflow-hidden">
                                <li v-for="(item, index) in costItems" :key="index"
                                    class="grid grid-cols-12 gap-2 px-4 py-3 text-sm bg-gray-50">
                                    <div class="col-span-5">
                                        <div class="font-medium text-gray-900">{{ item.label }}</div>
                                        <div class="text-xs text-gray-500">{{ categoryLabel(item.category) }}</div>
                                    </div>
                                    <div class="col-span-4 text-right text-gray-700 self-center">
                                        {{ formatPrice(item.amount_per_person) }} × {{ item.quantity }}
                                    </div>
                                    <div class="col-span-3 text-right font-semibold text-gray-900 self-center">
                                        {{ formatPrice(item.amount_per_person * item.quantity) }}
                                    </div>
                                </li>
                            </ul>

                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">{{ t('booking_steps.price.total_cost') }}</span>
                                <span class="font-semibold text-gray-900">{{ formatPrice(totalCostCents) }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">
                                    <template v-if="booking.margin_in_percentage">
                                        {{ t('booking_steps.price.margin') }} ({{ marginPercentage }}%)
                                    </template>
                                    <template v-else>{{ t('booking_steps.price.margin_fixed') }}</template>
                                </span>
                                <span class="font-semibold text-gray-900">
                                    {{ formatPrice(booking.margin_in_percentage ? marginAmountCents : booking.margin_amount) }}
                                </span>
                            </div>
                            <div v-if="!booking.margin_in_percentage"
                                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">
                                    {{ t('booking_steps.price.fee_total', { count: booking.total_adults ?? 0 }) }}
                                </span>
                                <span class="font-semibold text-gray-900">{{ formatPrice(feeTotalCents) }}</span>
                            </div>
                            <div v-for="[key, cents] in feesAndFundsRows" :key="key"
                                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">{{ t(`booking_steps.overview.${key}`) }}</span>
                                <span class="font-semibold text-gray-900">{{ formatPrice(cents) }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">{{ t('booking_steps.price.calculated_price') }}</span>
                                <span class="font-semibold text-gray-900"
                                    :class="{ 'line-through opacity-50': hasOverride }">
                                    {{ formatPrice(booking.calculated_price) }}
                                </span>
                            </div>
                            <div v-if="hasOverride"
                                class="flex items-center justify-between p-3 bg-brand-accent/10 rounded-lg border border-brand-accent/30">
                                <span class="text-sm font-medium text-brand-accent">{{ t('booking_steps.price.final_price') }}</span>
                                <span class="font-semibold text-brand-accent">{{ formatPrice(booking.final_price) }}</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-brand-primary/5 rounded-lg border border-brand-primary/20">
                                <span class="text-sm font-semibold text-gray-700">{{ t('booking_steps.price.display_price') }}</span>
                                <span class="text-lg font-bold text-brand-primary">{{ formatPrice(booking.display_price) }}</span>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </Admin>
</template>
