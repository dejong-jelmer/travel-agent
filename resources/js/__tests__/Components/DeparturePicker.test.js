import { describe, it, expect, vi, beforeAll } from "vitest";
import { mount } from "@vue/test-utils";
import { reactive } from "vue";

vi.mock("@inertiajs/vue3", () => ({
    usePage: () => ({ props: { locale: "nl", fees_and_funds: { booking_fee: 2500 } } }),
}));

// The test files share one module cache, and a file that ran before this one (BookingForm.test.js) has already loaded
// these components with the real Inertia. Loading them again after a reset is what makes the mock above reach them.
let DeparturePicker, DeparturePrice, Price, Input;

beforeAll(async () => {
    vi.resetModules();

    DeparturePicker = (await import("@/Components/Organisms/BookingSteps/DeparturePicker.vue")).default;
    DeparturePrice = (await import("@/Components/Organisms/BookingSteps/DeparturePrice.vue")).default;
    Price = (await import("@/Components/Organisms/BookingSteps/Price.vue")).default;
    Input = (await import("@/Components/Molecules/Input.vue")).default;
    global.components = { Input };
});

const departure = (attributes = {}) => ({
    id: 7,
    departs_on: "2027-06-20",
    returns_on: "2027-06-26",
    available_seats: 4,
    status: { value: "open", label: "Open" },
    booking_price_per_person: 120000,
    booking_single_supplement: 15000,
    ...attributes,
});

const form = (departures, attributes = {}) => reactive({
    trip: { id: 1, slug: "vezere", format: "group", departures },
    departure_id: null,
    departure: null,
    departure_date: null,
    return_date: null,
    participants: { adults: 2, children: 0 },
    single_rooms: null,
    final_price: null,
    errors: {},
    clearErrors: vi.fn(),
    ...attributes,
});

const global = {
    // Filled in beforeAll, once the components are loaded
    components: {},
    mocks: { route: (name, trip) => `/${name}/${trip.slug}` },
    stubs: { FormFeedback: { props: ["message"], template: "<p>{{ message }}</p>" }, Label: { template: "<label><slot /></label>" } },
};

describe("DeparturePicker", () => {
    it("lists the departures with their dates, places and price", () => {
        const text = mount(DeparturePicker, { props: { booking: form([departure()]) }, global }).text();

        expect(text).toContain("20 jun 2027 – 26 jun 2027");
        expect(text).toContain("nog 4 plekken vrij");
        expect(text).toMatch(/€\s1\.200,00/);
        expect(text).toContain("Geen vertrek gekozen");
    });

    it("puts the departure and its dates on the booking when one is picked", async () => {
        const booking = form([departure()]);
        const wrapper = mount(DeparturePicker, { props: { booking }, global });

        await wrapper.find('[data-testid="pick-departure-7"]').trigger("click");

        expect(booking.departure_id).toBe(7);
        expect(booking.departure).toMatchObject({ available_seats: 4, price_per_person: 120000, single_supplement: 15000 });
        expect(booking.departure_date.getFullYear()).toBe(2027);
        expect(booking.departure_date.getMonth()).toBe(5);
        expect(booking.departure_date.getDate()).toBe(20);
        expect(booking.return_date.getDate()).toBe(26);
        expect(wrapper.find('[data-testid="pick-departure-7"]').attributes("aria-checked")).toBe("true");
    });

    it("does not offer a departure that is full or has no price", async () => {
        const booking = form([
            departure({ id: 1, available_seats: 0, status: { value: "full", label: "Vol" } }),
            departure({ id: 2, booking_price_per_person: null, booking_single_supplement: null }),
        ]);
        const wrapper = mount(DeparturePicker, { props: { booking }, global });

        expect(wrapper.find('[data-testid="pick-departure-1"]').attributes("disabled")).toBeDefined();
        expect(wrapper.find('[data-testid="pick-departure-2"]').attributes("disabled")).toBeDefined();
        expect(wrapper.text()).toContain("vol");
        expect(wrapper.text()).toContain("geen prijs ingesteld");

        await wrapper.find('[data-testid="pick-departure-1"]').trigger("click");
        expect(booking.departure_id).toBeNull();
    });

    it("points to the departures of the trip when there are none to book", () => {
        const wrapper = mount(DeparturePicker, { props: { booking: form([]) }, global });

        expect(wrapper.find('[data-testid="no-departures"] a').attributes("href")).toBe("/admin.trips.departures.index/vezere");
    });
});

