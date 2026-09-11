<script setup>
import VueEasyLightbox from 'vue-easy-lightbox'

import { ref, computed, watch, onBeforeUnmount } from 'vue'
const props = defineProps({
    images: {
        type: Array,
        required: true
    }
})
// Image Lightbox
const lightboxVisible = ref(false)
const lightboxIndex = ref(0)
// The widest variant covers the viewport, while the original upload can be several megabytes
const imageUrls = computed(() => props.images.map((img) => img.sources?.at(-1)?.url ?? img.public_url))

// vue-easy-lightbox locks scrolling via `overflow-y: hidden` on <body>, but that
// is not propagated to the viewport because <html> has `overflow-x: clip` (app.css).
// Lock the root element ourselves while the lightbox is open.
const lockScroll = (locked) => {
    document.documentElement.style.overflowY = locked ? 'hidden' : ''
}
watch(lightboxVisible, lockScroll)
onBeforeUnmount(() => lockScroll(false))

function open(index) {
  lightboxIndex.value = index
  lightboxVisible.value = true
}

defineExpose({ open })
</script>

<template>
    <VueEasyLightbox
        :visible="lightboxVisible"
        :imgs="imageUrls"
        :index="lightboxIndex"
        :rotate-disabled="true"
        :mask-closable="false"
        @hide="lightboxVisible = false" />
</template>
