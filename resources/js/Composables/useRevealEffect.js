// resources/js/composables/useRevealEffect.js
import { ref, onMounted, onBeforeUnmount } from "vue";

export function useRevealEffect() {
    const rootRef = ref(null);
    const visible = ref(false);
    const prefersReducedMotion = ref(false);
    let observer = null;

    onMounted(() => {
        prefersReducedMotion.value = window.matchMedia(
            "(prefers-reduced-motion: reduce)",
        ).matches;

        if (prefersReducedMotion.value) {
            visible.value = true;
            return;
        }

        observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        visible.value = true;
                        observer.disconnect();
                    }
                });
            },
            { rootMargin: "0px 0px -10% 0px", threshold: 0 },
        );

        if (rootRef.value) observer.observe(rootRef.value);
    });

    onBeforeUnmount(() => {
        if (observer) observer.disconnect();
    });
    const reveal = (delay = 0) => ({
        style: {
            transitionDelay:
                visible.value && !prefersReducedMotion.value
                    ? `${delay}ms`
                    : "0ms",
        },
    });

    return {
        rootRef,
        visible,
        reveal,
    };
}
