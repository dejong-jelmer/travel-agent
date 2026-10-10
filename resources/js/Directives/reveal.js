/**
 * v-reveal: fades an element in and moves it up a little once it scrolls into view. Every element animates once, not
 * again when scrolling back.
 *
 * Usage: `v-reveal`, `v-reveal="{ delay: 80 }"` (in ms, at most 400) or `v-reveal="{ fade: true }"` (a fade only,
 * without the movement). The start state and the transition live in app.css (.reveal, .reveal-fade, .is-revealed).
 * Once the fade has ended the directive removes its classes again, so the element's own transitions (a hover lift,
 * for instance) apply as before.
 *
 * The directive leaves an element alone when the animations are switched off (data-reveal on <html>, from
 * config('app.reveal_animations')), when the visitor prefers reduced motion or when the browser has no
 * IntersectionObserver. As the hidden start state hangs on the .reveal class set here, everything stays visible
 * without JavaScript as well.
 */

const MAX_DELAY = 400;

// One observer for all elements, created on first use. The bottom margin waits until an element is a little into
// view; threshold 0, as an element taller than the viewport might never reach a larger share of it.
let observer = null;

// The options of every element the directive animates, until its fade has ended
const elements = new WeakMap();

// The elements that have come into view
const revealed = new WeakSet();

const isEnabled = () => document.documentElement.dataset.reveal === 'on'
    && !window.matchMedia('(prefers-reduced-motion: reduce)').matches
    && 'IntersectionObserver' in window;

// Steps aside once the fade has ended (or was cancelled), leaving the element as if the directive was never there
const finish = (el) => {
    el.classList.remove('reveal', 'reveal-fade', 'is-revealed');
    el.style.removeProperty('--reveal-delay');
    elements.delete(el);
};

const show = (el) => {
    revealed.add(el);
    el.classList.add('is-revealed');

    const onEnd = (event) => {
        if (event.target !== el || event.propertyName !== 'opacity') {
            return;
        }

        el.removeEventListener('transitionend', onEnd);
        el.removeEventListener('transitioncancel', onEnd);
        finish(el);
    };

    el.addEventListener('transitionend', onEnd);
    el.addEventListener('transitioncancel', onEnd);
};

const getObserver = () => {
    observer ??= new window.IntersectionObserver((entries) => {
        for (const entry of entries) {
            if (entry.isIntersecting) {
                show(entry.target);
                observer.unobserve(entry.target);
            }
        }
    }, { threshold: 0, rootMargin: '0px 0px -10% 0px' });

    return observer;
};

// Also called after every update: Vue overwrites the whole class and style attributes when a dynamic binding on the
// element changes, which drops what was set here
const apply = (el) => {
    const { delay = 0, fade = false } = elements.get(el);

    el.classList.add('reveal');
    el.classList.toggle('reveal-fade', fade);
    el.classList.toggle('is-revealed', revealed.has(el));
    el.style.setProperty('--reveal-delay', `${Math.min(Math.max(Number(delay) || 0, 0), MAX_DELAY)}ms`);
};

export default {
    beforeMount(el, { value }) {
        if (!isEnabled()) {
            return;
        }

        elements.set(el, value ?? {});
        apply(el);
    },

    mounted(el) {
        if (elements.has(el)) {
            getObserver().observe(el);
        }
    },

    updated(el, { value }) {
        if (elements.has(el)) {
            elements.set(el, value ?? {});
            apply(el);
        }
    },

    unmounted(el) {
        if (elements.has(el)) {
            observer?.unobserve(el);
            elements.delete(el);
        }
    },
};
