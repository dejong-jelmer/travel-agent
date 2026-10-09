import { describe, it, expect, beforeEach, afterEach, vi } from "vitest";
import { mount } from "@vue/test-utils";
import { h, ref, withDirectives } from "vue";

// Stands in for the browser's IntersectionObserver, so a test decides when an element comes into view
class MockIntersectionObserver {
    static instances = [];

    constructor(callback, options) {
        this.callback = callback;
        this.options = options;
        this.observe = vi.fn();
        this.unobserve = vi.fn();
        MockIntersectionObserver.instances.push(this);
    }

    enter(el) {
        this.callback([{ target: el, isIntersecting: true }]);
    }

    leave(el) {
        this.callback([{ target: el, isIntersecting: false }]);
    }
}

const observer = () => MockIntersectionObserver.instances[0];

let reveal;
let originalIntersectionObserver;

// Mounts a div with v-reveal; `classes` is a ref for a dynamic class binding on the same element
const mountWithReveal = (value, classes = ref('')) => mount({
    render: () => withDirectives(h('div', { class: classes.value }, 'Day 1'), [[reveal, value]]),
});

// Ends the transition of one property on the element, as the browser would
const endTransition = (el, propertyName, type = 'transitionend') => {
    const event = new Event(type);
    Object.defineProperty(event, 'propertyName', { value: propertyName });
    el.dispatchEvent(event);
};

const preferReducedMotion = (prefers) => {
    vi.spyOn(window, 'matchMedia').mockImplementation((query) => ({
        matches: prefers && query === '(prefers-reduced-motion: reduce)',
    }));
};

describe("v-reveal", () => {
    beforeEach(async () => {
        // The directive keeps one observer for all elements, so every test starts with a fresh module
        vi.resetModules();
        reveal = (await import("@/Directives/reveal.js")).default;

        originalIntersectionObserver = window.IntersectionObserver;
        window.IntersectionObserver = MockIntersectionObserver;
        MockIntersectionObserver.instances = [];

        document.documentElement.dataset.reveal = 'on';
        preferReducedMotion(false);
    });

    afterEach(() => {
        window.IntersectionObserver = originalIntersectionObserver;
        delete document.documentElement.dataset.reveal;
        vi.restoreAllMocks();
    });

    it("hides the element and observes it until it comes into view", () => {
        const el = mountWithReveal().element;

        expect(el.classList.contains('reveal')).toBe(true);
        expect(el.classList.contains('is-revealed')).toBe(false);
        expect(observer().observe).toHaveBeenCalledWith(el);
        expect(observer().options).toEqual({ threshold: 0, rootMargin: '0px 0px -10% 0px' });
    });

    it("reveals the element once it comes into view and stops observing it", () => {
        const el = mountWithReveal().element;

        observer().leave(el);
        expect(el.classList.contains('is-revealed')).toBe(false);

        observer().enter(el);
        expect(el.classList.contains('is-revealed')).toBe(true);
        expect(observer().unobserve).toHaveBeenCalledWith(el);
    });

    it("shares one observer between all elements", () => {
        mountWithReveal();
        mountWithReveal();

        expect(MockIntersectionObserver.instances).toHaveLength(1);
        expect(observer().observe).toHaveBeenCalledTimes(2);
    });

    it("stops observing an element that unmounts before it comes into view", () => {
        const wrapper = mountWithReveal();
        const el = wrapper.element;

        wrapper.unmount();

        expect(observer().unobserve).toHaveBeenCalledWith(el);
    });

    it("sets the delay as a custom property, at most 400 ms", () => {
        expect(mountWithReveal({ delay: 80 }).element.style.getPropertyValue('--reveal-delay')).toBe('80ms');
        expect(mountWithReveal({ delay: 1000 }).element.style.getPropertyValue('--reveal-delay')).toBe('400ms');
        expect(mountWithReveal().element.style.getPropertyValue('--reveal-delay')).toBe('0ms');
    });

    it("only fades an element in with the fade option", () => {
        expect(mountWithReveal({ fade: true }).element.classList.contains('reveal-fade')).toBe(true);
        expect(mountWithReveal().element.classList.contains('reveal-fade')).toBe(false);
    });

    it("keeps its classes when a class binding on the element changes", async () => {
        const classes = ref('rounded-2xl');
        const wrapper = mountWithReveal(undefined, classes);
        observer().enter(wrapper.element);

        classes.value = 'rounded-2xl shadow-lg';
        await wrapper.vm.$nextTick();

        expect(wrapper.element.classList.contains('shadow-lg')).toBe(true);
        expect(wrapper.element.classList.contains('reveal')).toBe(true);
        expect(wrapper.element.classList.contains('is-revealed')).toBe(true);
    });

    it("leaves the element visible and unobserved when the visitor prefers reduced motion", () => {
        preferReducedMotion(true);

        const el = mountWithReveal().element;

        expect(el.classList.contains('reveal')).toBe(false);
        expect(MockIntersectionObserver.instances).toHaveLength(0);
    });

    it("leaves the element visible and unobserved when the animations are switched off", () => {
        document.documentElement.dataset.reveal = 'off';

        const el = mountWithReveal().element;

        expect(el.classList.contains('reveal')).toBe(false);
        expect(MockIntersectionObserver.instances).toHaveLength(0);
    });

    it("leaves the element visible without IntersectionObserver support", () => {
        delete window.IntersectionObserver;

        const el = mountWithReveal().element;

        expect(el.classList.contains('reveal')).toBe(false);
    });

    it("removes its classes once the fade has ended, so the element's own transitions apply again", () => {
        const el = mountWithReveal({ delay: 80 }).element;
        observer().enter(el);

        endTransition(el, 'translate');
        expect(el.classList.contains('reveal')).toBe(true);

        endTransition(el, 'opacity');
        expect(el.classList.contains('reveal')).toBe(false);
        expect(el.classList.contains('is-revealed')).toBe(false);
        expect(el.style.getPropertyValue('--reveal-delay')).toBe('');
    });

    it("removes its classes when the fade is cancelled", () => {
        const el = mountWithReveal({ fade: true }).element;
        observer().enter(el);

        endTransition(el, 'opacity', 'transitioncancel');

        expect(el.classList.contains('reveal')).toBe(false);
        expect(el.classList.contains('reveal-fade')).toBe(false);
    });

    it("does not bring its classes back when the element updates after the fade", async () => {
        const classes = ref('rounded-2xl');
        const wrapper = mountWithReveal(undefined, classes);
        observer().enter(wrapper.element);
        endTransition(wrapper.element, 'opacity');

        classes.value = 'rounded-2xl shadow-lg';
        await wrapper.vm.$nextTick();

        expect(wrapper.element.classList.contains('reveal')).toBe(false);
    });
});
