<script setup>
import { useI18n } from 'vue-i18n';

const props = defineProps({
    countries: Object,
    filters: Object,
    totalCountries: Number,
});

const { t } = useI18n();

const columns = [
    { key: 'id', label: '#', sortable: true },
    { key: 'name', label: t('admin.countries.index.table_headers.countries'), sortable: true },
    { key: 'actions', label: t('admin.booking.index.table_headers.actions'), sortable: false },
];

</script>
<template>
    <Admin>
        <div class="w-full flex flex-col tablet:flex-row justify-between mb-6">
            <h1 class="text-3xl font-bold mb-4 tablet:mb-0">{{ t('admin.countries.index.title') }}</h1>
            <IconLink v-tippy="t('admin.countries.actions.create')" icon="Plus" type="info"
                :href="route('admin.countries.create')" />
        </div>
        <template v-if="totalCountries > 0">
            <DataTable :data="countries.data" :columns="columns" :links="countries.links" :current-sort="filters.sort"
                :current-direction="filters.direction" :current-search="filters.search" searchable
                :search-placeholder="t('admin.countries.index.search_placeholder')"
                :empty-message="filters.search
                    ? t('admin.countries.index.no_countries_found_with_search', { search: filters.search })
                    : t('admin.countries.index.no_countries_found')">


                <!-- Custom cell for actions -->
                <template #cell-actions="{ row }">
                    <DropdownMenu>
                        <template #default="{ MenuItem }">
                            <component :is="MenuItem">
                                <IconLink class="mx-auto" type="delete" icon="Trash2"
                                    :href="route('admin.countries.destroy', row)" method="delete" :showConfirm="true"
                                    :prompt="$t('admin.countries.actions.delete_confirm')"
                                    v-tippy="$t('admin.countries.actions.delete')" />
                            </component>
                        </template>
                    </DropdownMenu>
                </template>
            </DataTable>
        </template>
        <template v-else>
            <div class="p-5">
                <p>{{ t('admin.countries.index.no_countries') }}</p>
            </div>
        </template>
    </Admin>
</template>
