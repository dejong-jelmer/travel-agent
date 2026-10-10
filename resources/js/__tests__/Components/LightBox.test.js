import { describe, it, expect, vi, beforeEach, afterEach } from "vitest";
import { mount } from "@vue/test-utils";
import LightBox from "@/Components/Molecules/LightBox.vue";
import i18n from '@/plugins/i18n.js';

const instances = vi.hoisted(() => []);

vi.mock('photoswipe/lightbox', () => ({
    default: vi.fn(function (options) {
        this.options = options;
        this.loadAndOpen = vi.fn();
        this.destroy = vi.fn();
        instances.push(this);
    }),
}));

const withVariants = {
    public_url: '/storage/images/arena.jpg',
    width: 4000,
    height: 3000,
    sources: [
        { url: '/storage/images/arena-640.webp', width: 640, height: 480 },
        { url: '/storage/images/arena-1920.webp', width: 1920, height: 1440 },
    ],
};
const withoutVariants = { public_url: '/storage/images/tower.jpg', width: 800, height: 1200, sources: [] };

// A thumbnail inside a horizontally scrolling container, at the given position within it. happy-dom does no
// layout and leaves overflow-x empty instead of 'visible', so the container is the only one that clips.
const thumbnail = (alt, left) => {
    const container = document.createElement('div');
    container.dataset.clips = '';
    container.getBoundingClientRect = () => ({ left: 0, right: 600 });
    const button = document.createElement('button');
    button.innerHTML = `<img alt="${alt}" src="/thumb.webp">`;
    button.getBoundingClientRect = () => ({ left, right: left + 200 });
    container.appendChild(button);
    document.body.appendChild(container);

    return button;
};

const open = (index, thumbnails) => {
    mount(LightBox, { props: { images: [withVariants, withoutVariants] } }).vm.open(index, thumbnails);

    return instances.at(-1);
};

describe("LightBox", () => {
    beforeEach(() => {
        instances.splice(0);
        vi.spyOn(window, 'getComputedStyle').mockImplementation(el => ({ overflowX: 'clips' in el.dataset ? 'auto' : 'visible' }));
    });
    afterEach(() => vi.restoreAllMocks());

    it("opens PhotoSwipe at the given image with translated controls", () => {
        const lightbox = open(1);

        expect(lightbox.loadAndOpen).toHaveBeenCalledWith(1);
        expect(lightbox.options).toMatchObject({
            closeTitle: i18n.global.t('lightbox.close'),
            arrowPrevTitle: i18n.global.t('lightbox.previous'),
            arrowNextTitle: i18n.global.t('lightbox.next'),
        });
    });

    it("closes on a click next to the image", () => {
        expect(open(0).options.bgClickAction).toBeUndefined();
    });

    it("destroys the previous lightbox when opening again", () => {
        const wrapper = mount(LightBox, { props: { images: [withVariants] } });
        wrapper.vm.open(0);
        wrapper.vm.open(0);

        expect(instances[0].destroy).toHaveBeenCalled();
        expect(instances[1].destroy).not.toHaveBeenCalled();
        expect(instances[1].loadAndOpen).toHaveBeenCalledWith(0);
    });

    it("offers every variant as srcset, sized to the widest one", () => {
        const [slide] = open(0).options.dataSource;

        expect(slide).toMatchObject({
            src: '/storage/images/arena-1920.webp',
            srcset: '/storage/images/arena-640.webp 640w, /storage/images/arena-1920.webp 1920w',
            width: 1920,
            height: 1440,
        });
    });

    it("shows the original upload of an image without variants", () => {
        const [, slide] = open(0).options.dataSource;

        expect(slide).toMatchObject({ src: '/storage/images/tower.jpg', srcset: undefined, width: 800, height: 1200 });
    });

    it("takes the alt text and placeholder from the thumbnails, and zooms only from those in view", () => {
        const inView = thumbnail('Photo 1 of Verona', 0);
        const scrolledOut = thumbnail('Photo 2 of Verona', 650);
        const [first, second] = open(0, [inView, scrolledOut]).options.dataSource;

        expect(first).toMatchObject({ alt: 'Photo 1 of Verona', element: inView, thumbCropped: true });
        expect(first.msrc).toContain('/thumb.webp');
        expect(second).toMatchObject({ alt: 'Photo 2 of Verona', element: undefined });
    });
});
