<script setup>
import { computed } from 'vue';

const props = defineProps({
    trip: { type: Object, required: true },
});

// Supports both the legacy string format and the new { title, description } shape,
// so trips that have not been migrated yet keep rendering.
const highlights = computed(() =>
    (props.trip.highlights ?? []).map((highlight) =>
        typeof highlight === 'string'
            ? { title: highlight.split(':')[0], description: highlight.split(':')[1] }
            : { title: highlight.title, description: highlight.description },

        // { title: highlight, description: null }
    ),
);
</script>

<template>
    <ul class="space-y-4">
        <li v-for="highlight in highlights" :key="highlight.title" class="flex items-start gap-3">
            <span class="w-2 h-2 bg-brand-subtle rounded-full mt-2 flex-shrink-0" aria-hidden="true"></span>
            <div class="text-brand-text">
                <h3
                    class="inline font-medium after:content-[':']"
                >{{ highlight.title }}</h3>
                <p v-if="highlight.description" class="inline">{{ highlight.description }}</p>
            </div>
        </li>
    </ul>
</template>
