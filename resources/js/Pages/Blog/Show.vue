<script setup>
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useDateFormatter } from '@/Composables/useDateFormatter';
import { computed } from 'vue';

const props = defineProps({
    post: Object,
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
</script>

<template>
    <Layout>
        <article class="max-w-wide mx-auto px-4 py-12 laptop:py-20">
            <!-- Header -->
            <header class="max-w-3xl mx-auto text-center mb-10">
                <time class="text-sm text-gray-500">{{ formattedDate(post.published_at, {
                    longDay: false, locale: locale
                    }) }}</time>
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

            <!-- Body -->
            <div class="max-w-3xl mx-auto prose prose-lg prose-brand" v-html="post.body"></div>

            <!-- Who wrote it -->
            <AuthorBox :name="author.name" :bio="author.bio" class="max-w-3xl mx-auto mt-16" />
        </article>

        <!-- The trips this story goes with -->
        <RelatedTrips :trips="trips" class="max-w-wide mx-auto px-4 pb-12 laptop:pb-20" />
    </Layout>
</template>
