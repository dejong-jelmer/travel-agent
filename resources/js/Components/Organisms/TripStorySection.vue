<script setup>
import { computed } from 'vue';

const props = defineProps({
    // Shape: { key, title, html }, one section of the trip description as split on its H2 headings
    section: { type: Object, required: true },
});

const headingId = computed(() => `trip-story-${props.section.key}-heading`);
</script>

<template>
    <BaseCard :aria-labelledby="section.title ? headingId : undefined">
        <h2 v-if="section.title" :id="headingId" class="text-[26px] font-semibold text-brand-primary mb-4">
            {{ section.title }}
        </h2>
        <!-- prose-brand sets its own size and line height on paragraphs, the prose-p modifiers override those -->
        <div class="prose prose-brand max-w-[68ch] text-brand-text text-[17px] leading-[1.75] prose-p:text-[17px] prose-p:leading-[1.75]"
            v-html="section.html"></div>
    </BaseCard>
</template>
