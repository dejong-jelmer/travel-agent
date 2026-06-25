<script setup>
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useDateFormatter } from '@/Composables/useDateFormatter';
import { useRevealEffect } from '@/Composables/useRevealEffect.js';
import { computed } from 'vue';

const { rootRef, visible, reveal } = useRevealEffect();

const props = defineProps({
    post: Object,
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
            <div ref="rootRef" v-if="post.hero_image" class="max-w-4xl mx-auto mb-12 ">
                <img :src="post.hero_image.public_url" :alt="post.title" v-bind="reveal(Math.min(index, 5) * 75)"
                    :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    class="w-full rounded-lg object-cover transition-all duration-1000 ease-out" />
            </div>

            <!-- Body -->
            <div class="max-w-3xl mx-auto prose prose-lg prose-brand" v-html="post.body"></div>
        </article>
    </Layout>
</template>
