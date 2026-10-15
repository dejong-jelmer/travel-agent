import { describe, it, expect, vi, beforeEach, afterEach } from "vitest";
import { mount } from "@vue/test-utils";
import HomeHero from "@/Components/Organisms/HomeHero.vue";

// Answers the media queries of the hero as a screen of the given kind would
const useScreen = ({ phone = false, reducedMotion = false } = {}) => {
    vi.spyOn(window, 'matchMedia').mockImplementation((query) => ({
        matches: {
            '(prefers-reduced-motion: reduce)': reducedMotion,
            '(min-width: 600px)': !phone,
        }[query] ?? false,
    }));
};

const setReadyState = (state) => vi.spyOn(document, 'readyState', 'get').mockReturnValue(state);

const mountHero = () => mount(HomeHero, {
    global: { stubs: { ScrollIndicator: true } },
});

const video = (wrapper) => wrapper.find('video').element;

describe("HomeHero", () => {
    beforeEach(() => {
        useScreen();
        setReadyState('loading');
    });

    afterEach(() => {
        vi.restoreAllMocks();
        delete navigator.connection;
    });

    it("shows the poster as a picture, with the center crop on portrait screens", () => {
        const wrapper = mountHero();

        expect(wrapper.find('picture source').attributes('media')).toBe('(max-aspect-ratio: 3/5)');
        expect(wrapper.find('picture source').attributes('srcset')).toContain('hero-poster-portrait');
        expect(wrapper.find('picture img').attributes('fetchpriority')).toBe('high');
        expect(wrapper.find('video').attributes('poster')).toBeUndefined();
    });

    it("renders the video without a source, so the download does not start with the page", () => {
        const wrapper = mountHero();

        expect(wrapper.find('video').attributes('src')).toBeUndefined();
        expect(wrapper.find('video').attributes('autoplay')).toBeUndefined();
    });

    it("starts the video at 0.8 times its speed once the page has loaded", () => {
        const wrapper = mountHero();
        expect(video(wrapper).src).toBeFalsy();

        window.dispatchEvent(new Event('load'));

        expect(video(wrapper).src).toContain('home-hero');
        expect(video(wrapper).autoplay).toBe(true);
        expect(video(wrapper).playbackRate).toBe(0.8);
        expect(video(wrapper).defaultPlaybackRate).toBe(0.8);
    });

    it("starts the video at once when the page has already loaded, as after an Inertia visit", () => {
        setReadyState('complete');

        const wrapper = mountHero();

        expect(video(wrapper).src).toContain('home-hero');
    });

    it("keeps the poster on phones", () => {
        useScreen({ phone: true });
        setReadyState('complete');

        const wrapper = mountHero();

        expect(video(wrapper).src).toBeFalsy();
    });

    it("keeps the poster when the visitor asks to save data", () => {
        Object.defineProperty(navigator, 'connection', { value: { saveData: true }, configurable: true });
        setReadyState('complete');

        const wrapper = mountHero();

        expect(video(wrapper).src).toBeFalsy();
    });

    it("keeps the poster when the visitor prefers reduced motion", () => {
        useScreen({ reducedMotion: true });
        setReadyState('complete');

        const wrapper = mountHero();

        expect(video(wrapper).src).toBeFalsy();
    });

    it("stops waiting for the load event when it unmounts", () => {
        const removeEventListener = vi.spyOn(window, 'removeEventListener');
        const wrapper = mountHero();

        wrapper.unmount();

        expect(removeEventListener).toHaveBeenCalledWith('load', expect.any(Function));
    });
});
