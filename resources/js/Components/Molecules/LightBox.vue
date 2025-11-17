<script setup>
import VueEasyLightbox from 'vue-easy-lightbox'

import { ref } from 'vue'
const props = defineProps({
    images: {
        type:Object,
        required: false
    }
})
// Image Lightbox
const lightboxVisible = ref(false)
const lightboxIndex = ref(0)
function open(index) {
  lightboxIndex.value = index
  lightboxVisible.value = true
}

defineExpose({ open })
</script>

<template>
    <VueEasyLightbox
        :visible="lightboxVisible"
        :imgs="images.map((img) => img.full_path)"
        :index="lightboxIndex"
        :move-disabled="true"
        :scroll-disabled="true"
        :rotate-disabled="true"
        :zoom-disabled="true"
        @hide="lightboxVisible = false" />
</template>
ipt>
