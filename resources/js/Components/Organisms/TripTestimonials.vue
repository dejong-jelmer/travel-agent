<script setup>
defineProps({
    // Shape: [{ id, quote, paragraphs, author_name, author_context, travelled_in, photo, link_url, link_label }], the
    // published testimonials of the trip in their order, see TripController::testimonials(). A trip without them
    // passes an empty list and the section is not shown.
    testimonials: { type: Array, default: () => [] },
});
</script>

<template>
    <!-- No heading: the quote speaks for itself -->
    <section v-if="testimonials.length" v-reveal :aria-label="$t('trip_show.testimonials_label')">
        <BaseCard v-if="testimonials.length === 1" as="article">
            <QuoteBlock size="medium" :quote="testimonials[0].quote" :name="testimonials[0].author_name"
                :context="testimonials[0].author_context" :date="testimonials[0].travelled_in"
                :photo="testimonials[0].photo" :paragraphs="testimonials[0].paragraphs"
                :link-url="testimonials[0].link_url" :link-label="testimonials[0].link_label" />
        </BaseCard>

        <!-- A card per slide: the slider buttons overlap the edges of the slides, which here is the card padding.
             All cards take the height of the tallest, which grows when its account unfolds. -->
        <HorizontalSlider v-else :items="testimonials" :label="$t('trip_show.testimonials_label')" :visible="1"
            :gap="16">
            <template #default="{ item }">
                <BaseCard as="article" class="h-full">
                    <QuoteBlock size="medium" :quote="item.quote" :name="item.author_name" :context="item.author_context"
                        :date="item.travelled_in" :photo="item.photo" :paragraphs="item.paragraphs"
                        :link-url="item.link_url" :link-label="item.link_label" />
                </BaseCard>
            </template>
        </HorizontalSlider>
    </section>
</template>
