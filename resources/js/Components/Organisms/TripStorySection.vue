<script setup>
import { computed } from 'vue';

const props = defineProps({
    // Shape: { key, title, html }, one section of the trip description as split on its H2 headings
    section: { type: Object, required: true },
    // Light: the regular white card, dark: a card in the brand text color that makes the section stand out
    variant: {
        type: String,
        default: 'light',
        validator: (value) => ['light', 'dark'].includes(value),
    },
});

const headingId = computed(() => `trip-story-${props.section.key}-heading`);

// Contrast on brand-text: accent 6.5:1, secondary 12.4:1, link 6.1:1 (WCAG AA). Links keep the underline from
// prose, as their color differs too little from the body text (2:1) to stand out by color alone.
const headingClass = computed(() => props.variant === 'dark' ? 'text-brand-accent' : 'text-brand-primary');
const proseClass = computed(() => props.variant === 'dark'
    ? 'text-brand-secondary prose-headings:text-brand-accent prose-p:text-brand-secondary prose-strong:text-brand-secondary prose-code:text-brand-secondary prose-blockquote:text-brand-secondary prose-blockquote:border-brand-secondary/30 prose-a:text-brand-link prose-hr:border-brand-secondary/20 marker:text-brand-secondary/70'
    : 'text-brand-text');
</script>

<template>
    <BaseCard :variant="variant" :aria-labelledby="section.title ? headingId : undefined">
        <h2 v-if="section.title" :id="headingId" class="text-[26px] font-semibold mb-4" :class="headingClass">
            {{ section.title }}
        </h2>
        <!-- prose-brand sets its own size and line height on paragraphs, the prose-p modifiers override those -->
        <div class="prose prose-brand max-w-[68ch] text-[17px] leading-[1.75] prose-p:text-[17px] prose-p:leading-[1.75]"
            :class="proseClass" v-html="section.html"></div>
    </BaseCard>
</template>
