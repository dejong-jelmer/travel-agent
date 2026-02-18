<script setup>
import { useI18n } from 'vue-i18n';

const props = defineProps({
    destinations: Object,
    filters: Object,
    totalDestinations: Number,
});

const { t } = useI18n();

const columns = [
    { key: 'id', label: '#', sortable: true },
    { key: 'country_code', label: t('admin.destinations.index.table_headers.country_code'), sortable: true },
    { key: 'name', label: t('admin.destinations.index.table_headers.destinations'), sortable: true },
    { key: 'region', label: t('admin.destinations.index.table_headers.region'), sortable: true },
    { key: 'actions', label: t('admin.booking.index.table_headers.actions'), sortable: false },
];

</script>
<template>
    <Admin>
        <div class="w-full flex flex-col tablet:flex-row justify-between mb-6">
            <h1 class="text-3xl font-bold mb-4 tablet:mb-0">{{ t('admin.destinations.index.title') }}</h1>
            <IconLink v-tippy="t('admin.destinations.actions.create')" icon="Plus" type="info"
                :href="route('admin.destinations.create')" />
        </div>
        <template v-if="totalDestinations > 0">
            <DataTable :data="destinations.data" :columns="columns" :links="destinations.links" :current-sort="filters.sort"
                :current-direction="filters.direction" :current-search="filters.search" searchable
                :search-placeholder="t('admin.destinations.index.search_placeholder')"
                :empty-message="filters.search
                    ? t('admin.destinations.index.no_destinations_found_with_search', { search: filters.search })
                    : t('admin.destinations.index.no_destinations_found')">


                <!-- Custom cell for actions -->
                <template #cell-actions="{ row }">
                    <DropdownMenu>
                        <template #default="{ MenuItem }">
                            <component :is="MenuItem">
                                <IconLink icon="Pencil" :href="route('admin.destinations.edit', row)"
                                    v-tippy="t('admin.destination.actions.edit')" />
                            </component>
                            <component :is="MenuItem">
                                <IconLink class="mx-auto" type="delete" icon="Trash2"
                                    :href="route('admin.destinations.destroy', row)" method="delete" :showConfirm="true"
                                    :prompt="$t('admin.destinations.actions.delete_confirm')"
                                    v-tippy="$t('admin.destinations.actions.delete')" />
                            </component>
                        </template>
                    </DropdownMenu>
                </template>
            </DataTable>
        </template>
        <template v-else>
            <div class="p-5">
                <p>{{ t('admin.destinations.index.no_destinations') }}</p>
            </div>
        </template>
    </Admin>
</template>
