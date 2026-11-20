<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { useDateFormatter } from '@/Composables/useDateFormatter';
import { computed } from 'vue';

defineProps({
    // Shape: { slug, title, excerpt, published_at, hero_image }
    post: {
        type: Object,
        required: true,
    },
    // The width the image is rendered at, see ResponsiveImage
    sizes: {
        type: String,
        default: '(min-width: 900px) 360px, (min-width: 600px) 50vw, 100vw',
    },
    // Eager for a card that is in view when the page opens
    loading: {
        type: String,
        default: 'lazy',
    },
});

const locale = computed(() => usePage().props.locale);
const { formattedDate } = useDateFormatter();
</script>

<template>
    <Link :href="route('blog.show', post.slug)">
        <Card>
            <!-- Featured Image -->
            <div class="aspect-[16/10] overflow-hidden bg-gray-100">
                <ResponsiveImage v-if="post.hero_image" :image="post.hero_image" :sizes="sizes" :alt="post.hero_image.alt || post.title"
                    :loading="loading"
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
                <time class="text-sm text-gray-500">{{ formattedDate(post.published_at, { longDay: false, locale: locale }) }}</time>
                <h2 class="text-xl font-semibold text-brand-text mt-2 group-hover:text-brand-primary transition-colors">
                    {{ post.title }}
                </h2>
                <p v-if="post.excerpt" class="text-gray-600 mt-3 line-clamp-3">
                    {{ post.excerpt }}
                </p>
            </div>
        </Card>
    </Link>
</template>
