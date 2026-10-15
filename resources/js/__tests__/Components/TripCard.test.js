import { describe, it, expect } from "vitest";
import { mount } from "@vue/test-utils";
import TripCard from "@/Components/Organisms/TripCard.vue";
import ResponsiveImage from "@/Components/Atoms/ResponsiveImage.vue";

const heroImage = {
    public_url: '/storage/images/verona.jpg',
    width: 4000,
    height: 2500,
    sources: [
        { url: '/storage/images/variants/verona-480.webp', width: 480, height: 300 },
        { url: '/storage/images/variants/verona-768.webp', width: 768, height: 480 },
    ],
    fallback_source: { url: '/storage/images/variants/verona-768.webp', width: 768, height: 480 },
};

const trip = (attributes = {}) => ({
    id: 1,
    slug: 'verona',
    name: 'Verona',
    destinations_formatted: 'Italië',
    subtitle: 'By night train to Verona',
    price_formatted: '€ 899',
    is_expected: false,
    duration: 8,
    travel_mode: { value: 'night_train', label: 'Night train' },
    hero_image: heroImage,
    ...attributes,
});

const mountCard = (props) => mount(TripCard, {
    props,
    global: {
        components: { ResponsiveImage },
        mocks: { route: (name, params) => `/reizen/${params.slug}` },
        stubs: {
            Link: { props: ['href'], template: '<a :href="href"><slot /></a>' },
            PriceBadge: true,
        },
    },
});

describe("TripCard", () => {
    it("shows the WebP variants of the hero image instead of the original upload", () => {
        const img = mountCard({ trip: trip() }).find('img');

        expect(img.attributes('srcset')).toBe('/storage/images/variants/verona-480.webp 480w, /storage/images/variants/verona-768.webp 768w');
        expect(img.attributes('sizes')).toBe('(min-width: 900px) 420px, (min-width: 600px) 50vw, 100vw');
        expect(img.attributes('src')).toBe('/storage/images/variants/verona-768.webp');
        expect(img.attributes('alt')).toBe('Verona');
    });

    it("loads the image lazily, unless it is asked to load it at once", () => {
        expect(mountCard({ trip: trip() }).find('img').attributes('loading')).toBe('lazy');
        expect(mountCard({ trip: trip(), loading: 'eager' }).find('img').attributes('loading')).toBe('eager');
    });

    it("shows the placeholder for a trip without a hero image", () => {
        const img = mountCard({ trip: trip({ hero_image: null }) }).find('img');

        expect(img.attributes('src')).toContain('placeholder');
        expect(img.attributes('srcset')).toBeUndefined();
        expect(img.attributes('loading')).toBe('lazy');
    });
});
