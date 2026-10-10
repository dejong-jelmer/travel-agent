import { describe, it, expect, vi, afterEach } from "vitest";
import { mount } from "@vue/test-utils";
import { nextTick } from "vue";
import HorizontalSlider from "@/Components/Organisms/HorizontalSlider.vue";
import i18n from '@/plugins/i18n.js';

const items = [{ id: 1 }, { id: 2 }, { id: 3 }, { id: 4 }, { id: 5 }];

const mountSlider = (props = {}, options = {}) => mount(HorizontalSlider, {
    props: { items, label: 'Photos of Verona', ...props },
    slots: {
        default: `<template #default="{ item, loaded }"><img :alt="'Photo ' + item.id" :data-loaded="loaded" /></template>`,
    },
    ...options,
});

// The slider measures in the next animation frame, after which the DOM follows
const nextFrame = async () => {
    await new Promise(resolve => requestAnimationFrame(resolve));
    await nextTick();
};

// The real one, also while layout() spies on it
const getComputedStyle = window.getComputedStyle;

// happy-dom does no layout, so give the track the dimensions a browser would and let it handle a scroll
const layout = async (wrapper, { scrollLeft = 0, scrollWidth, clientWidth, slideWidth = 200 }) => {
    const track = wrapper.find('[role="region"]').element;
    Object.defineProperties(track, {
        scrollLeft: { value: scrollLeft, configurable: true },
        scrollWidth: { value: scrollWidth, configurable: true },
        clientWidth: { value: clientWidth, configurable: true },
    });
    Object.defineProperty(track.firstElementChild, 'offsetWidth', { value: slideWidth, configurable: true });
    vi.spyOn(window, 'getComputedStyle').mockImplementation(el => el === track ? { columnGap: '12px' } : getComputedStyle(el));
    track.scrollBy = vi.fn();
    await wrapper.find('[role="region"]').trigger('scroll');
    await nextFrame();

    return track;
};

const previous = (wrapper) => wrapper.find(`button[aria-label="${i18n.global.t('slider.previous')}"]`);
const next = (wrapper) => wrapper.find(`button[aria-label="${i18n.global.t('slider.next')}"]`);

describe("HorizontalSlider", () => {
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

    it("hides previous at the start and next at the end", async () => {
        // happy-dom only computes styles, which isVisible() reads, for elements in the document
        const wrapper = mountSlider({}, { attachTo: document.body });
        await layout(wrapper, { scrollWidth: 1060, clientWidth: 636 });

        expect(previous(wrapper).isVisible()).toBe(false);
        expect(next(wrapper).isVisible()).toBe(true);

        await layout(wrapper, { scrollLeft: 212, scrollWidth: 1060, clientWidth: 636 });

        expect(previous(wrapper).isVisible()).toBe(true);
        expect(next(wrapper).isVisible()).toBe(true);

        await layout(wrapper, { scrollLeft: 424, scrollWidth: 1060, clientWidth: 636 });

        expect(previous(wrapper).isVisible()).toBe(true);
        expect(next(wrapper).isVisible()).toBe(false);

        wrapper.unmount();
    });

    it("hands the focus to the other button when the focused one hides", async () => {
        // Unlike happy-dom, browsers do not focus an element with display: none
        const focus = HTMLElement.prototype.focus;
        vi.spyOn(HTMLElement.prototype, 'focus').mockImplementation(function () {
            if (getComputedStyle(this).display !== 'none') focus.call(this);
        });
        const wrapper = mountSlider({}, { attachTo: document.body });
        await layout(wrapper, { scrollWidth: 848, clientWidth: 636 });

        // One click from the start reaches the end, so previous only shows up as next hides
        next(wrapper).element.focus();
        await layout(wrapper, { scrollLeft: 212, scrollWidth: 848, clientWidth: 636 });
        expect(document.activeElement).toBe(previous(wrapper).element);

        await layout(wrapper, { scrollWidth: 848, clientWidth: 636 });
        expect(document.activeElement).toBe(next(wrapper).element);

        wrapper.unmount();
    });

    it("updates the buttons once a photo has loaded", async () => {
        const wrapper = mountSlider({}, { attachTo: document.body });
        const track = await layout(wrapper, { scrollWidth: 636, clientWidth: 636 });
        expect(wrapper.findAll('button')).toHaveLength(0);

        Object.defineProperty(track, 'scrollWidth', { value: 1060, configurable: true });
        // A load event does not bubble, so the slider has to catch it on its way down
        wrapper.find('img').element.dispatchEvent(new Event('load'));
        await nextFrame();

        expect(next(wrapper).isVisible()).toBe(true);

        wrapper.unmount();
    });

    it("cancels a pending update on unmount", async () => {
        const wrapper = mountSlider();
        const requestFrame = vi.spyOn(window, 'requestAnimationFrame');
        const cancelFrame = vi.spyOn(window, 'cancelAnimationFrame');
        await wrapper.find('[role="region"]').trigger('scroll');

        wrapper.unmount();

        expect(cancelFrame).toHaveBeenCalledWith(requestFrame.mock.results[0].value);
    });

    it("scrolls one slide per click and ignores a click on a hidden button", async () => {
        const wrapper = mountSlider();
        const track = await layout(wrapper, { scrollWidth: 1060, clientWidth: 636 });

        await previous(wrapper).trigger('click');
        expect(track.scrollBy).not.toHaveBeenCalled();

        await next(wrapper).trigger('click');
        expect(track.scrollBy).toHaveBeenCalledWith({ left: 212 });
    });

    it("sizes the slides as three side by side with a 12px gap by default", () => {
        const track = mountSlider().find('[role="region"]').element;

        expect(track.style.gap).toBe('12px');
        expect(track.style.getPropertyValue('--slide-width-phone')).toBe('84%');
        expect(track.style.getPropertyValue('--slide-width-tablet')).toBe('calc((100% - 24px) / 3)');
        expect(track.style.getPropertyValue('--slide-width-laptop')).toBe('calc((100% - 24px) / 3)');
    });

    it("sizes the slides from the props, with the laptop count on tablets unless given", () => {
        const track = (props) => mountSlider(props).find('[role="region"]').element;

        const custom = track({ visible: 4, tabletVisible: 2, mobileWidth: '80%', gap: 24 });
        expect(custom.style.gap).toBe('24px');
        expect(custom.style.getPropertyValue('--slide-width-phone')).toBe('80%');
        expect(custom.style.getPropertyValue('--slide-width-tablet')).toBe('calc((100% - 24px) / 2)');
        expect(custom.style.getPropertyValue('--slide-width-laptop')).toBe('calc((100% - 72px) / 4)');

        expect(track({ visible: 2 }).style.getPropertyValue('--slide-width-tablet')).toBe('calc((100% - 12px) / 2)');
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
