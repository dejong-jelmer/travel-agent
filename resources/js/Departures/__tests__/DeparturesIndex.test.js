import { describe, it, expect, vi } from "vitest";
import { mount } from "@vue/test-utils";

vi.mock("@inertiajs/vue3", () => ({
    usePage: () => ({ props: { locale: "nl" } }),
    useForm: (data) => ({ ...data, errors: {}, processing: false, post: vi.fn(), put: vi.fn() }),
    router: { post: vi.fn(), delete: vi.fn() },
    Link: { props: ["href"], template: '<a :href="href"><slot /></a>' },
}));

import Index from "@/Pages/Admin/Trip/Departures/Index.vue";
import Pill from "@/Components/Atoms/Pill.vue";
import Button from "@/Components/Atoms/Button.vue";

const departure = (attributes = {}) => ({
    id: 7,
    departs_on: "2027-06-20",
    returns_on: "2027-06-26",
    duration_in_days: 7,
    min_participants: 6,
    max_participants: 12,
    price_per_person: 129500,
    single_supplement: null,
    decision_date: "2027-05-31",
    latest_decision_date: "2027-05-31",
    guaranteed_at: null,
    cancelled_at: null,
    cancellation_reason: null,
    status: { value: "open", label: "Open" },
    is_guaranteed: false,
    occupied_seats: 5,
    confirmed_participants: 3,
    available_seats: 7,
    has_reached_minimum: false,
    awaits_guarantee: false,
    decision_overdue: false,
    can_be_edited: true,
    can_be_guaranteed: true,
    can_be_cancelled: true,
    ...attributes,
});

const mountPage = (props) => mount(Index, {
    props: { trip: { id: 1, name: "Vézère", slug: "vezere" }, isGroupTrip: true, departures: [], ...props },
    global: {
        components: { Pill, Button },
        mocks: { route: (name, id) => `/${name}/${id?.slug ?? id}` },
        stubs: {
            Admin: { template: "<div><slot /></div>" },
            Modal: { props: ["open"], template: '<div v-if="open" data-testid="modal"><slot /></div>' },
            Input: true,
            TextArea: true,
            Checkbox: true,
        },
    },
});

describe("Departures index", () => {
    it("shows a departure with its dates, counts, price and status", () => {
        const text = mountPage({ departures: [departure()] }).text();

        expect(text).toContain("20 jun 2027 – 26 jun 2027");
        expect(text).toContain("7 dagen");
        expect(text).toContain("3 bevestigd");
        expect(text).toContain("2 in afwachting");
        expect(text).toContain("minimum 6");
        expect(text).toContain("5 van 12 plekken bezet");
        expect(text).toMatch(/€\s1\.295,00/);
        expect(text).toContain("prijs van de reis");
        expect(text).toContain("Open");
    });

    it("draws the bar in two layers with the minimum halfway", () => {
        const wrapper = mountPage({ departures: [departure()] });

        expect(wrapper.find('[data-testid="progress-confirmed"]').attributes("style")).toContain("width: 25%");
        expect(wrapper.find('[data-testid="progress-held"]').attributes("style")).toContain("width: 16.6");
        expect(wrapper.find('[data-testid="progress-minimum"]').attributes("style")).toContain("50%");
    });

    it("points out a departure that waits for the guarantee or whose decision is overdue", () => {
        const waiting = mountPage({ departures: [departure({ awaits_guarantee: true, has_reached_minimum: true })] });
        const overdue = mountPage({ departures: [departure({ decision_overdue: true })] });

        expect(waiting.find('[data-testid="awaits-guarantee"]').exists()).toBe(true);
        expect(overdue.find('[data-testid="decision-overdue"]').exists()).toBe(true);
        expect(mountPage({ departures: [departure()] }).find('[data-testid="awaits-guarantee"]').exists()).toBe(false);
    });

    it("shows a full departure that is guaranteed as both", () => {
        const text = mountPage({
            departures: [departure({ status: { value: "full", label: "Vol" }, is_guaranteed: true, guaranteed_at: "2027-03-01T10:00:00+00:00" })],
        }).text();

        expect(text).toContain("Vol");
        expect(text).toContain("Gegarandeerd");
        expect(text).toContain("Gegarandeerd op 1 mrt 2027");
    });

    it("offers no actions that a cancelled departure cannot take, and shows the reason", () => {
        const wrapper = mountPage({
            departures: [departure({
                status: { value: "cancelled", label: "Geannuleerd" },
                cancelled_at: "2027-02-01T10:00:00+00:00",
                cancellation_reason: "Te weinig deelnemers",
                can_be_edited: false,
                can_be_guaranteed: false,
                can_be_cancelled: false,
            })],
        });

        expect(wrapper.find('[data-testid="guarantee"]').exists()).toBe(false);
        expect(wrapper.find('[data-testid="cancel"]').exists()).toBe(false);
        expect(wrapper.text()).toContain("Te weinig deelnemers");
    });

    it("opens the cancel dialog with a warning for a guaranteed departure", async () => {
        const wrapper = mountPage({ departures: [departure({ is_guaranteed: true, status: { value: "guaranteed", label: "Gegarandeerd" } })] });

        await wrapper.find('[data-testid="cancel"]').trigger("click");

        expect(wrapper.find('[data-testid="modal"]').exists()).toBe(true);
        expect(wrapper.find('[data-testid="warning-guaranteed"]').exists()).toBe(true);
        expect(wrapper.text()).toContain("5 bezette plekken");
    });

    it("opens the form to add a departure", async () => {
        const wrapper = mountPage();

        await wrapper.find('[data-testid="add-departure"]').trigger("click");

        expect(wrapper.find('[data-testid="modal"]').text()).toContain("Vertrek toevoegen");
    });

    it("tells an individual trip has no departures and offers no way to add one", () => {
        const wrapper = mountPage({ isGroupTrip: false });

        expect(wrapper.text()).toContain("individuele reis");
        expect(wrapper.find('[data-testid="add-departure"]').exists()).toBe(false);
    });
});
