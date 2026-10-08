import { describe, it, expect, vi, afterEach } from "vitest";
import { mount } from "@vue/test-utils";
import PhotoSlider from "@/Components/Organisms/PhotoSlider.vue";
import i18n from '@/plugins/i18n.js';

const items = [{ id: 1 }, { id: 2 }, { id: 3 }, { id: 4 }, { id: 5 }];

const mountSlider = () => mount(PhotoSlider, {
    props: { items, label: 'Photos of Verona' },
    slots: {
        default: `<template #default="{ item, loaded }"><img :alt="'Photo ' + item.id" :data-loaded="loaded" /></template>`,
    },
});

// happy-dom does no layout, so give the track the dimensions a browser would and let it handle a scroll
const layout = async (wrapper, { scrollLeft = 0, scrollWidth, clientWidth, slideWidth = 200 }) => {
    const track = wrapper.find('[role="region"]').element;
    Object.defineProperties(track, {
        scrollLeft: { value: scrollLeft, configurable: true },
        scrollWidth: { value: scrollWidth, configurable: true },
        clientWidth: { value: clientWidth, configurable: true },
    });
    Object.defineProperty(track.firstElementChild, 'offsetWidth', { value: slideWidth, configurable: true });
    const getComputedStyle = window.getComputedStyle;
    vi.spyOn(window, 'getComputedStyle').mockImplementation(el => el === track ? { columnGap: '12px' } : getComputedStyle(el));
    track.scrollBy = vi.fn();
    await wrapper.find('[role="region"]').trigger('scroll');

    return track;
};

const previous = (wrapper) => wrapper.find(`button[aria-label="${i18n.global.t('photo_slider.previous')}"]`);
const next = (wrapper) => wrapper.find(`button[aria-label="${i18n.global.t('photo_slider.next')}"]`);

describe("PhotoSlider", () => {
    afterEach(() => vi.restoreAllMocks());

    it("renders a labelled scroll region with every photo", () => {
        const wrapper = mountSlider();

        expect(wrapper.find('[role="region"]').attributes('aria-label')).toBe('Photos of Verona');
        expect(wrapper.findAll('img').map(img => img.attributes('alt')))
            .toEqual(['Photo 1', 'Photo 2', 'Photo 3', 'Photo 4', 'Photo 5']);
    });

    it("hides the buttons and the progress bar when all photos fit", async () => {
        const wrapper = mountSlider();
        await layout(wrapper, { scrollWidth: 636, clientWidth: 636 });

        expect(wrapper.findAll('button')).toHaveLength(0);
        expect(wrapper.find('[aria-hidden="true"].rounded-full').exists()).toBe(false);
    });

    it("disables previous at the start and next at the end", async () => {
        const wrapper = mountSlider();
        await layout(wrapper, { scrollWidth: 1060, clientWidth: 636 });

        expect(previous(wrapper).attributes('aria-disabled')).toBe('true');
        expect(next(wrapper).attributes('aria-disabled')).toBe('false');

        await layout(wrapper, { scrollLeft: 424, scrollWidth: 1060, clientWidth: 636 });

        expect(previous(wrapper).attributes('aria-disabled')).toBe('false');
        expect(next(wrapper).attributes('aria-disabled')).toBe('true');
    });

    it("scrolls one slide per click and ignores a disabled button", async () => {
        const wrapper = mountSlider();
        const track = await layout(wrapper, { scrollWidth: 1060, clientWidth: 636 });

        await previous(wrapper).trigger('click');
        expect(track.scrollBy).not.toHaveBeenCalled();

        await next(wrapper).trigger('click');
        expect(track.scrollBy).toHaveBeenCalledWith({ left: 212 });
    });

    it("only loads the photos in view, and one ahead once scrolling starts", async () => {
        const wrapper = mountSlider();
        const loaded = () => wrapper.findAll('img').map(img => img.attributes('data-loaded'));

        await layout(wrapper, { scrollWidth: 1060, clientWidth: 636 });
        expect(loaded()).toEqual(['true', 'true', 'true', 'false', 'false']);

        await layout(wrapper, { scrollLeft: 50, scrollWidth: 1060, clientWidth: 636 });
        expect(loaded()).toEqual(['true', 'true', 'true', 'true', 'true']);
    });
});
