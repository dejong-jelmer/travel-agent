<script setup>
defineProps({
    // Shape: [{ title, description, category, icon, label }], description null when the highlight only has a title;
    // category, icon and label null without a category. A trip without highlights passes an empty list and the
    // section is not shown.
    highlights: { type: Array, default: () => [] },
});
</script>

<template>
    <section v-if="highlights?.length" aria-labelledby="trip-highlights-heading">
        <h2 id="trip-highlights-heading" class="text-[26px] font-semibold text-brand-primary mb-4">
            {{ $t('trip_show.highlights_heading') }}
        </h2>

        <!-- Two columns from tablet up, stacked on phones. The second tile of a row comes into view a little later. -->
        <div class="grid grid-cols-1 tablet:grid-cols-2 gap-4">
            <BaseCard v-for="(highlight, index) in highlights" :key="highlight.title" v-reveal="{ delay: index % 2 ? 80 : 0 }"
                as="article" padding="compact">
                <!-- Category: brand-primary/85 instead of brand-light keeps the small label at 4.9:1 on white (WCAG AA) -->
                <div v-if="highlight.category" class="flex items-center gap-[10px] mb-3">
                    <HighlightIcon :icon="highlight.icon" :stroke-width="1.8"
                        class="w-[22px] h-[22px] flex-shrink-0 text-brand-primary" />
                    <span class="text-[12px] tablet:text-[12.5px] uppercase tracking-[0.14em] text-brand-primary/85">
                        {{ highlight.label }}
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-brand-primary" :class="{ 'mb-2': highlight.description }">
                    {{ highlight.title }}
                </h3>
                <p v-if="highlight.description" class="text-[15.5px] leading-[1.65] text-brand-text">
                    {{ highlight.description }}
                </p>
            </BaseCard>
        </div>
    </section>
</template>
