<script setup>
import { computed } from 'vue';

const props = defineProps({
    highlights: { type: Object, required: true },
});

// A highlight is either a { title, description } object or a plain string title.
const items = computed(() =>
    (props.highlights ?? []).map((highlight) =>
        typeof highlight === 'string'
            ? { title: highlight, description: null }
            : highlight,
    ),
);

// Arbitrary Tailwind value; the underscore renders as the space after the colon.
const colonAfter = "after:content-[':_']";
</script>

<template>
    <ul class="space-y-4">
        <li v-for="(highlight, index) in items" :key="index" class="flex items-start gap-3">
            <span class="w-2 h-2 bg-brand-subtle rounded-full mt-2 flex-shrink-0" aria-hidden="true"></span>
            <div class="text-brand-text text-base">
                <h3 class="inline font-bold" :class="highlight.description ? colonAfter : ''">{{ highlight.title }}</h3>
                <p v-if="highlight.description" class="inline">{{ highlight.description }}</p>
            </div>
        </li>
    </ul>
</template>
