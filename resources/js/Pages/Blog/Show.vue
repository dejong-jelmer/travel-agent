<script setup>
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useDateFormatter } from '@/Composables/useDateFormatter';
import { computed } from 'vue';
import { lastUpdatedDate } from '@/Support/storyDates';

const props = defineProps({
    post: Object,
    // Shape: { intro, rest }, the HTML of the body split where the summary goes; rest is empty without a summary
    body: Object,
    // The key points of the story for "In het kort", empty when it has none
    summary: {
        type: Array,
        default: () => [],
    },
    // The name and bio for the author box, from config/blog.php
    author: Object,
    // The published trips this story goes with, see RelatedTrips
    trips: {
        type: Array,
        default: () => [],
    },
});

const { t } = useI18n();
const locale = computed(() => usePage().props.locale);
const { formattedDate } = useDateFormatter();

// The day the story was last updated, only when that is after the day it was published
const lastUpdated = computed(() => lastUpdatedDate(props.post.published_at, props.post.updated_at,
    (date) => formattedDate(date, { longDay: false, locale: locale.value })));
</script>

<template>
    <Layout>
        <article class="max-w-wide mx-auto px-4 py-12 laptop:py-20">
            <!-- Header -->
            <header class="max-w-3xl mx-auto text-center mb-10">
                <p class="text-sm text-gray-500">
                    <time :datetime="post.published_at">{{ formattedDate(post.published_at, {
                        longDay: false, locale: locale
                        }) }}</time>
                    <template v-if="lastUpdated">
                        <span aria-hidden="true"> · </span>
                        <time :datetime="post.updated_at" data-testid="last-updated">{{ t('blog.last_updated', { date: lastUpdated }) }}</time>
                    </template>
                </p>
                <h1 class="text-4xl laptop:text-5xl font-poppins text-brand-text mt-3">
                    {{ post.title }}
                </h1>
                <p v-if="post.excerpt" class="text-lg text-gray-600 mt-4">
                    {{ post.excerpt }}
                </p>
            </header>

            <!-- Featured Image -->
            <div v-if="post.hero_image" class="max-w-4xl mx-auto mb-12 ">
                <ResponsiveImage :image="post.hero_image" sizes="(min-width: 928px) 896px, calc(100vw - 32px)"
                    :alt="post.hero_image.alt || post.title" class="w-full rounded-lg object-cover" />
            </div>

            <!-- Body: the introduction, the summary of the key points, then the rest of the story -->
            <div class="max-w-3xl mx-auto prose prose-lg prose-brand" v-html="body.intro"></div>
            <StorySummary :points="summary" class="max-w-3xl mx-auto my-10" />
            <div v-if="body.rest" class="max-w-3xl mx-auto prose prose-lg prose-brand" v-html="body.rest"></div>

            <!-- Who wrote it -->
            <AuthorBox :name="author.name" :bio="author.bio" class="max-w-3xl mx-auto mt-16" />
        </article>

        <!-- The trips this story goes with -->
        <RelatedTrips :trips="trips" class="max-w-wide mx-auto px-4 pb-12 laptop:pb-20" />
    </Layout>
</template>
