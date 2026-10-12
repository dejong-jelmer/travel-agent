<script setup>
import { quoteProps } from '@/Support/testimonials.js'

defineProps({
    // Shape: [{ id, quote, paragraphs, author_name, author_context, travelled_in, photo, link_url, link_label }], the
    // published testimonials not tied to a trip, in their order, see Testimonial::toDisplay()
    testimonials: { type: Array, required: true },
});
</script>

<template>
    <!-- Takes the place of the PullQuote, in the same section: a single testimonial looks like it, with its author
         below; more go in a slider, a card each, as on the trip page -->
    <section class="bg-white py-16 tablet:py-24 px-6 text-left" :aria-label="$t('home.testimonials_label')">
        <QuoteBlock v-if="testimonials.length === 1" v-reveal size="large" v-bind="quoteProps(testimonials[0])"
            class="max-w-4xl mx-auto" />
        <TestimonialSlider v-else v-reveal :testimonials="testimonials" :label="$t('home.testimonials_label')"
            size="large" class="max-w-4xl mx-auto" />
    </section>
</template>
