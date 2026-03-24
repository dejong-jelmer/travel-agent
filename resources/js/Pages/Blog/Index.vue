<script setup>
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    posts: Object,
});

const { t } = useI18n();

function formatDate(dateStr) {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('nl-NL', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });
}
</script>

<template>
    <Layout>
        <section class="max-w-6xl mx-auto px-4 py-12 laptop:py-20 text-center">
            <SectionHeader>
                {{ t('blog.index.title') }}
            </SectionHeader>

            <div class="grid grid-cols-1 tablet:grid-cols-2 laptop:grid-cols-3 gap-8">
                <Link v-for="post in posts.data" :key="post.id"
                    :href="route('blog.show', post.slug)">
                    <Card>
                        <!-- Featured Image -->
                        <div class="aspect-[16/10] overflow-hidden bg-gray-100">
                            <img v-if="post.hero_image"
                                :src="post.hero_image.public_url"
                                :alt="post.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                            <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <time class="text-sm text-gray-500">{{ formatDate(post.published_at) }}</time>
                            <h2 class="text-xl font-semibold text-brand-text mt-2 group-hover:text-brand-primary transition-colors">
                                {{ post.title }}
                            </h2>
                            <p v-if="post.excerpt" class="text-gray-600 mt-3 line-clamp-3">
                                {{ post.excerpt }}
                            </p>
                        </div>
                    </Card>
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="posts.links && posts.last_page > 1" class="mt-12 flex justify-center">
                <nav class="flex gap-2">
                    <Link v-for="link in posts.links" :key="link.label"
                        :href="link.url"
                        class="px-4 py-2 rounded-lg text-sm"
                        :class="{
                            'bg-brand-primary text-white': link.active,
                            'bg-white text-gray-700 hover:bg-gray-100': !link.active && link.url,
                            'text-gray-400 cursor-default': !link.url,
                        }"
                        v-html="link.label"
                        :preserve-scroll="true" />
                </nav>
            </div>

            <!-- Empty state -->
            <div v-if="!posts.data.length" class="text-center py-20">
                <p class="text-gray-500 text-lg">{{ t('blog.index.no_posts') }}</p>
            </div>
        </section>
    </Layout>
</template>
