<script setup>
import { useI18n } from 'vue-i18n';
import { computed } from 'vue';

const props = defineProps({
    tripRequests: Object,
    filters: Object,
    statusOptions: Array,
    totalTripRequests: Number,
});

const { t } = useI18n();

const columns = [
    { key: 'id', label: '#', sortable: true },
    { key: 'name', label: t('admin.trip_request.index.table_headers.name'), sortable: true },
    { key: 'email', label: t('admin.trip_request.index.table_headers.email'), sortable: true },
    { key: 'trip', label: t('admin.trip_request.index.table_headers.trip'), sortable: true },
    { key: 'status', label: t('admin.trip_request.index.table_headers.status'), sortable: true },
    { key: 'created_at', label: t('admin.trip_request.index.table_headers.created_at'), sortable: true },
    { key: 'actions', label: t('admin.trip_request.index.table_headers.actions'), sortable: false },
];

const filterOptions = computed(() => [
    {
        key: 'status',
        label: t('admin.trip_request.index.filters.status'),
        options: props.statusOptions || [],
    },
]);

const currentFilters = computed(() => ({
    status: props.filters.status,
}));
</script>

<template>
    <Admin>
        <template v-if="totalTripRequests > 0">
            <DataTable :data="tripRequests.data" :columns="columns" :links="tripRequests.links"
                :current-sort="filters.sort" :current-direction="filters.direction" :current-search="filters.search"
                :filter-options="filterOptions" :current-filters="currentFilters" searchable
                :search-placeholder="t('admin.trip_request.index.search_placeholder')"
                :empty-message="filters.search
                    ? t('admin.trip_request.index.no_requests_found_with_search', { search: filters.search })
                    : t('admin.trip_request.index.no_requests_found')">

                <!-- Custom cell for trip -->
                <template #cell-trip="{ row }">
                    {{ row.trip?.name ?? '-' }}
                </template>

                <!-- Custom cell for status -->
                <template #cell-status="{ row }">
                    <TripRequestStatusBadge class="w-full" :status="row.status">
                        {{ row.status_label }}
                    </TripRequestStatusBadge>
                </template>

                <!-- Custom cell for created_at -->
                <template #cell-created_at="{ row }">
                    {{ row.created_at_formatted }}
                </template>

                <!-- Custom cell for actions -->
                <template #cell-actions="{ row }">
                    <DropdownMenu>
                        <template #default="{ MenuItem }">
                            <component :is="MenuItem">
                                <IconLink icon="Eye" :href="route('admin.trip-requests.edit', row)"
                                    v-tippy="t('admin.trip_request.actions.edit')" />
                            </component>
                            <component :is="MenuItem">
                                <IconLink type="delete" icon="Trash2" :href="route('admin.trip-requests.destroy', row)"
                                    method="delete" :showConfirm="true"
                                    :prompt="t('admin.trip_request.actions.delete_confirm')"
                                    v-tippy="t('admin.trip_request.actions.delete')" />
                            </component>
                        </template>
                    </DropdownMenu>
                </template>
            </DataTable>
        </template>

        <template v-else>
            <div class="p-5">
                <p>{{ t('admin.trip_request.index.no_requests') }}</p>
            </div>
        </template>
    </Admin>
</template>
