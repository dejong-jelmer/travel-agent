<script setup>
defineProps({
    // The key points of a story, see BlogPost::summaryPoints(). Without points the block is not shown.
    points: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <aside v-if="points.length" aria-labelledby="story-summary-heading"
        class="rounded-2xl border border-brand-accent/30 bg-brand-secondary px-6 py-6 tablet:px-8">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-4 h-px bg-brand-accent"></div>
            <h2 id="story-summary-heading" class="text-xs font-medium tracking-widest uppercase text-brand-primary">
                {{ $t('blog.summary_title') }}
            </h2>
        </div>
        <ul class="space-y-3">
            <li v-for="(point, index) in points" :key="index" class="flex items-start gap-3">
                <!-- The dot sits on the first line: leading-7 is 28px, the dot 8px, so 10px down -->
                <span class="mt-[10px] w-2 h-2 flex-shrink-0 rounded-full bg-brand-accent" aria-hidden="true"></span>
                <span class="text-base leading-7 text-brand-text">{{ point }}</span>
            </li>
        </ul>
    </aside>
</template>
