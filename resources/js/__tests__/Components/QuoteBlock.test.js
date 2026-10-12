import { describe, it, expect } from "vitest";
import { mount } from "@vue/test-utils";
import QuoteBlock from "@/Components/Molecules/QuoteBlock.vue";
import ResponsiveImage from "@/Components/Atoms/ResponsiveImage.vue";
import i18n from '@/plugins/i18n.js';

const t = (key) => i18n.global.t(key);

const mountQuote = (props = {}) => mount(QuoteBlock, {
    props: { quote: 'De grotten waren ouder dan alles wat ik ooit zag.', ...props },
    global: { components: { ResponsiveImage } },
});

const toggle = (wrapper) => wrapper.find('button[aria-controls]');
const panel = (wrapper) => wrapper.find(`#${toggle(wrapper).attributes('aria-controls')}`);

describe("QuoteBlock", () => {
    it("renders the quote as a figure with the author below it", () => {
        const wrapper = mountQuote({ name: 'Henk', context: 'Mijn vader', date: 'mei 2024' });

        expect(wrapper.find('figure blockquote').text()).toBe('De grotten waren ouder dan alles wat ik ooit zag.');
        expect(wrapper.find('figure > svg').attributes('aria-hidden')).toBe('true');
        expect(wrapper.findAll('figcaption p').map(p => p.text())).toEqual(['Henk', 'Mijn vader · mei 2024']);
    });

    it("shows the context or the date alone", () => {
        expect(mountQuote({ name: 'Henk', date: 'mei 2024' }).findAll('figcaption p').map(p => p.text()))
            .toEqual(['Henk', 'mei 2024']);
        expect(mountQuote({ name: 'Henk', context: 'Mijn vader' }).findAll('figcaption p').map(p => p.text()))
            .toEqual(['Henk', 'Mijn vader']);
    });

    it("sizes the quote", () => {
        expect(mountQuote().find('blockquote p').classes()).toEqual(expect.arrayContaining(['text-2xl', 'laptop:text-3xl']));
        expect(mountQuote({ size: 'medium' }).find('blockquote p').classes())
            .toEqual(expect.arrayContaining(['text-[21px]', 'tablet:text-[26px]']));
    });

    it("shows the photo without alt text of its own, as the name is next to it", () => {
        const photo = { public_url: '/storage/images/henk.jpg', width: 400, height: 400, sources: [], fallback_source: null };
        const img = mountQuote({ name: 'Henk', photo }).find('figcaption img');

        expect(img.attributes('alt')).toBe('');
        expect(img.attributes('src')).toBe('/storage/images/henk.jpg');
    });

    it("has nothing to unfold without a full account", () => {
        const wrapper = mountQuote({ name: 'Henk' });

        expect(wrapper.find('button').exists()).toBe(false);
        expect(wrapper.find('a').exists()).toBe(false);
    });

    it("links to the longer story right below the quote without a full account", () => {
        const wrapper = mountQuote({ linkUrl: 'https://omdatwereizen.nl/mijn-verhalen/vezere' });
        const link = wrapper.find('a');

        expect(link.attributes('href')).toBe('https://omdatwereizen.nl/mijn-verhalen/vezere');
        expect(link.text()).toBe(`${t('quote_block.read_story')} →`);
        expect(mountQuote({ linkUrl: 'https://omdatwereizen.nl', linkLabel: 'Lees zijn verhaal' }).find('a').text())
            .toBe('Lees zijn verhaal →');
    });

    it("keeps the collapsed account in the page, out of reach until it unfolds", async () => {
        const wrapper = mountQuote({ paragraphs: ['Eerste alinea.', 'Tweede alinea.'] });

        expect(toggle(wrapper).text()).toBe(t('quote_block.expand'));
        expect(toggle(wrapper).attributes('aria-expanded')).toBe('false');
        expect(panel(wrapper).findAll('p').map(p => p.text())).toEqual(['Eerste alinea.', 'Tweede alinea.']);
        expect(panel(wrapper).element.inert).toBe(true);

        await toggle(wrapper).trigger('click');

        expect(toggle(wrapper).text()).toBe(t('quote_block.collapse'));
        expect(toggle(wrapper).attributes('aria-expanded')).toBe('true');
        expect(panel(wrapper).element.inert).toBe(false);

        await toggle(wrapper).trigger('click');

        expect(toggle(wrapper).attributes('aria-expanded')).toBe('false');
        expect(panel(wrapper).element.inert).toBe(true);
    });

    it("puts the link at the end of the full account, leaving one action below the quote", () => {
        const wrapper = mountQuote({ paragraphs: ['Eerste alinea.'], linkUrl: 'https://omdatwereizen.nl/mijn-verhalen/vezere' });

        expect(wrapper.findAll('a')).toHaveLength(1);
        expect(panel(wrapper).find('a').attributes('href')).toBe('https://omdatwereizen.nl/mijn-verhalen/vezere');
    });

    it("gives every account its own panel id", () => {
        const wrapper = mount({
            components: { QuoteBlock },
            template: `<div><QuoteBlock quote="Een" :paragraphs="['A']" /><QuoteBlock quote="Twee" :paragraphs="['B']" /></div>`,
        });
        const ids = wrapper.findAll('button[aria-controls]').map(button => button.attributes('aria-controls'));

        expect(new Set(ids).size).toBe(2);
    });
});
