import { describe, it, expect, vi, afterEach } from "vitest";
import { mount } from "@vue/test-utils";
import FocusPointPicker from "@/Components/Molecules/FocusPointPicker.vue";

const options = ['0% 0%', '50% 0%', '100% 0%', '0% 50%', '50% 50%', '100% 50%', '0% 100%', '50% 100%', '100% 100%']
    .map((id) => ({ id, name: `Point ${id}` }));

const mountPicker = (props = {}) => mount(FocusPointPicker, {
    props: { image: 'https://example.test/hero.jpg', options, label: 'Focal point', modelValue: null, ...props },
});

const pressed = (wrapper) => wrapper.findAll('button[aria-pressed="true"]').map((button) => button.attributes('aria-label'));

describe("FocusPointPicker", () => {
    afterEach(() => {
        vi.restoreAllMocks();
        vi.unstubAllGlobals();
    });

    it("renders a labelled button for every point of the grid", () => {
        const wrapper = mountPicker();

        expect(wrapper.find('[role="group"]').attributes('aria-label')).toBe('Focal point');
        expect(wrapper.findAll('button')).toHaveLength(9);
    });

    it("marks the centre as chosen without a value", () => {
        expect(pressed(mountPicker())).toEqual(['Point 50% 50%']);
    });

    it("marks the stored point as chosen", () => {
        expect(pressed(mountPicker({ modelValue: '100% 0%' }))).toEqual(['Point 100% 0%']);
    });

    it("emits the point that is clicked", async () => {
        const wrapper = mountPicker();

        await wrapper.findAll('button')[6].trigger('click');

        expect(wrapper.emitted('update:modelValue')).toEqual([['0% 100%']]);
    });

    it("shows the saved image by its URL", () => {
        expect(mountPicker().find('img').attributes('src')).toBe('https://example.test/hero.jpg');
    });

    it("shows a newly selected file through an object URL that is revoked on unmount", () => {
        const createObjectURL = vi.fn(() => 'blob:hero');
        const revokeObjectURL = vi.fn();
        vi.stubGlobal('URL', { ...URL, createObjectURL, revokeObjectURL });

        const file = new File(['hero'], 'hero.jpg', { type: 'image/jpeg' });
        const wrapper = mountPicker({ image: file });

        expect(createObjectURL).toHaveBeenCalledWith(file);
        expect(wrapper.find('img').attributes('src')).toBe('blob:hero');

        wrapper.unmount();

        expect(revokeObjectURL).toHaveBeenCalledWith('blob:hero');
    });
});
