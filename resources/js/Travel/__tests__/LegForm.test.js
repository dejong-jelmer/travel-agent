import { describe, it, expect } from "vitest";
import { nextTick } from "vue";
import { mount } from "@vue/test-utils";
import LegForm from "@/Travel/LegForm.vue";
import Button from "@/Components/Atoms/Button.vue";
import FormFeedback from "@/Components/Atoms/FormFeedback.vue";
import Label from "@/Components/Atoms/Label.vue";
import Input from "@/Components/Molecules/Input.vue";
import Select from "@/Components/Molecules/Select.vue";
import TextArea from "@/Components/Molecules/TextArea.vue";

const legTypes = [
    { id: "train", name: "Trein", has_lines: true },
    { id: "tram", name: "Tram", has_lines: true },
    { id: "transfer", name: "Overstap", has_lines: false },
];
const stations = [
    { id: 2, name: "Nijmegen", city: "Nijmegen" },
    { id: 3, name: "Utrecht Centraal", city: "Utrecht" },
];
const transitLines = [
    { id: 5, name: "NS Intercity", mode: "train", color: null },
    { id: 7, name: "Tram C", mode: "tram", color: null },
];
const train = {
    id: 4,
    type: "train",
    from_station: stations[0],
    to_station: stations[1],
    departure_at: "2026-08-11T05:43",
    arrival_at: "2026-08-11T06:37",
    duration_minutes: null,
    number: null,
    carriage: null,
    seat: null,
    note: null,
    transit_lines: [{ id: 5, name: "NS Intercity", direction: "Den Helder" }],
};

const mountForm = (props) => mount(LegForm, {
    props: { owner: { type: "journey", id: 1 }, legTypes, stations, transitLines, ...props },
    global: {
        components: { Button, FormFeedback, Input, Label, Select, TextArea },
        mocks: { route: () => "#" },
    },
});

const labels = (wrapper) => wrapper.findAll("label").map((label) => label.text().replace("*", ""));

describe("LegForm", () => {
    it("shows the stations, times, ticket and lines of a train", () => {
        const wrapper = mountForm({ leg: train, timed: true, after: "2026-08-11" });

        expect(labels(wrapper)).toEqual(["Soort", "Van", "Naar", "Vertrek", "Aankomst", "Treinnummer", "Rijtuig", "Stoel", "Opmerking"]);
        expect(wrapper.text()).toContain("Lijnen");
        expect(wrapper.find('#direction_0').element.value).toBe("Den Helder");
    });

    it("derives the duration from both times instead of asking for it", () => {
        const wrapper = mountForm({ leg: train, timed: true, after: "2026-08-11" });

        expect(wrapper.find('#duration_minutes').exists()).toBe(false);
        expect(wrapper.text()).toContain("di 11 aug 05:43 → di 11 aug 06:37 · 54 min");
    });

    it("asks a transfer for one station and its duration", async () => {
        const wrapper = mountForm({ leg: null, defaults: { type: "transfer", from_station_id: 3 }, timed: true, after: "2026-08-11T06:37" });

        expect(labels(wrapper)).toEqual(["Soort", "Station", "Vertrek", "Aankomst", "Duur in minuten", "Opmerking"]);
        expect(wrapper.find('#station').element.value).toBe("Utrecht Centraal");
        expect(wrapper.text()).not.toContain("Lijnen");
    });

    it("gives an arrival route no times", () => {
        const wrapper = mountForm({ leg: null, defaults: { type: "tram" }, timed: false, after: null });

        expect(labels(wrapper)).not.toContain("Vertrek");
        expect(labels(wrapper)).toContain("Duur in minuten");
    });

    it("asks for a date on the journey before times can be filled in", () => {
        const wrapper = mountForm({ leg: null, timed: true, after: null });

        expect(wrapper.find('#departure_time').attributes("disabled")).toBeDefined();
        expect(wrapper.text()).toContain("Geef de reis eerst een datum");
    });

    it("drops the lines of another mode when the type changes", async () => {
        const wrapper = mountForm({ leg: train, timed: true, after: "2026-08-11" });

        await wrapper.find("select").setValue("tram");
        await nextTick();

        expect(wrapper.find('#direction_0').exists()).toBe(false);
        expect(wrapper.text()).toContain("Lijn toevoegen");
    });
});
