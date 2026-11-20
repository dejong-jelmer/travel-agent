import { describe, it, expect } from "vitest";
import { formatDate, progressParts } from "@/Departures/format.js";

describe("formatDate", () => {
    it("formats a calendar day without shifting it", () => {
        expect(formatDate("2027-06-20", "nl")).toBe("20 jun 2027");
        expect(formatDate("2027-01-01", "nl")).toBe("1 jan 2027");
    });

    it("formats a timestamp", () => {
        expect(formatDate("2027-03-01T12:00:00+00:00", "nl")).toBe("1 mrt 2027");
    });

    it("gives nothing for no date", () => {
        expect(formatDate(null)).toBe("");
    });
});

describe("progressParts", () => {
    it("splits the bar into confirmed and held places, with the minimum", () => {
        expect(progressParts({ occupied: 6, confirmed: 4, min: 5, max: 10 })).toEqual({
            held: 2,
            confirmedWidth: 40,
            heldWidth: 20,
            minimumAt: 50,
        });
    });

    it("has no held part when every place is confirmed", () => {
        const parts = progressParts({ occupied: 4, confirmed: 4, min: 4, max: 8 });

        expect(parts.held).toBe(0);
        expect(parts.heldWidth).toBe(0);
    });

    it("never draws past the end of the bar", () => {
        const parts = progressParts({ occupied: 14, confirmed: 9, min: 4, max: 10 });

        expect(parts.confirmedWidth + parts.heldWidth).toBeLessThanOrEqual(100);
    });

    it("draws nothing for an empty departure", () => {
        expect(progressParts({ occupied: 0, confirmed: 0, min: 6, max: 12 })).toEqual({
            held: 0,
            confirmedWidth: 0,
            heldWidth: 0,
            minimumAt: 50,
        });
    });
});
