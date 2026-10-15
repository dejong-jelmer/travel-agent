import { describe, it, expect, vi } from "vitest";
import { defineComponent, nextTick, ref } from "vue";
import { mount } from "@vue/test-utils";
import { moveItem, useSortable } from "@/Travel/useSortable.js";

// Sortable itself needs pointer events; the test drags by moving the element as Sortable does and calling onEnd.
const sortables = [];
vi.mock("sortablejs", () => ({
    default: {
        create: (element, options) => {
            const sortable = { element, options, destroy: vi.fn() };
            sortables.push(sortable);

            return sortable;
        },
    },
}));

const List = defineComponent({
    setup() {
        const items = ref([{ id: 1 }, { id: 2 }, { id: 3 }]);
        const list = ref(null);
        useSortable(list, ".handle", (from, to) => moveItem(items.value, from, to));

        return { items, list };
    },
    template: `<ol ref="list"><li v-for="item in items" :key="item.id" :data-id="item.id">{{ item.id }}</li></ol>`,
});

const drag = async (wrapper, oldIndex, newIndex) => {
    const { element, options } = sortables.at(-1);
    const item = element.children[oldIndex];
    const target = [...element.children].filter((child) => child !== item)[newIndex] ?? null;
    target ? target.before(item) : element.children[element.children.length - 1].after(item);

    options.onEnd({ item, from: element, oldIndex, newIndex });
    await nextTick();

    return [...wrapper.findAll("li")].map((li) => Number(li.attributes("data-id")));
};

describe("useSortable", () => {
    it.each([
        ["the last item to the top", 2, 0, [3, 1, 2]],
        ["the first item to the bottom", 0, 2, [2, 3, 1]],
        ["the middle item down", 1, 2, [1, 3, 2]],
        ["the last item up one", 2, 1, [1, 3, 2]],
    ])("moves %s in the list and lets Vue render it", async (_name, oldIndex, newIndex, order) => {
        const wrapper = mount(List, { attachTo: document.body });
        await nextTick();

        expect(await drag(wrapper, oldIndex, newIndex)).toEqual(order);
        expect(wrapper.vm.items.map((item) => item.id)).toEqual(order);

        // A second drag still lands right, so the DOM stayed in Vue's hands.
        expect(await drag(wrapper, 0, 2)).toEqual([...order.slice(1), order[0]]);

        wrapper.unmount();
    });

    it("drags by the handle it is given", async () => {
        const wrapper = mount(List);
        await nextTick();

        expect(sortables.at(-1).options.handle).toBe(".handle");
        wrapper.unmount();
        expect(sortables.at(-1).destroy).toHaveBeenCalled();
    });
});
