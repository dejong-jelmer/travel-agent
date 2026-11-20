import { describe, it, expect } from "vitest";
import { lastUpdatedDate } from "@/Support/storyDates";
import { useDateFormatter } from "@/Composables/useDateFormatter";

const { formattedDate } = useDateFormatter();
// As the story page formats its dates
const dutch = (date) => formattedDate(date, { longDay: false, locale: 'nl' });

describe("lastUpdatedDate", () => {
    it("gives the day of the last update in Dutch", () => {
        expect(lastUpdatedDate('2026-06-20T09:00:00+02:00', '2026-11-20T10:30:00+01:00', dutch))
            .toBe('20 november 2026');
    });

    it("leaves out an update on the day of publication", () => {
        expect(lastUpdatedDate('2026-06-20T09:00:00+02:00', '2026-06-20T16:00:00+02:00', dutch)).toBeNull();
    });

    it("leaves out a last save before the publication", () => {
        // A story scheduled ahead goes live without a save
        expect(lastUpdatedDate('2026-06-20T09:00:00+02:00', '2026-06-01T09:00:00+02:00', dutch)).toBeNull();
    });

    it("leaves out a story without a publication or a save", () => {
        expect(lastUpdatedDate(null, '2026-11-20T10:30:00+01:00', dutch)).toBeNull();
        expect(lastUpdatedDate('2026-06-20T09:00:00+02:00', null, dutch)).toBeNull();
    });
});
