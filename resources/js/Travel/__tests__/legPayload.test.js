import { describe, it, expect } from "vitest";
import { legPayload, legValues } from "@/Travel/legPayload.js";

const train = {
    id: 4,
    type: "train",
    from_station: { id: 2, name: "Nijmegen" },
    to_station: { id: 3, name: "Utrecht Centraal" },
    departure_at: "2026-08-11T05:43",
    arrival_at: "2026-08-11T06:37",
    duration_minutes: null,
    number: "3512",
    carriage: null,
    seat: null,
    note: null,
    transit_lines: [{ id: 5, name: "NS Intercity", direction: "Den Helder" }],
};

describe("the values of the leg form", () => {
    it("takes the times of day and the line ids of a leg", () => {
        expect(legValues(train)).toMatchObject({
            type: "train",
            from_station_id: 2,
            to_station_id: 3,
            departure_time: "05:43",
            arrival_time: "06:37",
            duration_minutes: "",
            number: "3512",
            carriage: "",
            transit_lines: [{ id: 5, direction: "Den Helder" }],
        });
    });

    it("starts a new leg from the defaults", () => {
        expect(legValues(null, { type: "transfer", from_station_id: 3 })).toMatchObject({
            type: "transfer",
            from_station_id: 3,
            to_station_id: null,
            departure_time: "",
            transit_lines: [],
        });
    });
});

describe("the payload of the leg form", () => {
    const options = { after: "2026-08-11", hasLines: true };

    it("sends the times as moments and leaves the duration to them", () => {
        const payload = legPayload({ ...legValues(train), duration_minutes: "54" }, options);

        expect(payload).toMatchObject({
            departure_at: "2026-08-11T05:43",
            arrival_at: "2026-08-11T06:37",
            duration_minutes: null,
            number: "3512",
            carriage: null,
            transit_lines: [{ id: 5, direction: "Den Helder" }],
        });
    });

    it("keeps a transfer at its one station, with a duration and without lines or a ticket", () => {
        const values = { ...legValues(train), type: "transfer", departure_time: "", arrival_time: "", duration_minutes: "11" };

        expect(legPayload(values, { after: "2026-08-11T06:37", hasLines: false })).toMatchObject({
            from_station_id: 2,
            to_station_id: 2,
            departure_at: null,
            arrival_at: null,
            duration_minutes: 11,
            number: null,
            transit_lines: [],
        });
    });

    it("sends no times for an arrival route", () => {
        const payload = legPayload(legValues(train), { after: null, hasLines: true });

        expect(payload).toMatchObject({ departure_at: null, arrival_at: null, duration_minutes: null });
    });

    it("sends empty fields as null", () => {
        const values = { ...legValues(null, { type: "walk" }), duration_minutes: "", note: "" };

        expect(legPayload(values, { after: null, hasLines: false })).toMatchObject({ duration_minutes: null, note: null });
    });
});