describe("DeparturePrice", () => {
    const picked = (attributes = {}) => form([departure()], {
        departure_id: 7,
        departure: { id: 7, available_seats: 4, price_per_person: 120000, single_supplement: 15000 },
        ...attributes,
    });

    it("builds the price from the travelers, the single rooms and the fees", () => {
        const wrapper = mount(DeparturePrice, { props: { booking: picked({ single_rooms: 1 }) }, global });

        expect(wrapper.find('[data-testid="base-total"]').text()).toMatch(/2\.400,00/);
        expect(wrapper.find('[data-testid="supplement-total"]').text()).toMatch(/150,00/);
        expect(wrapper.find('[data-testid="calculated-price"]').text()).toMatch(/2\.575,00/);
        // No cost items or margin to fill in
        expect(wrapper.find('#margin_percentage').exists()).toBe(false);
        expect(wrapper.findAll("input").map((input) => input.attributes("id"))).toEqual(["single_rooms", "final_price"]);
    });

    it("shows the default single room of a traveler on their own, and keeps a typed number", async () => {
        const booking = picked({ participants: { adults: 1, children: 0 } });
        const wrapper = mount(DeparturePrice, { props: { booking }, global });
        const input = wrapper.find('#single_rooms');

        expect(input.element.value).toBe("1");
        expect(booking.single_rooms).toBeNull();

        await input.setValue("0");

        expect(booking.single_rooms).toBe(0);
        expect(wrapper.find('[data-testid="supplement-total"]').text()).toMatch(/0,00/);
    });

    it("lets a final price override the price of the departure", () => {
        const wrapper = mount(DeparturePrice, { props: { booking: picked({ final_price: 2000 }) }, global });

        expect(wrapper.find('[data-testid="display-price"]').text()).toMatch(/2\.000,00/);
        expect(wrapper.find('[data-testid="calculated-price"]').classes()).toContain("line-through");
    });

    it("says so when the departure has no price", () => {
        const wrapper = mount(DeparturePrice, { props: { booking: picked({ departure: null }) }, global });

        expect(wrapper.find('[data-testid="no-departure-price"]').exists()).toBe(true);
    });
});

// BookingForm swaps the steps inside a <Transition mode="out-in">. That only works on a component with one element
// as its root: with anything next to it, also a comment, the step never leaves and the next one stays empty.
describe("Price step root", () => {
    const mountPrice = (booking) => mount(Price, { props: { booking }, global: { ...global, stubs: { ...global.stubs, Select: true } } });

    it("is a single element for an individual booking", () => {
        const wrapper = mountPrice(form([], {
            trip: { id: 1, format: "individual" },
            cost_items: [{ category: "train", label: "", amount_per_person: null, quantity: 2 }],
            margin_percentage: 15,
            margin_amount: null,
            fee_per_person: null,
        }));

        expect(wrapper.element.nodeType).toBe(Node.ELEMENT_NODE);
        expect(wrapper.element.parentElement.childNodes).toHaveLength(1);
        expect(wrapper.find("#margin_percentage").exists()).toBe(true);
    });

    it("is a single element for a booking on a departure", () => {
        const wrapper = mountPrice(form([departure()], {
            departure_id: 7,
            departure: { id: 7, available_seats: 4, price_per_person: 120000, single_supplement: 15000 },
        }));

        expect(wrapper.element.nodeType).toBe(Node.ELEMENT_NODE);
        expect(wrapper.element.parentElement.childNodes).toHaveLength(1);
        expect(wrapper.find("#single_rooms").exists()).toBe(true);
    });
});
