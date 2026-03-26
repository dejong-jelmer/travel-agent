<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useDateFormatter } from '@/Composables/useDateFormatter';
import { computed } from 'vue';

const props = defineProps({
    blogPost: Object,
});

const { t } = useI18n();
const locale = computed(() => usePage().props.locale);
const { formattedDate } = useDateFormatter();
</script>

<template>
    <Admin>
        <div class="max-w-wide mx-auto">
            <div class="grid grid-cols-1 laptop:grid-cols-3 gap-8">
                <!-- Header Section -->
                <div class="laptop:col-span-3 bg-white py-10">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-700">
                                {{ blogPost.title }}
                            </h1>
                            <p class="mt-1 text-sm text-gray-700/50">
                                {{ blogPost.slug }}
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <IconLink icon="Pencil" :href="route('admin.posts.edit', blogPost)"
                                v-tippy="t('admin.blog_posts.actions.edit')" />
                            <IconLink icon="Eye" :href="route('blog.show', blogPost.slug)"
                                v-tippy="t('admin.blog_posts.actions.view')" />
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="laptop:col-span-2 space-y-8">
                    <!-- Excerpt -->
                    <section v-if="blogPost.excerpt" class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">{{ t('admin.blog_posts.show.excerpt') }}</h2>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-600">{{ blogPost.excerpt }}</p>
                        </div>
                    </section>

                    <!-- Body -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">{{ t('admin.blog_posts.show.body') }}</h2>
                        </div>
                        <div class="p-6 prose max-w-none" v-html="blogPost.body"></div>
                    </section>
                </div>

                <!-- Sidebar -->
                <div class="space-y-8">
                    <!-- Status -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">{{ t('admin.blog_posts.show.status') }}</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">{{ t('admin.blog_posts.show.status') }}</span>
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                    :class="blogPost.is_published
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-gray-100 text-gray-800'">
                                    {{ blogPost.is_published ? t('admin.blog_posts.status.published') : t('admin.blog_posts.status.draft') }}
                                </span>
                            </div>
                            <div v-if="blogPost.published_at" class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">{{ t('admin.blog_posts.show.published_at') }}</span>
                                <span class="text-sm text-gray-700">{{ formattedDate(blogPost.published_at, { longDay: false, fallback: '-', hour: true, minute: true, locale: locale }) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">{{ t('admin.blog_posts.show.created_at') }}</span>
                                <span class="text-sm text-gray-700">{{ formattedDate(blogPost.created_at, { longDay: false, fallback: '-', hour: true, minute: true, locale: locale }) }}</span>
                            </div>
                        </div>
                    </section>

                    <!-- Featured Image -->
                    <section v-if="blogPost.hero_image" class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">{{ t('admin.blog_posts.show.featured_image') }}</h2>
                        </div>
                        <div class="p-6">
                            <img :src="blogPost.hero_image.public_url" :alt="blogPost.title"
                                class="w-full rounded-lg object-cover" />
                        </div>
                    </section>

                    <!-- Meta / SEO -->
                    <section v-if="blogPost.meta_title || blogPost.meta_description"
                        class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">SEO</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div v-if="blogPost.meta_title">
                                <span class="text-sm font-medium text-gray-500">{{ t('forms.blog_post.fields.meta_title') }}</span>
                                <p class="text-sm text-gray-700 mt-1">{{ blogPost.meta_title }}</p>
                            </div>
                            <div v-if="blogPost.meta_description">
                                <span class="text-sm font-medium text-gray-500">{{ t('forms.blog_post.fields.meta_description') }}</span>
                                <p class="text-sm text-gray-700 mt-1">{{ blogPost.meta_description }}</p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </Admin>
</template>
