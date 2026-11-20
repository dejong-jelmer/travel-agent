<script setup>
defineProps({
    // Shape: [{ id, slug, title, excerpt, published_at, hero_image }], the published stories of a trip, newest first.
    // A trip without them passes an empty list and the block is not shown.
    stories: { type: Array, default: () => [] },
});
</script>

<template>
    <BaseCard v-if="stories.length" padding="compact" aria-labelledby="related-stories-heading">
        <SectionHeader id="related-stories-heading">{{ $t('trip_show.related_stories') }}</SectionHeader>
        <!-- Two side by side from tablet on: the cards sit in the main column, two thirds of the page -->
        <div class="grid grid-cols-1 tablet:grid-cols-2 gap-6">
            <StoryCard v-for="story in stories" :key="story.id" :post="story"
                sizes="(min-width: 1350px) 400px, (min-width: 600px) 50vw, 100vw" />
        </div>
    </BaseCard>
</template>
