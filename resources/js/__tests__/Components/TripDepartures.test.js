import { describe, it, expect } from "vitest";
import { mount } from "@vue/test-utils";
import TripDepartures from "@/Components/Organisms/TripDepartures.vue";

const departure = (attributes = {}) => ({
    id: 7,
    departs_on: "2027-06-20",
    returns_on: "2027-06-26",
    duration_in_days: 7,
    price_per_person: 129500,
    single_supplement: 15000,
    availability: "available",
    seats_left: null,
    is_guaranteed: false,
    min_participants: 6,
    decision_date: "2027-05-31",
    ...attributes,
});

const mountList = (props) => mount(TripDepartures, {
    props,
});

describe("TripDepartures", () => {
    it("shows a departure with its dates, price and single room supplement", () => {
        const text = mountList({ departures: [departure()] }).text();

        expect(text).toContain("zo 20 juni – za 26 juni 2027");
        expect(text).toMatch(/€\s1\.295\s+per persoon/);
        expect(text).toMatch(/eenpersoonskamer \+ €\s150/);
        expect(text).not.toContain("Je reist op een van deze data");
    });

    it("sums the period up in a date tile", () => {
        const wrapper = mountList({ departures: [departure({ departs_on: "2027-02-05", returns_on: "2027-02-07" })] });
        const tile = wrapper.findAll('[data-testid="departure-7"] div[aria-hidden="true"] > span').map((span) => span.text());

        expect(tile).toEqual(["vr – zo", "5–7", "feb 2027", "februari 2027"]);
    });

    it("names both months in the tile of a departure across months", () => {
        const wrapper = mountList({ departures: [departure({ departs_on: "2027-01-30", returns_on: "2027-02-01" })] });
        const tile = wrapper.findAll('[data-testid="departure-7"] div[aria-hidden="true"] > span').map((span) => span.text());

        expect(tile).toEqual(["za – ma", "30–1", "jan – feb 2027", "jan – feb 2027"]);
    });

    it("tells from how many participants a departure goes ahead and by when that is known", () => {
        const wrapper = mountList({ departures: [departure()] });

        expect(wrapper.find('[data-testid="minimum"]').text().replace(/\s+/g, " "))
            .toBe("Gaat door vanaf 6 deelnemers. Uiterlijk 31 mei 2027 hoor je of de reis doorgaat.");
        expect(wrapper.find('[data-testid="guaranteed"]').exists()).toBe(false);
    });

    it("leaves the day out once it has passed", () => {
        const wrapper = mountList({ departures: [departure({ decision_date: null })] });

        expect(wrapper.find('[data-testid="minimum"]').text()).toBe("Gaat door vanaf 6 deelnemers.");
    });

    it("says a guaranteed departure is certain, without the minimum", () => {
        const wrapper = mountList({ departures: [departure({ is_guaranteed: true, min_participants: null, decision_date: null })] });

        expect(wrapper.find('[data-testid="guaranteed"]').text()).toBe("Vertrek gegarandeerd");
        expect(wrapper.find('[data-testid="minimum"]').exists()).toBe(false);
    });

    it("names the places left only when few are", () => {
        const few = mountList({ departures: [departure({ availability: "few_seats", seats_left: 3 })] });
        const one = mountList({ departures: [departure({ availability: "few_seats", seats_left: 1 })] });
        const roomy = mountList({ departures: [departure()] });

        expect(few.find('[data-testid="seats-left"]').text()).toBe("Nog 3 plaatsen");
        expect(one.find('[data-testid="seats-left"]').text()).toBe("Nog 1 plaats");
        expect(roomy.find('[data-testid="seats-left"]').exists()).toBe(false);
    });

    it("asks a place on the departure that is clicked", async () => {
        const wrapper = mountList({ departures: [departure(), departure({ id: 8 })] });

        expect(wrapper.find('[data-testid="departure-8"] [data-testid="request"]').text()).toBe("Vraag aan");
        await wrapper.find('[data-testid="departure-8"] [data-testid="request"]').trigger("click");

        expect(wrapper.emitted("request")[0][0].id).toBe(8);
    });

    it("keeps a full departure in the list, with a way to show interest instead of a request", async () => {
        const wrapper = mountList({ departures: [departure({ availability: "full" })] });

        expect(wrapper.find('[data-testid="full"]').text()).toBe("Vol");
        expect(wrapper.find('[data-testid="request"]').exists()).toBe(false);
        expect(wrapper.find('[data-testid="interest"]').text()).toBe("Wil je horen als er een plek vrijkomt?");

        await wrapper.find('[data-testid="interest"]').trigger("click");
        expect(wrapper.emitted("request")[0][0].id).toBe(7);
    });

    it("tells what a booking pays on top of the price", () => {
        const wrapper = mountList({
            departures: [departure()],
            fees: { total: 3750, items: { booking_fee: 2500, guarantee_fund: 1000, emergency_fund: 250 } },
        });

        expect(wrapper.find('[data-testid="fees"]').text().replace(/\u00a0/g, " "))
            .toBe("Prijzen zijn per persoon. Per boeking komt daar € 37,50 bij: boekingskost € 25, garantiefonds € 10, calamiteitenfonds € 2,50.");
    });

    it("leaves that out when nothing comes on top", () => {
        expect(mountList({ departures: [departure()] }).find('[data-testid="fees"]').exists()).toBe(false);
    });

    it("shows a departure without a price without one", () => {
        const text = mountList({ departures: [departure({ price_per_person: null, single_supplement: null })] }).text();

        expect(text).not.toContain("per persoon");
    });

    it("says so when no departures are planned", () => {
        const wrapper = mountList({ departures: [] });

        expect(wrapper.find('[data-testid="no-departures"]').text()).toBe("Er staan nu geen vertrekdata gepland.");
        expect(wrapper.find("ul").exists()).toBe(false);
    });
});
