<script setup>
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    posts: Object,
});

const { t } = useI18n();
</script>

<template>
    <Layout>
        <section class="max-w-6xl mx-auto px-4 py-12 laptop:py-20 text-center">
            <SectionHeader>
                {{ t('blog.index.title') }}
            </SectionHeader>

            <!-- The cards of a row come into view one after the other (three columns from laptop on) -->
            <div class="grid grid-cols-1 tablet:grid-cols-2 laptop:grid-cols-3 gap-8">
                <!-- The first card is in view on every screen, so its image is not lazy loaded -->
                <StoryCard v-for="(post, index) in posts.data" :key="post.id" :post="post"
                    :loading="index === 0 ? 'eager' : 'lazy'" v-reveal="{ delay: (index % 3) * 80 }" />
            </div>

            <!-- Pagination -->
            <div v-if="posts.links && posts.last_page > 1" class="mt-12 flex justify-center">
                <nav class="flex gap-2">
                    <template v-for="link in posts.links" :key="link.label">
                        <span v-if="!link.url"
                            class="px-4 py-2 rounded-lg text-sm text-gray-400 cursor-default"
                            v-html="link.label" />
                        <Link v-else
                            :href="link.url"
                            class="px-4 py-2 rounded-lg text-sm"
                            :class="{
                                'bg-brand-primary text-white': link.active,
                                'bg-white text-gray-700 hover:bg-gray-100': !link.active,
                            }"
                            v-html="link.label"
                            :preserve-scroll="true" />
                    </template>
                </nav>
            </div>

            <!-- Empty state -->
            <div v-if="!posts.data.length" class="text-center py-20">
                <p class="text-gray-500 text-lg">{{ t('blog.index.no_posts') }}</p>
            </div>
        </section>
    </Layout>
</template>
