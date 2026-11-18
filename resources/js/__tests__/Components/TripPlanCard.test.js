import { describe, it, expect } from "vitest";
import { mount } from "@vue/test-utils";
import TripPlanCard from "@/Components/Organisms/TripPlanCard.vue";

const mountCard = (props = {}) => mount(TripPlanCard, {
    props,
    global: { stubs: { BaseCard: { template: "<section><slot /></section>" }, Button: { template: "<button><slot /></button>" } } },
});

describe("TripPlanCard", () => {
    it("keeps the texts of an individual trip", () => {
        const text = mountCard().text();

        expect(text).toContain("Je ontvangt een persoonlijk reisvoorstel");
        expect(text).toContain("De getoonde prijs is een vanaf prijs per persoon");
    });

    it("tells how a request for a group trip goes and what the price holds", () => {
        const wrapper = mountCard({ isGroupTrip: true });
        const text = wrapper.text();

        expect(text).toContain("Kies een vertrekdatum en doe een aanvraag.");
        expect(text).toContain("Je plek is zeker zodra ik je aanvraag bevestig");
        expect(text).not.toContain("persoonlijk reisvoorstel");
        expect(wrapper.find('[data-testid="group-prices"]').text()).toContain("garantie- en calamiteitenfonds");
    });
});
