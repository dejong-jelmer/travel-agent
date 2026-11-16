import { describe, it, expect, vi } from "vitest";
import { mount } from "@vue/test-utils";

vi.mock("@inertiajs/vue3", () => ({
    usePage: () => ({ props: { locale: "nl" } }),
    useForm: (data) => ({ ...data, errors: {}, processing: false, post: vi.fn(), put: vi.fn() }),
    router: { post: vi.fn(), delete: vi.fn() },
    Link: { props: ["href"], template: '<a :href="href"><slot /></a>' },
}));

import Index from "@/Pages/Admin/Trip/Departures/Index.vue";
import Overview from "@/Pages/Admin/Departures/Index.vue";
import Show from "@/Pages/Admin/Departures/Show.vue";
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
    decision_due_soon: false,
    needs_attention: false,
    days_until_decision: 40,
    trip: { id: 1, name: "Vézère", slug: "vezere" },
    can_be_edited: true,
    can_be_guaranteed: true,
    can_be_cancelled: true,
    ...attributes,
});

const global = {
    components: { Pill, Button },
    mocks: { route: (name, id) => `/${name}/${id?.slug ?? id ?? ""}` },
    stubs: {
        Admin: { template: "<div><slot /></div>" },
        Modal: { props: ["open"], template: '<div v-if="open" data-testid="modal"><slot /></div>' },
        Input: true,
        TextArea: true,
        Checkbox: true,
        BookingStatusBadge: { template: "<span><slot /></span>" },
        PaymentStatusBadge: { template: "<span><slot /></span>" },
    },
};

const mountPage = (props) => mount(Index, {
    props: { trip: { id: 1, name: "Vézère", slug: "vezere" }, isGroupTrip: true, departures: [], ...props },
    global,
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

describe("Departure card", () => {
    it("warns when the decision is due soon and the minimum is not reached", () => {
        const wrapper = mountPage({
            departures: [departure({ decision_due_soon: true, needs_attention: true, days_until_decision: 3 })],
        });

        expect(wrapper.find('[data-testid="decision-due-soon"]').text())
            .toContain("Over 3 dagen moet je beslissen en er ontbreken nog 3 bevestigde deelnemers");
    });

    it("links to the participant list", () => {
        const link = mountPage({ departures: [departure()] }).find('[data-testid="participants"]');

        expect(link.attributes("href")).toBe("/admin.departures.show/7");
    });
});

describe("Departures overview", () => {
    const mountOverview = (props) => mount(Overview, {
        props: { attention: [], upcoming: [], past: [], ...props },
        global,
    });

    it("puts what needs attention first, with the trip of every departure", () => {
        const wrapper = mountOverview({
            attention: [departure({ id: 1, awaits_guarantee: true, needs_attention: true, trip: { id: 2, name: "Lascaux", slug: "lascaux" } })],
            upcoming: [departure({ id: 2 })],
            past: [departure({ id: 3, status: { value: "completed", label: "Afgerond" }, can_be_edited: false, can_be_guaranteed: false, can_be_cancelled: false })],
        });

        const sections = wrapper.findAll("section").map((section) => section.attributes("data-testid"));
        expect(sections).toEqual(["section-attention", "section-upcoming", "section-past"]);
        expect(wrapper.find('[data-testid="section-attention"]').text()).toContain("Actie nodig");
        expect(wrapper.find('[data-testid="section-attention"]').text()).toContain("Lascaux");
        expect(wrapper.find('[data-testid="section-upcoming"]').text()).toContain("Vézère");
    });

    it("leaves the attention section out when nothing needs it", () => {
        const wrapper = mountOverview({ upcoming: [departure()] });

        expect(wrapper.find('[data-testid="section-attention"]').exists()).toBe(false);
        expect(wrapper.find('[data-testid="section-past"]').exists()).toBe(false);
    });

    it("says there are no departures yet", () => {
        expect(mountOverview().text()).toContain("Er zijn nog geen vertrekken");
    });
});

describe("Participant list", () => {
    const booking = (attributes = {}) => ({
        id: 11,
        reference: "2027-000011",
        status: "confirmed",
        status_label: "Bevestigd",
        payment_status: "paid",
        payment_status_label: "Betaald",
        occupies_seat: true,
        counts_towards_minimum: true,
        participants: 2,
        single_rooms: 1,
        display_price: 259000,
        main_booker: "Anne de Vries",
        email: "anne@example.com",
        phone: "0612345678",
        travelers: [
            { id: 1, full_name: "Anne de Vries", type: "adult", birthdate: "01-02-1980", special_requests: "Vegetarisch" },
            { id: 2, full_name: "Sam de Vries", type: "child", birthdate: "03-04-2018", special_requests: null },
        ],
        ...attributes,
    });

    const mountShow = (bookings) => mount(Show, {
        props: { departure: departure({ occupied_seats: 2 }), bookings },
        global,
    });

    it("lists the travelers of every booking that holds places, with the totals", () => {
        const wrapper = mountShow([booking()]);
        const table = wrapper.find('[data-testid="participants-table"]').text();

        expect(table).toContain("2027-000011");
        expect(table).toContain("Anne de Vries");
        expect(table).toContain("Sam de Vries");
        expect(table).toContain("kind");
        expect(table).toContain("anne@example.com");
        expect(table).toContain("Bevestigd");
        expect(table).toMatch(/€\s2\.590,00/);
        expect(wrapper.find('[data-testid="totals"]').text()).toBe("2 deelnemers · 1 boekingen · 1 eenpersoonskamers");
    });

    it("gathers the special requests", () => {
        const requests = mountShow([booking()]).find('[data-testid="special-requests"]').text();

        expect(requests).toContain("Anne de Vries");
        expect(requests).toContain("Vegetarisch");
        expect(requests).not.toContain("Sam de Vries");
    });

    it("keeps canceled bookings out of the list and the totals", () => {
        const wrapper = mountShow([
            booking(),
            booking({ id: 12, reference: "2027-000012", status: "canceled", occupies_seat: false, main_booker: "Piet Jansen", single_rooms: 2 }),
        ]);

        expect(wrapper.find('[data-testid="participants-table"]').text()).not.toContain("2027-000012");
        expect(wrapper.find('[data-testid="canceled-bookings"]').text()).toContain("2027-000012");
        expect(wrapper.find('[data-testid="canceled-bookings"]').text()).toContain("Piet Jansen");
        expect(wrapper.find('[data-testid="totals"]').text()).toContain("1 eenpersoonskamers");
    });

    it("says when nobody has booked yet and shows no actions on the departure", () => {
        const wrapper = mountShow([]);

        expect(wrapper.text()).toContain("Er zijn nog geen boekingen op dit vertrek.");
        expect(wrapper.find('[data-testid="guarantee"]').exists()).toBe(false);
    });
});

describe("Open requests on a departure", () => {
    it("shows the places asked and the requests waiting, apart from the bar", () => {
        const text = mountPage({ departures: [departure({ requested_seats: 5, interested_requests: 1 })] })
            .find('[data-testid="open-requests"]').text();

        expect(text).toContain("5 plekken aangevraagd");
        expect(text).toContain("1 wacht op een vrije plek");
    });

    it("says nothing without open requests or without the counts", () => {
        expect(mountPage({ departures: [departure({ requested_seats: 0, interested_requests: 0 })] }).find('[data-testid="open-requests"]').exists()).toBe(false);
        expect(mountPage({ departures: [departure()] }).find('[data-testid="open-requests"]').exists()).toBe(false);
    });
});
