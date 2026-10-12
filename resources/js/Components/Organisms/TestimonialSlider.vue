<script setup>
import { quoteProps } from '@/Support/testimonials.js'

defineProps({
    // Shape: [{ id, quote, paragraphs, author_name, author_context, travelled_in, photo, link_url, link_label }], see
    // Testimonial::toDisplay()
    testimonials: { type: Array, required: true },
    // Accessible name of the slider
    label: { type: String, required: true },
    // Size of the quotes, see QuoteBlock
    size: { type: String, default: 'medium' },
});
</script>

<template>
    <!-- A card per slide: the slider buttons overlap the edges of the slides, which here is the card padding.
         All cards take the height of the tallest, which grows when its account unfolds. -->
    <HorizontalSlider :items="testimonials" :label="label" :visible="1" :gap="16">
        <template #default="{ item }">
            <BaseCard as="article" class="h-full">
                <QuoteBlock :size="size" v-bind="quoteProps(item)" />
            </BaseCard>
        </template>
    </HorizontalSlider>
</template>
