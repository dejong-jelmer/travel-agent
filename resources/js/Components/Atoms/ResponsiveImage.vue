<script setup>
import { computed } from 'vue'

const props = defineProps({
    // Image model with its WebP variants (`sources`, `fallback_source`)
    image: {
        type: Object,
        required: true,
    },
    // Rendered width per breakpoint, so the browser can pick the smallest variant that is sharp enough
    sizes: {
        type: String,
        required: true,
    },
    alt: {
        type: String,
        required: true,
    },
    loading: String,
    // Leaves out the image URLs, so nothing is fetched until the image is needed
    deferred: {
        type: Boolean,
        default: false,
    },
})

const srcset = computed(() => props.image.sources?.map((source) => `${source.url} ${source.width}w`).join(', ') || undefined)

// Images without variants (still being processed, or narrower than every variant) show the original upload
const fallback = computed(() => props.image.fallback_source ?? {
    url: props.image.public_url,
    width: props.image.width,
    height: props.image.height,
})
</script>

<template>
    <img :loading="loading" :sizes="srcset ? sizes : undefined" :srcset="deferred ? undefined : srcset"
        :src="deferred ? undefined : fallback.url" :width="fallback.width" :height="fallback.height" :alt="alt" />
</template>
