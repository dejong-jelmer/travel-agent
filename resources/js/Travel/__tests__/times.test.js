import { describe, it, expect } from "vitest";
import { minutesBetween, momentBefore, placeTime, placeTimes } from "@/Travel/times.js";

describe("placing times of day", () => {
    it("places a time on the date of the journey", () => {
        expect(placeTime("2026-08-11", "05:43")).toBe("2026-08-11T05:43");
    });

    it("places a time on the same day when it is not before the moment it follows", () => {
        expect(placeTime("2026-08-11T06:37", "06:48")).toBe("2026-08-11T06:48");
        expect(placeTime("2026-08-11T06:37", "06:37")).toBe("2026-08-11T06:37");
    });

    it("places a time on the next day when it is before the moment it follows", () => {
        expect(placeTime("2026-08-11T23:10", "07:05")).toBe("2026-08-12T07:05");
        expect(placeTime("2026-12-31T23:10", "07:05")).toBe("2027-01-01T07:05");
    });

    it("lets the arrival follow on the departure, so a night train arrives the next day", () => {
        expect(placeTimes("2026-08-11T19:30", "23:10", "07:05")).toEqual({
            departureAt: "2026-08-11T23:10",
            arrivalAt: "2026-08-12T07:05",
        });
    });

    it("places nothing without a moment to follow on", () => {
        expect(placeTimes(null, "05:43", "06:37")).toEqual({ departureAt: null, arrivalAt: null });
    });

    it("counts the minutes across midnight", () => {
        expect(minutesBetween("2026-08-11T23:10", "2026-08-12T07:05")).toBe(475);
    });
});

describe("the moment a leg follows on", () => {
    const legs = [
        { departure_at: "2026-08-11T05:43", arrival_at: "2026-08-11T06:37" },
        { departure_at: null, arrival_at: null },
        { departure_at: "2026-08-11T06:48", arrival_at: null },
    ];

    it("is the last time of the legs before it", () => {
        expect(momentBefore(legs, 2, "2026-08-11")).toBe("2026-08-11T06:37");
        expect(momentBefore(legs, 3, "2026-08-11")).toBe("2026-08-11T06:48");
    });

    it("is the date of the journey for the first leg", () => {
        expect(momentBefore(legs, 0, "2026-08-11")).toBe("2026-08-11");
        expect(momentBefore([], 0, null)).toBe(null);
    });
});
