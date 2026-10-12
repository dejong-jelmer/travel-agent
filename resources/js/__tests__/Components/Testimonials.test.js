import { describe, it, expect } from "vitest";
import { mount } from "@vue/test-utils";
import HomeTestimonials from "@/Components/Organisms/HomeTestimonials.vue";
import TripTestimonials from "@/Components/Organisms/TripTestimonials.vue";
import TestimonialSlider from "@/Components/Organisms/TestimonialSlider.vue";
import HorizontalSlider from "@/Components/Organisms/HorizontalSlider.vue";
import QuoteBlock from "@/Components/Molecules/QuoteBlock.vue";
import BaseCard from "@/Components/Molecules/BaseCard.vue";
import ResponsiveImage from "@/Components/Atoms/ResponsiveImage.vue";
import { quoteProps } from "@/Support/testimonials.js";

const testimonial = (id, overrides = {}) => ({
    id,
    quote: `Citaat ${id}`,
    paragraphs: [],
    author_name: `Schrijver ${id}`,
    author_context: null,
    travelled_in: null,
    photo: null,
    link_url: null,
    link_label: null,
    ...overrides,
});

const mountWith = (component, testimonials) => mount(component, {
    props: { testimonials },
    global: {
        components: { TestimonialSlider, HorizontalSlider, QuoteBlock, BaseCard, ResponsiveImage },
        directives: { reveal: {} },
    },
});

describe("quoteProps", () => {
    it("maps a testimonial onto the QuoteBlock props", () => {
        expect(quoteProps(testimonial(1, {
            paragraphs: ['Alinea.'],
            author_context: 'Mijn vader',
            travelled_in: 'mei 2024',
            link_url: 'https://omdatwereizen.nl',
            link_label: 'Lees verder',
        }))).toEqual({
            quote: 'Citaat 1',
            name: 'Schrijver 1',
            context: 'Mijn vader',
            date: 'mei 2024',
            photo: null,
            paragraphs: ['Alinea.'],
            linkUrl: 'https://omdatwereizen.nl',
            linkLabel: 'Lees verder',
        });
    });
});

describe("HomeTestimonials", () => {
    it("shows a single testimonial as a large quote, without a card or slider", () => {
        const wrapper = mountWith(HomeTestimonials, [testimonial(1, { author_context: 'Mijn vader' })]);

        expect(wrapper.find('[role="region"]').exists()).toBe(false);
        expect(wrapper.find('article').exists()).toBe(false);
        expect(wrapper.find('blockquote p').classes()).toContain('laptop:text-3xl');
        expect(wrapper.findAll('figcaption p').map(p => p.text())).toEqual(['Schrijver 1', 'Mijn vader']);
    });

    it("puts more testimonials in a slider, a card each", () => {
        const wrapper = mountWith(HomeTestimonials, [testimonial(1), testimonial(2), testimonial(3)]);

        expect(wrapper.find('[role="region"]').exists()).toBe(true);
        expect(wrapper.findAll('[role="region"] article blockquote').map(quote => quote.text()))
            .toEqual(['Citaat 1', 'Citaat 2', 'Citaat 3']);
        expect(wrapper.find('blockquote p').classes()).toContain('laptop:text-3xl');
    });
});

describe("TripTestimonials", () => {
    it("shows nothing without testimonials", () => {
        const wrapper = mountWith(TripTestimonials, []);

        expect(wrapper.find('section').exists()).toBe(false);
        expect(wrapper.find('blockquote').exists()).toBe(false);
    });

    it("shows a single testimonial as a medium quote in a card", () => {
        const wrapper = mountWith(TripTestimonials, [testimonial(1)]);

        expect(wrapper.find('[role="region"]').exists()).toBe(false);
        expect(wrapper.find('article blockquote').text()).toBe('Citaat 1');
        expect(wrapper.find('blockquote p').classes()).toContain('tablet:text-[26px]');
    });

    it("puts more testimonials in a slider, a card each", () => {
        const wrapper = mountWith(TripTestimonials, [testimonial(1), testimonial(2)]);

        expect(wrapper.findAll('[role="region"] article')).toHaveLength(2);
        expect(wrapper.find('blockquote p').classes()).toContain('tablet:text-[26px]');
    });
});
