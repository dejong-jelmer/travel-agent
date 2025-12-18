<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { ArrowUpDown, ArrowUp, ArrowDown, Search, Filter } from 'lucide-vue-next';
import { debounce } from 'lodash';

const props = defineProps({
    data: {
        type: Array,
        required: true,
    },
    columns: {
        type: Array,
        required: true,
        // Example: [
        //   { key: 'id', label: '#', sortable: true },
        //   { key: 'reference', label: 'Reference', sortable: true },
        //   { key: 'status', label: 'Status', sortable: false },
        // ]
    },
    links: {
        type: Array,
        default: () => [],
    },
    searchable: {
        type: Boolean,
        default: false,
    },
    searchPlaceholder: {
        type: String,
        default: 'Search...',
    },
    emptyMessage: {
        type: String,
        default: 'No records found',
    },
    currentSort: {
        type: String,
        default: null,
    },
    currentDirection: {
        type: String,
        default: 'asc',
    },
    currentSearch: {
        type: String,
        default: '',
    },
    filterOptions: {
        type: Array,
        default: () => [],
        // Example: [
        //   { key: 'status', label: 'Status', options: [{ value: 'new', label: 'New' }] },
        //   { key: 'payment_status', label: 'Payment', options: [...] },
        // ]
    },
    currentFilters: {
        type: Object,
        default: () => ({}),
    },
});

const search = ref(props.currentSearch);
const filters = ref({ ...props.currentFilters });

const buildParams = () => {
    const params = {
        search: search.value,
        ...filters.value,
    };

    // Add sort params if set
    if (props.currentSort) {
        params.sort = props.currentSort;
        params.direction = props.currentDirection;
    }

    return params;
};

const sortColumn = (columnKey) => {
    if (!columnKey) return;

    let newSort = columnKey;
    let newDirection = 'desc';

    // 3-state cycle: not sorted → desc → asc → reset
    if (props.currentSort === columnKey) {
        if (props.currentDirection === 'desc') {
            newDirection = 'asc';
        } else if (props.currentDirection === 'asc') {
            // Reset: remove sort
            newSort = null;
            newDirection = null;
        }
    }

    const params = {
        search: search.value,
        ...filters.value,
    };

    // Only add sort params if not resetting
    if (newSort) {
        params.sort = newSort;
        params.direction = newDirection;
    }

    router.get(
        route(route().current()),
        params,
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const applyFilters = () => {
    router.get(
        route(route().current()),
        buildParams(),
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const debouncedSearch = debounce(() => {
    router.get(
        route(route().current()),
        buildParams(),
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
}, 300);

const onSearch = () => {
    debouncedSearch();
};

const getSortIcon = (columnKey) => {
    if (props.currentSort !== columnKey) return ArrowUpDown;
    return props.currentDirection === 'asc' ? ArrowUp : ArrowDown;
};

const isSorted = (columnKey) => {
    return props.currentSort === columnKey;
};
</script>

<template>
    <div class="space-y-4">
        <!-- Search & Filters Bar -->
        <div v-if="searchable || filterOptions.length > 0" class="bg-white p-4 rounded-lg shadow space-y-3">
            <!-- Search -->
            <div v-if="searchable" class="flex items-center gap-2">
                <Search class="w-5 h-5 text-gray-400" />
                <input
                    v-model="search"
                    @input="onSearch"
                    type="text"
                    :placeholder="searchPlaceholder"
                    class="flex-1 outline-none text-sm"
                />
            </div>

            <!-- Filters -->
            <div v-if="filterOptions.length > 0" class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <Filter class="w-4 h-4" />
                    <span class="font-medium">Filters:</span>
                </div>

                <div
                    v-for="filter in filterOptions"
                    :key="filter.key"
                    class="flex items-center gap-2"
                >
                    <label :for="`filter-${filter.key}`" class="text-sm text-gray-600">
                        {{ filter.label }}
                    </label>
                    <select
                        :id="`filter-${filter.key}`"
                        v-model="filters[filter.key]"
                        @change="applyFilters"
                        class="text-sm border border-gray-300 rounded-md px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-default focus:border-transparent"
                    >
                        <option :value="null">All</option>
                        <option
                            v-for="option in filter.options"
                            :key="option.id"
                            :value="option.id"
                        >
                            {{ option.name }}
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white shadow-lg rounded-2xl">
            <template v-if="data.length > 0">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                            <th
                                v-for="column in columns"
                                :key="column.key"
                                class="py-4 px-6 text-center"
                                :class="{ 'cursor-pointer hover:bg-gray-300 transition': column.sortable }"
                                @click="column.sortable ? sortColumn(column.key) : null"
                            >
                                <div class="flex items-center justify-center gap-1 whitespace-nowrap">
                                    <span>{{ column.label }}</span>
                                    <component
                                        v-if="column.sortable"
                                        :is="getSortIcon(column.key)"
                                        class="w-4 h-4"
                                        :class="{ 'text-primary-default': isSorted(column.key) }"
                                    />
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm divide-y divide-gray-200">
                        <tr
                            v-for="(row, rowIndex) in data"
                            :key="rowIndex"
                            class="transition hover:bg-gray-100"
                        >
                            <td
                                v-for="column in columns"
                                :key="column.key"
                                class="py-4 px-6 text-center"
                            >
                                <!-- Custom slot for each cell -->
                                <slot
                                    :name="`cell-${column.key}`"
                                    :row="row"
                                    :value="row[column.key]"
                                    :column="column"
                                >
                                    <!-- Default cell content -->
                                    {{ row[column.key] ?? '-' }}
                                </slot>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </template>
            <template v-else>
                <div class="p-8 text-center text-gray-500">
                    <slot name="empty">
                        <p>{{ emptyMessage }}</p>
                    </slot>
                </div>
            </template>
        </div>

        <!-- Pagination -->
        <div v-if="links.length > 0" class="flex justify-center gap-2">
                <div class="my-5 w-full flex justify-center">
                    <Pagination v-if="(typeof links !== 'undefined')" :links="links"></Pagination>
                </div>
        </div>
    </div>
</template>
