<script setup>
import { useI18n } from 'vue-i18n';
import { computed } from 'vue';

const props = defineProps({
    subscribers: Object,
    filters: Object,
    statusOptions: Array,
    totalSubscribers: Number
});

const { t } = useI18n();

const columns = [
    { key: 'id', label: '#', sortable: true },
    { key: 'name', label: t('admin.newsletter.subscribers.index.table_headers.name'), sortable: true },
    { key: 'email', label: t('admin.newsletter.subscribers.index.table_headers.email'), sortable: true },
    { key: 'status', label: t('admin.newsletter.subscribers.index.table_headers.status'), sortable: false },
    { key: 'actions', label: t('admin.newsletter.subscribers.index.table_headers.actions'), sortable: false },
];

const filterOptions = computed(() => [
    {
        key: 'status',
        label: t('admin.newsletter.subscribers.index.filters.status', 'Status'),
        options: props.statusOptions || [],
    },
]);

const currentFilters = computed(() => ({
    status: props.filters.status,
}));

</script>

<template>
    <Admin>
        <div class="w-full flex flex-col tablet:flex-row justify-between mb-6">
            <h1 class="text-3xl font-bold mb-4 tablet:mb-0">
                {{ t('admin.newsletter.subscribers.index.title') }}
            </h1>
        </div>

        <template v-if="totalSubscribers > 0">
            <DataTable
            :data="subscribers.data"
            :columns="columns"
            :links="subscribers.links"
            :current-sort="filters.sort"
            :current-direction="filters.direction"
            :current-search="filters.search"
            :filter-options="filterOptions"
            :current-filters="currentFilters"
            searchable
            :search-placeholder="t('admin.newsletter.subscribers.index.search_placeholder')"
            :empty-message="filters.search
                    ? t('admin.newsletter.subscribers.index.no_subscribers_found_with_search', { search: filters.search })
                    : t('admin.newsletter.subscribers.index.no_subscribers_found')">
            <!-- Custom cell for name -->
            <template #cell-name="{ row }">
                {{ row.name || '-' }}
            </template>

            <!-- Custom cell for status -->
            <template #cell-status="{ row }">
                <SubscriberStatusBadge class="w-full mx-auto" :status="row.status">
                    {{ row.status_label || '-' }}
                </SubscriberStatusBadge>
            </template>

            <!-- Custom cell for actions -->
            <template #cell-actions="{ row }">
                <DropdownMenu>
                    <template #default="{ MenuItem }">
                        <component v-if="row.status === 'expired'" :is="MenuItem">
                            <IconLink
                                icon="Refresh"
                                :href="route('admin.newsletter.subscribers.resend', row)"
                                :showConfirm="true"
                                :prompt="t('admin.newsletter.subscribers.actions.resend_confirm')"
                                v-tippy="t('admin.newsletter.subscribers.actions.resend')"
                            />
                        </component>
                        <component :is="MenuItem">
                            <IconLink
                                type="delete"
                                icon="Trash2"
                                :href="route('admin.newsletter.subscribers.destroy', row)"
                                method="delete"
                                :showConfirm="true"
                                :prompt="t('admin.newsletter.subscribers.actions.delete_confirm')"
                                v-tippy="t('admin.newsletter.subscribers.actions.delete')"
                            />
                        </component>
                    </template>
                </DropdownMenu>
            </template>
        </DataTable>
        </template>
        <template v-else>
            <div class="p-5">
                <p>{{ t('admin.newsletter.subscribers.index.no_subscribers') }}</p>
            </div>
        </template>
    </Admin>
</template>
