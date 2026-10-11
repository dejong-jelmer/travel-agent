<script setup>
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { fetchApi } from '@/fetchApi';

defineProps({
    // Shape: [{ id, quote, author_name, relationship, trip_name, photo_url, is_published, sort_order }], in display
    // order; relationship is the label, trip_name null for a general testimonial
    testimonials: Array,
});

const { t } = useI18n();

// SortableBlocks numbers the blocks it has moved in `order`; blocks fresh from the server only have their sort_order
function updateOrder(blocks) {
    fetchApi(route('admin.testimonials.order'), {
        method: 'PATCH',
        body: { testimonials: blocks.map((block) => ({ id: block.id, sort_order: block.order ?? block.sort_order })) },
    }).catch((error) => console.error(error));
}

function togglePublished(testimonial) {
    router.patch(route('admin.testimonials.publish', testimonial.id), {}, { preserveScroll: true });
}
</script>

<template>
    <Admin>
        <div class="w-full flex flex-col tablet:flex-row justify-between mb-6">
            <div class="mb-4 tablet:mb-0">
                <h1 class="text-3xl font-bold">{{ t('admin.testimonials.index.title') }}</h1>
                <p v-if="testimonials.length > 1" class="mt-1 text-sm text-gray-700/50">
                    {{ t('admin.testimonials.index.drag_help') }}
                </p>
            </div>
            <IconLink v-tippy="t('admin.testimonials.actions.create')" icon="Plus" type="info"
                :href="route('admin.testimonials.create')" />
        </div>

        <SortableBlocks v-if="testimonials.length" :blocks="testimonials" @update:order="updateOrder" class="space-y-4">
            <template #default="{ block }">
                <div class="flex flex-col tablet:flex-row tablet:items-center gap-4 bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <img v-if="block.photo_url" :src="block.photo_url" alt=""
                            class="w-11 h-11 flex-shrink-0 rounded-full object-cover" />
                        <div class="min-w-0">
                            <p class="text-gray-700 line-clamp-2">“{{ block.quote }}”</p>
                            <p class="mt-1 text-sm text-gray-700/60">
                                {{ block.author_name }} · {{ block.relationship }} ·
                                {{ block.trip_name ?? t('admin.testimonials.index.general') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 flex-shrink-0">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                            :class="block.is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                            {{ block.is_published ? t('admin.testimonials.status.published') : t('admin.testimonials.status.draft') }}
                        </span>
                        <button type="button" @click="togglePublished(block)"
                            class="text-sm font-medium text-slate-700 underline-offset-4 hover:underline">
                            {{ block.is_published ? t('admin.testimonials.actions.unpublish') : t('admin.testimonials.actions.publish') }}
                        </button>
                        <IconLink icon="Pencil" :href="route('admin.testimonials.edit', block.id)"
                            v-tippy="t('admin.testimonials.actions.edit')" />
                        <IconLink type="delete" icon="Trash2" :href="route('admin.testimonials.destroy', block.id)"
                            method="delete" :showConfirm="true" :prompt="t('admin.testimonials.actions.delete_confirm')"
                            v-tippy="t('admin.testimonials.actions.delete')" />
                    </div>
                </div>
            </template>
        </SortableBlocks>
        <p v-else class="p-5">{{ t('admin.testimonials.index.no_testimonials') }}</p>
    </Admin>
</template>
