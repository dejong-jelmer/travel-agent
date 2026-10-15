import { describe, it, expect } from "vitest";
import { formatDate, formatDuration, formatShortDate, formatTime } from "@/Travel/format.js";

describe("travel plan formatting", () => {
    it("formats a date in Dutch, on the day itself", () => {
        expect(formatDate("2026-08-11")).toBe("dinsdag 11 augustus");
        expect(formatShortDate("2026-08-16")).toBe("zo 16 aug");
    });

    it("reads the time from the string, without a time zone", () => {
        expect(formatTime("2026-08-11T05:43")).toBe("05:43");
    });

    it("formats a duration in hours and minutes", () => {
        expect(formatDuration(54)).toBe("54 min");
        expect(formatDuration(120)).toBe("2 u");
        expect(formatDuration(136)).toBe("2 u 16 min");
        expect(formatDuration(0)).toBe("0 min");
    });

    it("leaves a missing value empty", () => {
        expect(formatDate(null)).toBe("");
        expect(formatShortDate(null)).toBe("");
        expect(formatTime(null)).toBe("");
        expect(formatDuration(null)).toBe("");
    });
});
