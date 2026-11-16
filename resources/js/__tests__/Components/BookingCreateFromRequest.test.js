import { describe, it, expect, vi, beforeAll } from "vitest";
import { mount } from "@vue/test-utils";
import { reactive, nextTick } from "vue";

vi.mock("@inertiajs/vue3", () => ({
    usePage: () => ({ props: { locale: "nl" } }),
    useForm: (data) => reactive({ ...data, errors: {}, clearErrors: vi.fn() }),
    Link: { props: ["href"], template: '<a :href="href"><slot /></a>' },
}));

let Create;
let lastBooking;

beforeAll(async () => {
    vi.resetModules();
    Create = (await import("@/Pages/Admin/Booking/Create.vue")).default;
});

const departure = (attributes = {}) => ({
    id: 7,
    departs_on: "2027-06-20",
    returns_on: "2027-06-26",
    available_seats: 4,
    booking_price_per_person: 120000,
    booking_single_supplement: 15000,
    ...attributes,
});

const trips = [
    { id: 1, name: "Verona", format: "individual", departures: [] },
    { id: 2, name: "Vézère", format: "group", departures: [departure()] },
];

const mountCreate = (fromRequest) => mount(Create, {
    props: { trips, fromRequest },
    global: {
        mocks: { route: (name, id) => `/${name}/${id}` },
        stubs: {
            Admin: { template: "<div><slot /></div>" },
            Select: true,
            BookingForm: {
                props: ["booking"],
                setup(props) {
                    lastBooking = props.booking;
                },
                template: "<div />",
            },
        },
    },
});

const request = (attributes = {}) => ({
    id: 31,
    trip_id: 2,
    departure_id: 7,
    travelers_count: 3,
    single_room: true,
    name: "Anne van der Berg",
    email: "anne@example.com",
    phone: "0612345678",
    ...attributes,
});

describe("Making a booking from a request", () => {
    it("starts with the trip, departure, travelers, single room and contact of the request", async () => {
        const wrapper = mountCreate(request());
        await nextTick();

        expect(lastBooking.trip.id).toBe(2);
        expect(lastBooking.trip_request_id).toBe(31);
        expect(lastBooking.departure_id).toBe(7);
        expect(lastBooking.departure).toMatchObject({ available_seats: 4, price_per_person: 120000 });
        expect(lastBooking.participants.adults).toBe(3);
        expect(lastBooking.travelers.adults).toHaveLength(3);
        expect(lastBooking.single_rooms).toBe(1);
        expect(lastBooking.contact.email).toBe("anne@example.com");
        expect(lastBooking.contact.phone).toBe("0612345678");
        expect(lastBooking.travelers.adults[0].first_name).toBe("Anne");
        expect(lastBooking.travelers.adults[0].last_name).toBe("van der Berg");
        expect(wrapper.find('[data-testid="from-request"]').text()).toContain("Anne van der Berg");
    });

    it("leaves the single rooms to the default when none was asked", () => {
        mountCreate(request({ single_room: false, travelers_count: 1 }));

        expect(lastBooking.single_rooms).toBeNull();
        expect(lastBooking.participants.adults).toBe(1);
    });

    it("leaves a departure that cannot be booked for the admin to choose", () => {
        const full = [trips[0], { ...trips[1], departures: [departure({ available_seats: 0 })] }];
        mount(Create, {
            props: { trips: full, fromRequest: request() },
            global: { mocks: { route: () => "" }, stubs: { Admin: { template: "<div><slot /></div>" }, Select: true, BookingForm: { props: ["booking"], setup(props) { lastBooking = props.booking; }, template: "<div />" } } },
        });

        expect(lastBooking.departure_id).toBeNull();
    });

    it("starts as before without a request", () => {
        const wrapper = mountCreate(null);

        expect(lastBooking.trip.id).toBe(1);
        expect(lastBooking.trip_request_id).toBeNull();
        expect(wrapper.find('[data-testid="from-request"]').exists()).toBe(false);
    });
});
