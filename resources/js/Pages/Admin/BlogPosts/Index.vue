<script setup>
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useDateFormatter } from '@/Composables/useDateFormatter';
import { computed } from 'vue';

const props = defineProps({
    posts: Object,
    filters: Object,
    totalPosts: Number,
});

const { t } = useI18n();
const locale = computed(() => usePage().props.locale);
const { formattedDate } = useDateFormatter();

const columns = [
    { key: 'title', label: t('admin.blog_posts.index.table_headers.title'), sortable: true },
    { key: 'status', label: t('admin.blog_posts.index.table_headers.status'), sortable: false },
    { key: 'published_at', label: t('admin.blog_posts.index.table_headers.published_at'), sortable: true },
    { key: 'actions', label: t('admin.blog_posts.index.table_headers.actions'), sortable: false },
];

</script>

<template>
    <Admin>
        <div class="w-full flex flex-col tablet:flex-row justify-between mb-6">
            <h1 class="text-3xl font-bold mb-4 tablet:mb-0">{{ t('admin.blog_posts.index.title') }}</h1>
            <IconLink v-tippy="t('admin.blog_posts.actions.create')" icon="Plus" type="info"
                :href="route('admin.posts.create')" />
        </div>
        <template v-if="totalPosts > 0">
            <DataTable :data="posts.data" :columns="columns" :links="posts.links" :current-sort="filters.sort"
                :current-direction="filters.direction" :current-search="filters.search" :searchable="totalPosts > 0"
                :search-placeholder="t('admin.blog_posts.index.search_placeholder')"
                :empty-message="filters.search
                    ? t('admin.blog_posts.index.no_posts_found_with_search', { search: filters.search })
                    : t('admin.blog_posts.index.no_posts_found')">

                <!-- Status column -->
                <template #cell-status="{ row }">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                        :class="row.is_published
                            ? 'bg-green-100 text-green-800'
                            : 'bg-gray-100 text-gray-800'">
                        {{ row.is_published ? t('admin.blog_posts.status.published') : t('admin.blog_posts.status.draft') }}
                    </span>
                </template>

                <!-- Published at column -->
                <template #cell-published_at="{ row }">
                    {{ formattedDate(row.published_at, { longDay: false, fallback: '-', locale: locale }) }}
                </template>

                <!-- Actions column -->
                <template #cell-actions="{ row }">
                    <DropdownMenu>
                        <template #default="{ MenuItem }">
                            <component :is="MenuItem">
                                <IconLink icon="Eye" :href="route('blog.show', row.slug)"
                                    v-tippy="t('admin.blog_posts.actions.view')" />
                            </component>
                            <component :is="MenuItem">
                                <IconLink icon="Pencil" :href="route('admin.posts.edit', row)"
                                    v-tippy="t('admin.blog_posts.actions.edit')" />
                            </component>
                            <component :is="MenuItem">
                                <IconLink type="delete" icon="Trash2" :href="route('admin.posts.destroy', row)"
                                    method="delete" :showConfirm="true"
                                    :prompt="t('admin.blog_posts.actions.delete_confirm')"
                                    v-tippy="t('admin.blog_posts.actions.delete')" />
                            </component>
                        </template>
                    </DropdownMenu>
                </template>
            </DataTable>
        </template>
        <template v-else>
            <div class="p-5">
                <p>{{ t('admin.blog_posts.index.no_posts') }}</p>
            </div>
        </template>
    </Admin>
</template>
