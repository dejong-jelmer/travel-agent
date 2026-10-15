import { describe, it, expect } from "vitest";
import { mount } from "@vue/test-utils";
import CoordinateFields from "@/Travel/Library/CoordinateFields.vue";
import { coordinatePair, matches } from "@/Travel/Library/search.js";
import FormFeedback from "@/Components/Atoms/FormFeedback.vue";
import Label from "@/Components/Atoms/Label.vue";
import Input from "@/Components/Molecules/Input.vue";

describe("searching the library", () => {
    it("matches any of the values, ignoring case", () => {
        expect(matches("saint", "Bordeaux Saint-Jean", "Bordeaux")).toBe(true);
        expect(matches("BORDEAUX", "Gare Saint-Jean", "Bordeaux")).toBe(true);
        expect(matches("lyon", "Bordeaux Saint-Jean", null)).toBe(false);
    });

    it("matches everything with an empty search", () => {
        expect(matches("  ", "Bordeaux")).toBe(true);
    });
});

describe("coordinates", () => {
    it("splits a pair as a map copies it", () => {
        expect(coordinatePair("44.8249878, -0.5602157")).toEqual({ latitude: "44.8249878", longitude: "-0.5602157" });
        expect(coordinatePair("44.8249878,-0.5602157")).toEqual({ latitude: "44.8249878", longitude: "-0.5602157" });
    });

    it("leaves a single value alone, also one with a decimal comma", () => {
        expect(coordinatePair("44.8249878")).toBe(null);
        expect(coordinatePair("44,8249878")).toBe(null);
    });

    // Mounted as v-model does: each update comes back as the prop.
    const mountFields = () => {
        const wrapper = mount(CoordinateFields, {
            props: {
                latitude: "",
                longitude: "",
                "onUpdate:latitude": (value) => wrapper.setProps({ latitude: value }),
                "onUpdate:longitude": (value) => wrapper.setProps({ longitude: value }),
            },
            global: { components: { FormFeedback, Input, Label } },
        });

        return wrapper;
    };

    it("fills both fields from a pair pasted into either", async () => {
        const wrapper = mountFields();

        await wrapper.find("#longitude").setValue("44.8249878, -0.5602157");

        expect(wrapper.find("#latitude").element.value).toBe("44.8249878");
        expect(wrapper.find("#longitude").element.value).toBe("-0.5602157");
    });

    it("takes a single value as it is", async () => {
        const wrapper = mountFields();

        await wrapper.find("#latitude").setValue("44.82");

        expect(wrapper.emitted("update:latitude")).toEqual([["44.82"]]);
        expect(wrapper.emitted("update:longitude")).toBeUndefined();
    });
});
