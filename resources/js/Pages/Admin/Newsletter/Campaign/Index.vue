<script setup>
import { useI18n } from 'vue-i18n';
import { computed } from 'vue';

const props = defineProps({
    campaigns: Object,
    filters: Object,
    statusOptions: Array,
    totalCampaigns: Number,
});

const { t } = useI18n();

const columns = [
    { key: 'id', label: '#', sortable: true },
    { key: 'subject', label: t('admin.newsletter.campaigns.index.table_headers.subject'), sortable: true },
    { key: 'status', label: t('admin.newsletter.campaigns.index.table_headers.status'), sortable: true },
    { key: 'scheduled_at', label: t('admin.newsletter.campaigns.index.table_headers.scheduled_at'), sortable: true },
    { key: 'sent_at', label: t('admin.newsletter.campaigns.index.table_headers.sent_at'), sortable: true },
    { key: 'sent_count', label: t('admin.newsletter.campaigns.index.table_headers.sent_count'), sortable: true },
    { key: 'actions', label: t('admin.newsletter.campaigns.index.table_headers.actions'), sortable: false },
]

const filterOptions = computed(() => [
    {
        key: 'status',
        label: t('admin.newsletter.campaigns.index.filters.status', 'Status'),
        options: props.statusOptions || [],
    }
]);

</script>
<template>
    <Admin>
        <div class="w-full flex flex-col tablet:flex-row justify-between mb-6">
            <h1 class="text-3xl font-bold mb-4 tablet:mb-0">{{ t('admin.newsletter.campaigns.index.title') }}</h1>
            <IconLink v-tippy="t('admin.newsletter.campaigns.actions.create')" icon="Plus" type="info"
                :href="route('admin.newsletter.campaigns.create')" />
        </div>
        <template v-if="totalCampaigns > 0">
            <DataTable :data="campaigns.data" :columns="columns" :links="campaigns.links" :current-sort="filters.sort"
                :current-direction="filters.direction" :current-search="filters.search" :filter-options="filterOptions"
                searchable
                :search-placeholder="t('admin.newsletter.campaigns.index.search_placeholder', 'Search by reference or trip...')"
                :empty-message="t('admin.newsletter.campaigns.index.no_campaigns_found', { search: filters.search })">
                <!-- Custom cell for status -->
                <template #cell-status="{ row }">
                    <CampaignStatusBadge class="w-full mx-auto" :status="row.status">
                        {{ row.status_label }}
                    </CampaignStatusBadge>
                </template>

                <!-- Custom cell for scheduled_at -->
                <template #cell-scheduled_at="{ row }">
                    {{ row.scheduled_at_formatted }}
                </template>

                <!-- Custom cell for sent_at -->
                <template #cell-sent_at="{ row }">
                    {{ row.sent_at_formatted }}
                </template>

                <!-- Custom cell for actions -->
                <template #cell-actions="{ row }">
                    <DropdownMenu>
                        <template #default="{ MenuItem }">
                            <component :is="MenuItem">
                                <IconLink icon="Pencil" :href="route('admin.newsletter.campaigns.edit', row)"
                                    v-tippy="$t('admin.newsletter.campaigns.actions.edit')" />
                            </component>
                            <component :is="MenuItem">
                                <IconLink icon="Send" method="post"
                                    :href="route('admin.newsletter.campaigns.send', row)" :showConfirm="true"
                                    :prompt="$t('admin.newsletter.campaigns.actions.send_confirm')"
                                    v-tippy="$t('admin.newsletter.campaigns.actions.send')" />
                            </component>
                            <component :is="MenuItem">
                                <IconLink type="delete" icon="Trash2"
                                    :href="route('admin.newsletter.campaigns.destroy', row)" method="delete"
                                    :showConfirm="true"
                                    :prompt="$t('admin.newsletter.campaigns.actions.delete_confirm')"
                                    v-tippy="$t('admin.newsletter.campaigns.actions.delete')" />
                            </component>
                        </template>
                    </DropdownMenu>
                </template>
            </DataTable>
        </template>
        <template v-else>
            <div class="p-5">
                <p>{{ t('admin.newsletter.campaigns.index.no_campaigns') }}</p>
            </div>
        </template>
    </Admin>
</template>
