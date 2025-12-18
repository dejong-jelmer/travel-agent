<script setup>
import { useI18n } from 'vue-i18n';
import { computed } from 'vue';

const props = defineProps({
    bookings: Object,
    filters: Object,
    statusOptions: Array,
    paymentStatusOptions: Array,
    totalBookings: Number,
});

const { t } = useI18n();

const columns = [
    { key: 'id', label: '#', sortable: true },
    { key: 'reference', label: t('admin.booking.index.table_headers.reference'), sortable: true },
    { key: 'trip', label: t('admin.booking.index.table_headers.trip'), sortable: true },
    { key: 'departure_date', label: t('admin.booking.index.table_headers.departure_date'), sortable: true },
    { key: 'status', label: t('admin.booking.index.table_headers.status'), sortable: true },
    { key: 'payment_status', label: t('admin.booking.index.table_headers.payment'), sortable: true },
    { key: 'actions', label: t('admin.booking.index.table_headers.actions'), sortable: false },
];

const filterOptions = computed(() => [
    {
        key: 'status',
        label: t('admin.booking.index.filters.status', 'Status'),
        options: props.statusOptions || [],
    },
    {
        key: 'payment_status',
        label: t('admin.booking.index.filters.payment_status', 'Payment'),
        options: props.paymentStatusOptions || [],
    },
]);

const currentFilters = computed(() => ({
    status: props.filters.status,
    payment_status: props.filters.payment_status,
}));
</script>

<template>
    <Admin>
        <template v-if="totalBookings > 0">
            <DataTable :data="bookings.data" :columns="columns" :links="bookings.links" :current-sort="filters.sort"
                :current-direction="filters.direction" :current-search="filters.search" :filter-options="filterOptions"
                :current-filters="currentFilters" searchable
                :search-placeholder="t('admin.booking.index.search_placeholder', 'Search by reference or trip...')"
                :empty-message="t('admin.booking.index.no_bookings_found', { search: filters.search })">
                <!-- Custom cell for trip -->
                <template #cell-trip="{ row }">
                    {{ row.trip?.name ?? '-' }}
                </template>

                <!-- Custom cell for departure_date -->
                <template #cell-departure_date="{ row }">
                    {{ row.departure_date_formatted }}
                </template>

                <!-- Custom cell for status -->
                <template #cell-status="{ row }">
                    <BookingStatusBadge class="w-full" :status="row.status">
                        {{ row.status_label }}
                    </BookingStatusBadge>
                </template>

                <!-- Custom cell for payment_status -->
                <template #cell-payment_status="{ row }">
                    <PaymentStatusBadge class="w-full" :status="row.payment_status">
                        {{ row.payment_status_label }}
                    </PaymentStatusBadge>
                </template>

                <!-- Custom cell for actions -->
                <template #cell-actions="{ row }">
                    <DropdownMenu>
                        <template #default="{ MenuItem }">
                            <component :is="MenuItem">
                                <IconLink icon="Eye" :href="route('admin.bookings.show', row)"
                                    v-tippy="t('admin.booking.actions.show')" />
                            </component>
                            <component :is="MenuItem">
                                <IconLink icon="Pencil" :href="route('admin.bookings.edit', row)"
                                    v-tippy="t('admin.booking.actions.edit')" />
                            </component>
                            <component :is="MenuItem">
                                <IconLink type="delete" icon="Trash2" :href="route('admin.bookings.destroy', row)"
                                    method="delete" :showConfirm="true"
                                    :prompt="t('admin.booking.actions.delete_confirm')"
                                    v-tippy="t('admin.booking.actions.delete')" />
                            </component>
                        </template>
                    </DropdownMenu>
                </template>
            </DataTable>
        </template>

        <template v-else>
            <div class="p-5">
                <p>{{ t('admin.booking.index.no_bookings') }}</p>
            </div>
        </template>

    </Admin>
</template>
