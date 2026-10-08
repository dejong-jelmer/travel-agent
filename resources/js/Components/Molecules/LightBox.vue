<script setup>
import PhotoSwipeLightbox from 'photoswipe/lightbox'
import 'photoswipe/style.css'
import { onBeforeUnmount } from 'vue'
import { useI18n } from 'vue-i18n'

// Renders nothing: PhotoSwipe adds its own dialog to the end of <body>
defineOptions({ render: () => null })

const props = defineProps({
    images: {
        type: Array,
        required: true
    }
})

const { t } = useI18n()

// A thumbnail scrolled out of view in a slider has no visible spot to zoom from or back to; PhotoSwipe fades instead
const isInView = (element) => {
    const rect = element.getBoundingClientRect()
    for (let parent = element.parentElement; parent; parent = parent.parentElement) {
        if (getComputedStyle(parent).overflowX === 'visible') continue
        const clip = parent.getBoundingClientRect()
        if (rect.left < clip.left || rect.right > clip.right) return false
    }
    return true
}

// Every slide offers the WebP variants as srcset; PhotoSwipe sets `sizes` to the displayed width, so the browser
// picks the smallest variant that is sharp enough. Width and height are those of the widest file, the furthest
// PhotoSwipe zooms in. Alt text and placeholder come from the thumbnail, if there is one.
const slide = (image, thumbnail) => {
    const widest = image.sources?.at(-1) ?? { url: image.public_url, width: image.width, height: image.height }
    const thumbnailImage = thumbnail?.querySelector('img')

    return {
        src: widest.url,
        srcset: image.sources?.map((source) => `${source.url} ${source.width}w`).join(', ') || undefined,
        width: widest.width,
        height: widest.height,
        alt: thumbnailImage?.alt,
        msrc: thumbnailImage?.currentSrc,
        element: thumbnail && isInView(thumbnail) ? thumbnail : undefined,
        // Thumbnails use object-cover, so they show a crop of the image
        thumbCropped: true,
    }
}

let lightbox = null

// Opens the image at `index`; `thumbnails` are the elements the images open from, in the same order
function open(index, thumbnails = []) {
    lightbox = new PhotoSwipeLightbox({
        dataSource: props.images.map((image, i) => slide(image, thumbnails[i])),
        pswpModule: () => import('photoswipe'),
        bgClickAction: false,
        closeTitle: t('lightbox.close'),
        zoomTitle: t('lightbox.zoom'),
        arrowPrevTitle: t('lightbox.previous'),
        arrowNextTitle: t('lightbox.next'),
        errorMsg: t('lightbox.error'),
    })
    lightbox.loadAndOpen(index)
}

onBeforeUnmount(() => lightbox?.destroy())

defineExpose({ open })
</script>
