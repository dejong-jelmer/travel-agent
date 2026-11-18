import { describe, it, expect, vi, beforeAll } from "vitest";
import { mount } from "@vue/test-utils";
import { reactive } from "vue";

const forms = [];

vi.mock("@inertiajs/vue3", () => ({
    useForm: (data) => {
        const form = reactive({ ...data, errors: {}, processing: false });
        form.transform = vi.fn((callback) => {
            form.transformed = callback;
            return form;
        });
        form.post = vi.fn();
        forms.push(form);
        return form;
    },
    usePage: () => ({ props: { locale: "nl" } }),
}));

vi.mock("vue-toastification", () => ({ useToast: () => ({ success: vi.fn() }) }));

let TripRequestForm, Select, Checkbox;

beforeAll(async () => {
    // The form calls Ziggy's route() from its script, where the template mock does not reach
    vi.stubGlobal("route", () => "/reis/vezere/aanvraag");
    vi.resetModules();
    TripRequestForm = (await import("@/Components/Organisms/Forms/TripRequestForm.vue")).default;
    Select = (await import("@/Components/Molecules/Select.vue")).default;
    Checkbox = (await import("@/Components/Molecules/Checkbox.vue")).default;
});

const departure = (attributes = {}) => ({
    id: 7,
    departs_on: "2027-06-20",
    returns_on: "2027-06-26",
    availability: "available",
    seats_left: null,
    ...attributes,
});

const mountForm = (props) => mount(TripRequestForm, {
    props,
    global: {
        components: { Select, Checkbox },
        mocks: { route: () => "/reis/vezere/aanvraag" },
        stubs: {
            Input: true,
            TextArea: true,
            Label: { template: "<label><slot /></label>" },
            FormFeedback: true,
            DefaultLink: true,
            Button: { template: "<button><slot /></button>" },
            // The real field throws on a bot; the stub lets the form submit
            VueHoneypot: { template: "<div />", methods: { validate() {} } },
            "i18n-t": true,
        },
    },
});

const groupTrip = { id: 1, slug: "vezere", name: "Vézère", format: "group" };

describe("TripRequestForm for a group trip", () => {
    it("asks for a departure instead of a period and a station", () => {
        const wrapper = mountForm({ trip: groupTrip, departures: [departure()] });

        expect(wrapper.find("#departure_id").exists()).toBe(true);
        expect(wrapper.find("#preferred_month").exists()).toBe(false);
        expect(wrapper.find("#departure_station").exists()).toBe(false);
        expect(wrapper.text()).toContain("Aanvraag voor Vézère");
        expect(wrapper.text()).toContain("Ik wil graag een eenpersoonskamer");
        expect(wrapper.find('[data-testid="no-reservation"]').text())
            .toBe("Je aanvraag reserveert nog geen plek. Je plek is zeker zodra ik je aanvraag bevestig.");
    });

    it("starts with the departure that was picked on the page", () => {
        mountForm({ trip: groupTrip, departures: [departure(), departure({ id: 8 })], departure: departure({ id: 8 }) });

        expect(forms.at(-1).departure_id).toBe("8");
    });

    it("lists the departures with their dates, a full one marked", () => {
        const options = mountForm({ trip: groupTrip, departures: [departure(), departure({ id: 8, availability: "full" })] })
            .findAll("#departure_id option").map((option) => option.text());

        expect(options).toContain("20 juni – 26 juni 2027");
        expect(options).toContain("20 juni – 26 juni 2027 · vol");
    });

    it("offers at most 6 travelers, and no more than the places that are left", async () => {
        const wrapper = mountForm({ trip: groupTrip, departures: [departure(), departure({ id: 8, availability: "few_seats", seats_left: 3 })] });
        const travelerOptions = () => wrapper.findAll("#travelers_count option").filter((option) => option.attributes("value")).map((option) => option.text());

        forms.at(-1).departure_id = "7";
        await wrapper.vm.$nextTick();
        expect(travelerOptions()).toEqual(["1", "2", "3", "4", "5", "6"]);

        forms.at(-1).travelers_count = "5";
        forms.at(-1).departure_id = "8";
        await wrapper.vm.$nextTick();
        expect(travelerOptions()).toEqual(["1", "2", "3"]);
        expect(forms.at(-1).travelers_count).toBe("");
    });

    it("turns into leaving interest for a full departure", async () => {
        const wrapper = mountForm({ trip: groupTrip, departures: [departure({ availability: "full" })], departure: departure({ availability: "full" }) });

        expect(wrapper.find('[data-testid="interest-notice"]').text()).toContain("Dit vertrek is vol.");
        expect(wrapper.find('[data-testid="no-reservation"]').exists()).toBe(false);
        expect(wrapper.text()).toContain("Laat het me weten");

        await wrapper.find("form").trigger("submit");
        const form = forms.at(-1);
        expect(form.transformed({ name: "Anne" })).toEqual({ name: "Anne", interest: true });
        expect(form.post).toHaveBeenCalled();
    });

    it("sends an ordinary request for a departure with room", async () => {
        const wrapper = mountForm({ trip: groupTrip, departures: [departure()], departure: departure() });

        expect(wrapper.text()).toContain("Doe een aanvraag");
        await wrapper.find("form").trigger("submit");

        expect(forms.at(-1).transformed({}).interest).toBe(false);
    });
});

describe("TripRequestForm for an individual trip", () => {
    it("keeps the period, the station and more than 6 travelers", () => {
        const wrapper = mountForm({ trip: { id: 2, slug: "verona", name: "Verona", format: "individual" } });

        expect(wrapper.find("#preferred_month").exists()).toBe(true);
        expect(wrapper.find("#departure_station").exists()).toBe(true);
        expect(wrapper.find("#departure_id").exists()).toBe(false);
        expect(wrapper.findAll("#travelers_count option").map((option) => option.text())).toContain("Meer dan 6");
        expect(wrapper.text()).toContain("Vraag voorstel aan");
    });
});
