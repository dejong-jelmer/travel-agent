<script setup>
import { ref, computed, useId } from 'vue'
import { Quote, ChevronDown } from '@lucide/vue'
import { useI18n } from 'vue-i18n'

const props = defineProps({
    quote: {
        type: String,
        required: true,
    },
    // Name of the author, below the quote
    name: {
        type: String,
        default: null,
    },
    // Who the author is, e.g. 'My father, about his trip to the Vézère valley', below the name
    context: {
        type: String,
        default: null,
    },
    // When the author travelled, as month and year ready for display
    date: {
        type: String,
        default: null,
    },
    // Image model with its WebP variants, shown small and round next to the name
    photo: {
        type: Object,
        default: null,
    },
    // The full account below the quote, one string per paragraph; without paragraphs there is nothing to unfold
    paragraphs: {
        type: Array,
        default: () => [],
    },
    // Link to a longer story, and its text instead of the default
    linkUrl: {
        type: String,
        default: null,
    },
    linkLabel: {
        type: String,
        default: null,
    },
    // Large on the homepage, medium on the trip page
    size: {
        type: String,
        default: 'large',
        validator: (value) => ['large', 'medium'].includes(value),
    },
})

const { t } = useI18n()

const quoteClasses = {
    large: 'text-2xl laptop:text-3xl',
    medium: 'text-[21px] tablet:text-[26px]',
}

const details = computed(() => [props.context, props.date].filter(Boolean).join(' · '))
const linkText = computed(() => props.linkLabel || t('quote_block.read_story'))

// The full account is always in the page, only visually collapsed. While collapsed it is inert, so the link at its
// end is out of the tab order and hidden from screen readers.
const open = ref(false)
const bodyId = useId()

// Drawn outside the element: in a slider the card padding leaves room for it
const actionClasses = 'inline-flex items-center gap-2 min-h-[44px] font-semibold text-brand-primary underline-offset-4 hover:underline rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-accent'
</script>

<template>
    <div>
        <figure>
            <Quote class="w-8 h-8 text-brand-accent rotate-180" aria-hidden="true" />
            <blockquote class="mt-4">
                <p class="font-poppins text-balance leading-snug text-brand-primary" :class="quoteClasses[size]">
                    {{ quote }}
                </p>
            </blockquote>
            <figcaption v-if="name || details" class="mt-6 flex items-center gap-3">
                <!-- The name is right next to the photo, so it needs no alt text of its own -->
                <ResponsiveImage v-if="photo" :image="photo" sizes="44px" alt="" loading="lazy"
                    class="w-11 h-11 flex-shrink-0 rounded-full object-cover" />
                <div>
                    <p v-if="name" class="font-poppins text-sm text-brand-light tracking-widest">{{ name }}</p>
                    <p v-if="details" class="mt-1 text-sm text-brand-light">{{ details }}</p>
                </div>
            </figcaption>
        </figure>

        <template v-if="paragraphs.length">
            <button type="button" :aria-expanded="open" :aria-controls="bodyId" @click="open = !open"
                class="mt-4" :class="actionClasses">
                {{ open ? t('quote_block.collapse') : t('quote_block.expand') }}
                <ChevronDown aria-hidden="true"
                    class="w-5 h-5 flex-shrink-0 transition-transform duration-[250ms] motion-reduce:transition-none"
                    :class="{ 'rotate-180': open }" />
            </button>

            <!-- Unfolds by animating the row height from 0fr to 1fr, so the height needs no measuring -->
            <div :id="bodyId" :inert="!open"
                class="grid transition-[grid-template-rows,opacity] duration-[250ms] ease-out motion-reduce:transition-none"
                :class="open ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'">
                <!-- Room for the focus outline of the link, which the clipping would cut off; vertical padding goes on the
                     inner element, as padding here would stay visible when collapsed -->
                <div class="overflow-hidden -mx-1 px-1">
                    <div class="pt-4 pb-1 max-w-[68ch] space-y-4 text-[17px] leading-[1.75] text-brand-text">
                        <p v-for="(paragraph, index) in paragraphs" :key="index">{{ paragraph }}</p>
                        <p v-if="linkUrl">
                            <a :href="linkUrl" :class="actionClasses">{{ linkText }}&nbsp;→</a>
                        </p>
                    </div>
                </div>
            </div>
        </template>

        <a v-else-if="linkUrl" :href="linkUrl" class="mt-4" :class="actionClasses">{{ linkText }}&nbsp;→</a>
    </div>
</template>
