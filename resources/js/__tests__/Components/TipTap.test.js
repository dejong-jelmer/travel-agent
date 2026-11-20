import { describe, it, expect } from "vitest";
import { mount, flushPromises } from "@vue/test-utils";
import TipTap from "@/Components/Organisms/TipTap.vue";

// Content as it arrives when text is pasted from another editor: a bold and italic heading, and a link whose title
// repeats its URL
const PASTED = '<h2><strong>Langs de <em>Vézère</em></strong></h2>'
    + '<p>Lees <a href="https://www.lascaux.fr" title="https://www.lascaux.fr">de site</a> en <strong>boek op tijd</strong>.</p>';

const mountEditor = async (content) => {
    const wrapper = mount(TipTap, {
        props: { modelValue: content, name: 'body' },
        global: { stubs: { Label: true, FormFeedback: true } },
        attachTo: document.body,
    });
    await flushPromises();
    return wrapper;
};

describe("TipTap", () => {
    it("keeps the headings plain text", async () => {
        const wrapper = await mountEditor(PASTED);
        const heading = wrapper.find('.ProseMirror h2');

        expect(heading.text()).toBe('Langs de Vézère');
        expect(heading.element.innerHTML).toBe('Langs de Vézère');
        wrapper.unmount();
    });

    it("leaves the title off links but keeps their url", async () => {
        const wrapper = await mountEditor(PASTED);
        const link = wrapper.find('.ProseMirror a');

        expect(link.attributes('href')).toBe('https://www.lascaux.fr');
        expect(link.attributes('title')).toBeUndefined();
        wrapper.unmount();
    });

    it("still allows bold text outside headings", async () => {
        const wrapper = await mountEditor(PASTED);

        expect(wrapper.find('.ProseMirror p strong').text()).toBe('boek op tijd');
        wrapper.unmount();
    });
});
