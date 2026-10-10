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
    // Gallery image shown next to the card; without one the card takes the full width
    image: { type: Object, default: null },
    imageAlt: { type: String, default: '' },
    // Side of the photo from tablet on; on phones it always sits above the card
    imageSide: {
        type: String,
        default: 'left',
        validator: (value) => ['left', 'right'].includes(value),
    },
});

const headingId = computed(() => `trip-story-${props.section.key}-heading`);

// Contrast on brand-text: accent 6.5:1, secondary 12.4:1, link 6.1:1 (WCAG AA). Links keep the underline from
// prose, as their color differs too little from the body text (2:1) to stand out by color alone.
const headingClass = computed(() => props.variant === 'dark' ? 'text-brand-accent' : 'text-brand-primary');
const proseClass = computed(() => props.variant === 'dark'
    ? 'text-brand-secondary prose-headings:text-brand-accent prose-p:text-brand-secondary prose-strong:text-brand-secondary prose-code:text-brand-secondary prose-blockquote:text-brand-secondary prose-blockquote:border-brand-secondary/30 prose-a:text-brand-link prose-hr:border-brand-secondary/20 marker:text-brand-secondary/70'
    : 'text-brand-text');

// Card and photo side by side from tablet on (text 8fr, photo 4fr), stacked with the photo on top on phones
const gridClass = computed(() => props.imageSide === 'left'
    ? 'grid gap-6 tablet:grid-cols-[minmax(0,4fr)_minmax(0,8fr)]'
    : 'grid gap-6 tablet:grid-cols-[minmax(0,8fr)_minmax(0,4fr)]');

// Rendered photo width per breakpoint (screens.js), following the layout around the story cards: the page padding
// (px-4, tablet:px-6), from laptop on the two-thirds column (grid-cols-3, gap-12, max-w-screen-desktop) and a third
// of that column minus the 24px gap from tablet on. Update this when that layout changes.
const imageSizes = '(min-width: 1350px) 276px, (min-width: 900px) calc(22.2vw - 24px), (min-width: 600px) calc(33.3vw - 24px), calc(100vw - 32px)';
</script>

<template>
    <div :class="image ? gridClass : undefined">
        <!-- The photo is positioned absolutely, so it follows the height of the card instead of adding its own.
             It fades in, while the card also moves up as it comes into view. -->
        <div v-if="image" v-reveal="{ fade: true }" class="relative aspect-[16/10] tablet:aspect-auto tablet:min-h-[280px]"
            :class="{ 'tablet:order-last': imageSide === 'right' }">
            <ResponsiveImage :image="image" :sizes="imageSizes" :alt="imageAlt" loading="lazy"
                class="absolute inset-0 w-full h-full object-cover rounded-xl" />
        </div>
        <BaseCard v-reveal :variant="variant" :aria-labelledby="section.title ? headingId : undefined">
            <h2 v-if="section.title" :id="headingId" class="text-[26px] font-semibold mb-4" :class="headingClass">
                {{ section.title }}
            </h2>
            <!-- prose-brand sets its own size and line height on paragraphs, the prose-p modifiers override those -->
            <div class="prose prose-brand max-w-[68ch] text-[17px] leading-[1.75] prose-p:text-[17px] prose-p:leading-[1.75]"
                :class="proseClass" v-html="section.html"></div>
        </BaseCard>
    </div>
</template>
