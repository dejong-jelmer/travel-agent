<script setup>
import placeholder from '@/../images/placeholder.png';
import { computed } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'

const props = defineProps({
    title: {
        type: String,
        default: null
    },
    description: {
        type: String,
        default: null
    },
    image: {
        type: String,
        default: null
    },
    url: {
        type: String,
        default: null
    }
})

const page = usePage()
console.log(page.props);


const metaTitle = computed(() => {
    return props.title || page.props.seo?.title || ''
})

const metaDescription = computed(() => {
    return props.description || page.props.seo?.description || ''
})

const metaImage = computed(() => {
    return props.image || page.props.seo?.og_image || placeholder
})

const metaUrl = computed(() => {
    return props.url || window.location.href
})

console.log('metaTitle: ', metaTitle.value)
console.log('metaDescription: ', metaDescription.value)
console.log('metaImage: ', metaImage.value)
console.log('metaUrl: ', metaUrl.value)

</script>
<template>
    <Head>
        <title>{{ metaTitle }}</title>
        <meta name="description" :content="metaDescription" />

        <!-- Open Graph -->
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="metaUrl" />
        <meta property="og:title" :content="metaTitle" />
        <meta property="og:description" :content="metaDescription" />
        <meta property="og:image" :content="metaImage" v-if="metaImage" />

        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image" />
        <meta property="twitter:url" :content="metaUrl" />
        <meta property="twitter:title" :content="metaTitle" />
        <meta property="twitter:description" :content="metaDescription" />
        <meta property="twitter:image" :content="metaImage" v-if="metaImage" />
    </Head>
</template>
