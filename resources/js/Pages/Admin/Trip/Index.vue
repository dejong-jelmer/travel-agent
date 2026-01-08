<script setup>
import { useI18n } from 'vue-i18n';

const props = defineProps({
    trips: Object,
    filters: Object,
    totalTrips: Number
});

const { t } = useI18n();

const columns = [
    { key: 'id', label: t('admin.trips.index.table_headers.id'), sortable: true },
    { key: 'image', label: t('admin.trips.index.table_headers.image'), sortable: false },
    { key: 'name', label: t('admin.trips.index.table_headers.product'), sortable: true },
    { key: 'countries', label: t('admin.trips.index.table_headers.countries'), sortable: true },
    { key: 'price', label: t('admin.trips.index.table_headers.price'), sortable: true },
    { key: 'duration', label: t('admin.trips.index.table_headers.days'), sortable: true },
    { key: 'actions', label: t('admin.trips.index.table_headers.actions'), sortable: false },
];

</script>

<template>
    <Admin>
        <div class="w-full flex flex-col tablet:flex-row justify-between mb-6">
            <h1 class="text-3xl font-bold mb-4 tablet:mb-0">{{ t('admin.trips.index.title') }}</h1>
            <IconLink v-tippy="t('admin.trips.actions.create')" icon="Plus" type="info"
                :href="route('admin.trips.create')" />
        </div>
        <template v-if="totalTrips > 0">
            <DataTable :data="trips.data" :columns="columns" :links="trips.links" :current-sort="filters.sort"
                :current-direction="filters.direction" :current-search="filters.search" :searchable="totalTrips > 0"
                :search-placeholder="t('admin.trips.index.search_placeholder')"
                :empty-message="filters.search
                    ? t('admin.trips.index.no_trips_found_with_search', { search: filters.search })
                    : t('admin.trips.index.no_trips_found')">

                <!-- Custom cell for image -->
                <template #cell-image="{ row }">
                    <div class="flex justify-center">
                        <Thumbnail :imageUrl="row.hero_image?.public_url || ''" :alt="row.name" />
                    </div>
                </template>

                <!-- Custom cell for countries -->
                <template #cell-countries="{ row }">
                    {{ row.countries_formatted }}
                </template>

                <!-- Custom cell for price -->
                <template #cell-price="{ row }">
                    € {{ row.price }}
                </template>

                <!-- Custom cell for actions -->
                <template #cell-actions="{ row }">
                    <DropdownMenu>
                        <template #default="{ MenuItem }">
                            <component :is="MenuItem">
                                <IconLink icon="Eye" :href="route('admin.trips.show', row)"
                                    v-tippy="t('admin.trips.actions.view')" />
                            </component>
                            <component :is="MenuItem">
                                <IconLink icon="Pencil" :href="route('admin.trips.edit', row)"
                                    v-tippy="t('admin.trips.actions.edit')" />
                            </component>
                            <component :is="MenuItem">
                                <IconLink icon="Route" :href="row.itineraries?.length
                                    ? route('admin.trips.itineraries.index', row)
                                    : route('admin.trips.itineraries.create', row)"
                                    v-tippy="t('admin.trips.actions.itinerary')" />
                            </component>
                            <component :is="MenuItem">
                                <IconLink type="delete" icon="Trash2" :href="route('admin.trips.destroy', row)"
                                    method="delete" :showConfirm="true"
                                    :prompt="t('admin.trips.actions.delete_confirm')"
                                    v-tippy="t('admin.trips.actions.delete')" />
                            </component>
                        </template>
                    </DropdownMenu>
                </template>
            </DataTable>
        </template>
        <template v-else>
            <div class="p-5">
                <p>{{ t('admin.trips.index.no_trips') }}</p>
            </div>
        </template>
    </Admin>
</template>
