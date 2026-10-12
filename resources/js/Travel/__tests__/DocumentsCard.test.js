import { describe, it, expect } from "vitest";
import { mount } from "@vue/test-utils";
import DocumentsCard from "@/Travel/DocumentsCard.vue";

const mountCard = (shareLink) => mount(DocumentsCard, {
    props: { booking: { id: 81 }, shareLink },
    global: {
        directives: { tippy: {} },
        mocks: { route: (name, id) => `/${name}/${id}` },
    },
});

describe("DocumentsCard", () => {
    it("opens the PDF in a new tab", () => {
        const link = mountCard(null).find('a[target="_blank"]');

        expect(link.attributes("href")).toBe("/admin.bookings.travel-documents/81");
        expect(link.text()).toBe("PDF bekijken");
    });

    it("offers to make a link while there is none", () => {
        const wrapper = mountCard(null);

        expect(wrapper.find("#share-link").exists()).toBe(false);
        expect(wrapper.text()).toContain("Deellink maken");
    });

    it("shows the link and until when it works", () => {
        const wrapper = mountCard({ url: "http://omdatwereizen.test/reisdocumenten/abc", expires_on: "2026-09-15", expired: false });

        expect(wrapper.find("#share-link").element.value).toBe("http://omdatwereizen.test/reisdocumenten/abc");
        expect(wrapper.text()).toContain("Werkt tot en met di 15 sep, 30 dagen na de terugreis.");
        expect(wrapper.text()).toContain("Nieuwe link maken");
    });

    it("says when the link has expired", () => {
        const wrapper = mountCard({ url: "http://omdatwereizen.test/reisdocumenten/abc", expires_on: "2026-09-15", expired: true });

        expect(wrapper.find("p.text-status-error").text()).toContain("Verlopen na di 15 sep");
    });
});
