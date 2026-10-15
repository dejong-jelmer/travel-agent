import { describe, it, expect } from "vitest";
import { mount } from "@vue/test-utils";
import LinesSection from "@/Travel/Library/LinesSection.vue";

const line = (infoLink, hasVariables = false) => ({
    id: 1,
    name: "Eurostar",
    mode: "train",
    color: null,
    info_link: infoLink,
    info_link_has_variables: hasVariables,
});

const mountSection = (transitLines) => mount(LinesSection, {
    props: { transitLines, transitModeOptions: [{ id: "train", name: "Trein" }] },
    global: {
        stubs: { LibraryCard: { template: "<div><slot /></div>" }, Modal: true, RowActions: true },
        directives: { tippy: {} },
        mocks: { route: (name, id) => `/${name}/${id}` },
    },
});

describe("LinesSection", () => {
    it("links to the information of a line", () => {
        const link = mountSection([line("https://www.eurostar.com")]).find("a");

        expect(link.attributes("href")).toBe("https://www.eurostar.com");
        expect(link.attributes("target")).toBe("_blank");
    });

    it("shows a link with variables without leading anywhere", () => {
        const template = "https://www.eurostar.com/nl-nl/reisinformatie/dienstregeling/{%leg_number%}?date={%journey_date%}";
        const wrapper = mountSection([line(template, true)]);

        expect(wrapper.find("a").exists()).toBe(false);
        expect(wrapper.find('[role="img"]').attributes("title")).toBe(template);
    });

    it("shows no icon for a line without a link", () => {
        const wrapper = mountSection([line(null)]);

        expect(wrapper.find("a").exists()).toBe(false);
        expect(wrapper.find('[role="img"]').exists()).toBe(false);
    });
});
