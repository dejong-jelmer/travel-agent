<script setup>
import { quoteProps } from '@/Support/testimonials.js'

defineProps({
    // Shape: [{ id, quote, paragraphs, author_name, author_context, travelled_in, photo, link_url, link_label }], the
    // published testimonials of the trip in their order, see Testimonial::toDisplay(). A trip without them passes an
    // empty list and the section is not shown.
    testimonials: { type: Array, default: () => [] },
});
</script>

<template>
    <!-- No heading: the quote speaks for itself -->
    <section v-if="testimonials.length" v-reveal :aria-label="$t('trip_show.testimonials_label')">
        <BaseCard v-if="testimonials.length === 1" as="article">
            <QuoteBlock size="medium" v-bind="quoteProps(testimonials[0])" />
        </BaseCard>
        <TestimonialSlider v-else :testimonials="testimonials" :label="$t('trip_show.testimonials_label')" />
    </section>
</template>
