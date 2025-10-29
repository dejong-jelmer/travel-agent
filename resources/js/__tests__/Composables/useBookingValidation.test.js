/**
 * @fileoverview Test suite for useBookingValidation composable
 * Tests validation logic for all booking form steps
 */

import { describe, it, expect, beforeEach } from "vitest";
import { useBookingValidation } from "@/Composables/useBookingValidation.js";

// Field path constants for consistent test assertions
const FIELD_PATHS = {
    travelers: {
        adult: (index, field) => `travelers.adults.${index}.${field}`,
        child: (index, field) => `travelers.children.${index}.${field}`,
    },
    contact: {
        street: "contact.street",
        houseNumber: "contact.house_number",
        postalCode: "contact.postal_code",
        city: "contact.city",
        email: "contact.email",
        phone: "contact.phone",
    },
};

// Factory functions for creating test data
function createValidTraveler(overrides = {}) {
    return {
        first_name: "Jan",
        last_name: "Jansen",
        birthdate: "01-01-1990",
        nationality: "NL",
        ...overrides,
    };
}

function createValidContact(overrides = {}) {
    return {
        street: "Hoofdstraat",
        house_number: "123",
        postal_code: "1234AB",
        city: "Amsterdam",
        email: "test@example.com",
        phone: "0612345678",
        ...overrides,
    };
}

function createBookingData({ adults = [], children = [], contact = null } = {}) {
    const data = {};

    if (adults.length > 0 || children.length > 0) {
        data.travelers = {};
        if (adults.length > 0) data.travelers.adults = adults;
        if (children.length > 0) data.travelers.children = children;
    }

    if (contact) {
        data.contact = contact;
    }

    return data;
}

describe("useBookingValidation", () => {
    let validator;

    beforeEach(() => {
        validator = useBookingValidation();
    });

    describe("validateTripStep", () => {
        describe("departure_date validation", () => {
            it("should return error when departure_date is missing", () => {
                const bookingData = { departure_date: null };
                const errors = validator.validateTripStep(bookingData);

                expect(errors).toHaveProperty("departure_date");
                expect(errors.departure_date).toBe("Kies een vertrekdatum.");
            });

            it("should return error when departure_date is undefined", () => {
                const bookingData = {};
                const errors = validator.validateTripStep(bookingData);

                expect(errors).toHaveProperty("departure_date");
                expect(errors.departure_date).toContain("vertrekdatum");
            });

            it("should accept valid departure_date", () => {
                const bookingData = { departure_date: "2025-05-15" };
                const errors = validator.validateTripStep(bookingData);

                expect(errors).toEqual({});
            });
        });

        describe("bookingData validation", () => {
            it("should return error when bookingData is null", () => {
                const errors = validator.validateTripStep(null);

                expect(errors).toHaveProperty("trip");
                expect(errors.trip).toBe("Boekinggegevens ontbreken.");
            });

            it("should return error when bookingData is undefined", () => {
                const errors = validator.validateTripStep(undefined);

                expect(errors).toHaveProperty("trip");
                expect(errors.trip).toContain("ontbreken");
            });
        });
    });

    describe("validateTravelersStep", () => {
        describe("first_name validation", () => {
            it("should return error when first_name is empty", () => {
                const bookingData = createBookingData({
                    adults: [createValidTraveler({ first_name: "" })],
                });

                const errors = validator.validateTravelersStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.travelers.adult(0, "first_name"));
                expect(errors[FIELD_PATHS.travelers.adult(0, "first_name")]).toContain(
                    "Voornaam ontbreekt"
                );
            });

            it("should return error when first_name is only whitespace", () => {
                const bookingData = createBookingData({
                    adults: [createValidTraveler({ first_name: "   " })],
                });

                const errors = validator.validateTravelersStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.travelers.adult(0, "first_name"));
                expect(errors[FIELD_PATHS.travelers.adult(0, "first_name")]).toContain(
                    "ontbreekt"
                );
            });

            it("should return error when first_name is too short", () => {
                const bookingData = createBookingData({
                    adults: [createValidTraveler({ first_name: "Jo" })],
                });

                const errors = validator.validateTravelersStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.travelers.adult(0, "first_name"));
                expect(errors[FIELD_PATHS.travelers.adult(0, "first_name")]).toContain("te kort");
                expect(errors[FIELD_PATHS.travelers.adult(0, "first_name")]).toContain("3");
            });

            it("should return error when first_name is too long", () => {
                const bookingData = createBookingData({
                    adults: [createValidTraveler({ first_name: "a".repeat(256) })],
                });

                const errors = validator.validateTravelersStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.travelers.adult(0, "first_name"));
                expect(errors[FIELD_PATHS.travelers.adult(0, "first_name")]).toContain("te lang");
                expect(errors[FIELD_PATHS.travelers.adult(0, "first_name")]).toContain("255");
            });

            it("should accept valid first_name", () => {
                const bookingData = createBookingData({
                    adults: [createValidTraveler()],
                });

                const errors = validator.validateTravelersStep(bookingData);

                expect(errors).not.toHaveProperty(FIELD_PATHS.travelers.adult(0, "first_name"));
            });

            it("should trim whitespace from first_name", () => {
                const bookingData = createBookingData({
                    adults: [createValidTraveler({ first_name: "  Jan  " })],
                });

                const errors = validator.validateTravelersStep(bookingData);

                expect(errors).not.toHaveProperty(FIELD_PATHS.travelers.adult(0, "first_name"));
            });
        });

        describe("last_name validation", () => {
            it("should return error when last_name is empty", () => {
                const bookingData = createBookingData({
                    adults: [createValidTraveler({ last_name: "" })],
                });

                const errors = validator.validateTravelersStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.travelers.adult(0, "last_name"));
                expect(errors[FIELD_PATHS.travelers.adult(0, "last_name")]).toContain(
                    "Achternaam ontbreekt"
                );
            });

            it("should return error when last_name is only whitespace", () => {
                const bookingData = createBookingData({
                    adults: [createValidTraveler({ last_name: "   " })],
                });

                const errors = validator.validateTravelersStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.travelers.adult(0, "last_name"));
                expect(errors[FIELD_PATHS.travelers.adult(0, "last_name")]).toContain("ontbreekt");
            });

            it("should return error when last_name is too short", () => {
                const bookingData = createBookingData({
                    adults: [createValidTraveler({ last_name: "Li" })],
                });

                const errors = validator.validateTravelersStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.travelers.adult(0, "last_name"));
                expect(errors[FIELD_PATHS.travelers.adult(0, "last_name")]).toContain("te kort");
            });

            it("should return error when last_name is too long", () => {
                const bookingData = createBookingData({
                    adults: [createValidTraveler({ last_name: "a".repeat(256) })],
                });

                const errors = validator.validateTravelersStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.travelers.adult(0, "last_name"));
                expect(errors[FIELD_PATHS.travelers.adult(0, "last_name")]).toContain("te lang");
                expect(errors[FIELD_PATHS.travelers.adult(0, "last_name")]).toContain("255");
            });

            it("should accept valid last_name", () => {
                const bookingData = createBookingData({
                    adults: [createValidTraveler()],
                });

                const errors = validator.validateTravelersStep(bookingData);

                expect(errors).not.toHaveProperty(FIELD_PATHS.travelers.adult(0, "last_name"));
            });

            it("should trim whitespace from last_name", () => {
                const bookingData = createBookingData({
                    adults: [createValidTraveler({ last_name: "  Jansen  " })],
                });

                const errors = validator.validateTravelersStep(bookingData);

                expect(errors).not.toHaveProperty(FIELD_PATHS.travelers.adult(0, "last_name"));
            });
        });

        describe("birthdate validation", () => {
            it("should return error when birthdate is missing", () => {
                const bookingData = createBookingData({
                    adults: [createValidTraveler({ birthdate: null })],
                });

                const errors = validator.validateTravelersStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.travelers.adult(0, "birthdate"));
                expect(errors[FIELD_PATHS.travelers.adult(0, "birthdate")]).toBe(
                    "Vul een geldige geboortedatum in."
                );
            });

            it("should return error when birthdate is invalid format", () => {
                const bookingData = createBookingData({
                    adults: [createValidTraveler({ birthdate: "invalid-date" })],
                });

                const errors = validator.validateTravelersStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.travelers.adult(0, "birthdate"));
                expect(errors[FIELD_PATHS.travelers.adult(0, "birthdate")]).toContain("geldig");
            });

            it("should accept valid birthdate format", () => {
                const bookingData = createBookingData({
                    adults: [createValidTraveler()],
                });

                const errors = validator.validateTravelersStep(bookingData);

                expect(errors).not.toHaveProperty(FIELD_PATHS.travelers.adult(0, "birthdate"));
            });
        });

        describe("nationality validation", () => {
            it("should return error when nationality is missing", () => {
                const bookingData = createBookingData({
                    adults: [createValidTraveler({ nationality: "" })],
                });

                const errors = validator.validateTravelersStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.travelers.adult(0, "nationality"));
                expect(errors[FIELD_PATHS.travelers.adult(0, "nationality")]).toContain(
                    "Nationaliteit ontbreekt"
                );
            });

            it("should return error when nationality is too short", () => {
                const bookingData = createBookingData({
                    adults: [createValidTraveler({ nationality: "N" })],
                });

                const errors = validator.validateTravelersStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.travelers.adult(0, "nationality"));
                expect(errors[FIELD_PATHS.travelers.adult(0, "nationality")]).toContain("te kort");
                expect(errors[FIELD_PATHS.travelers.adult(0, "nationality")]).toContain("2");
            });

            it("should accept valid 2-letter nationality code", () => {
                const bookingData = createBookingData({
                    adults: [createValidTraveler()],
                });

                const errors = validator.validateTravelersStep(bookingData);

                expect(errors).not.toHaveProperty(FIELD_PATHS.travelers.adult(0, "nationality"));
            });
        });

        describe("children travelers", () => {
            it("should validate children traveler fields", () => {
                const bookingData = createBookingData({
                    adults: [createValidTraveler()],
                    children: [
                        createValidTraveler({
                            first_name: "Emma",
                            last_name: "Jansen",
                            birthdate: "20-08-2015",
                        }),
                    ],
                });

                const errors = validator.validateTravelersStep(bookingData);

                expect(errors).toEqual({});
            });

            it("should return errors for invalid children traveler", () => {
                const bookingData = createBookingData({
                    adults: [createValidTraveler()],
                    children: [
                        createValidTraveler({
                            first_name: "",
                            birthdate: "20-08-2015",
                        }),
                    ],
                });

                const errors = validator.validateTravelersStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.travelers.child(0, "first_name"));
                expect(errors[FIELD_PATHS.travelers.child(0, "first_name")]).toContain("ontbreekt");
            });

            it("should validate multiple children travelers", () => {
                const bookingData = createBookingData({
                    adults: [createValidTraveler()],
                    children: [
                        createValidTraveler({
                            first_name: "Emma",
                            birthdate: "20-08-2015",
                        }),
                        createValidTraveler({
                            first_name: "Lucas",
                            birthdate: "15-03-2018",
                        }),
                    ],
                });

                const errors = validator.validateTravelersStep(bookingData);

                expect(errors).toEqual({});
            });
        });

        describe("multiple travelers", () => {
            it("should validate all travelers across different types", () => {
                const bookingData = createBookingData({
                    adults: [
                        createValidTraveler(),
                        createValidTraveler({
                            first_name: "",
                            last_name: "Pietersen",
                            birthdate: "15-05-1985",
                            nationality: "BE"
                        }),
                    ],
                    children: [
                        createValidTraveler({
                            first_name: "Emma",
                            birthdate: "20-08-2015"
                        }),
                    ],
                });

                const errors = validator.validateTravelersStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.travelers.adult(1, "first_name"));
                expect(errors).not.toHaveProperty(FIELD_PATHS.travelers.adult(0, "first_name"));
                expect(errors).not.toHaveProperty(FIELD_PATHS.travelers.child(0, "first_name"));
            });

            it("should return empty errors when all travelers are valid", () => {
                const bookingData = createBookingData({
                    adults: [createValidTraveler()],
                });

                const errors = validator.validateTravelersStep(bookingData);

                expect(errors).toEqual({});
            });
        });

        it("should return error when travelers object is missing", () => {
            const errors = validator.validateTravelersStep({});

            expect(errors).toHaveProperty("travelers");
            expect(errors.travelers).toBe("Reizigersgegevens ontbreken.");
        });

        it("should return error when bookingData is null", () => {
            const errors = validator.validateTravelersStep(null);

            expect(errors).toHaveProperty("travelers");
        });
    });

    describe("validateContactStep", () => {
        describe("street validation", () => {
            it("should return error when street is empty", () => {
                const bookingData = createBookingData({
                    contact: createValidContact({ street: "" }),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.contact.street);
                expect(errors[FIELD_PATHS.contact.street]).toContain("Straatnaam ontbreekt");
            });

            it("should return error when street is too short", () => {
                const bookingData = createBookingData({
                    contact: createValidContact({ street: "AB" }),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.contact.street);
                expect(errors[FIELD_PATHS.contact.street]).toContain("te kort");
            });

            it("should accept valid street", () => {
                const bookingData = createBookingData({
                    contact: createValidContact(),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).not.toHaveProperty(FIELD_PATHS.contact.street);
            });
        });

        describe("house_number validation", () => {
            it("should return error when house_number is 0", () => {
                const bookingData = createBookingData({
                    contact: createValidContact({ house_number: "0" }),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.contact.houseNumber);
                expect(errors[FIELD_PATHS.contact.houseNumber]).toBe(
                    "Het huisnummer moet een geldig getal groter dan 0 zijn."
                );
            });

            it("should return error when house_number is negative", () => {
                const bookingData = createBookingData({
                    contact: createValidContact({ house_number: "-5" }),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.contact.houseNumber);
                expect(errors[FIELD_PATHS.contact.houseNumber]).toContain("geldig getal");
            });

            it("should return error when house_number is not a number", () => {
                const bookingData = createBookingData({
                    contact: createValidContact({ house_number: "abc" }),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.contact.houseNumber);
                expect(errors[FIELD_PATHS.contact.houseNumber]).toContain("geldig getal");
            });

            it("should accept valid house_number", () => {
                const bookingData = createBookingData({
                    contact: createValidContact({ house_number: "123" }),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).not.toHaveProperty(FIELD_PATHS.contact.houseNumber);
            });

            it("should accept house_number with suffix", () => {
                const bookingData = createBookingData({
                    contact: createValidContact({ house_number: "123A" }),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).not.toHaveProperty(FIELD_PATHS.contact.houseNumber);
            });
        });

        describe("postal_code validation", () => {
            it("should return error when postal_code is empty", () => {
                const bookingData = createBookingData({
                    contact: createValidContact({ postal_code: "" }),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.contact.postalCode);
                expect(errors[FIELD_PATHS.contact.postalCode]).toContain("Postcode ontbreekt");
            });

            it("should return error when postal_code is invalid format", () => {
                const bookingData = createBookingData({
                    contact: createValidContact({ postal_code: "ABCD" }),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.contact.postalCode);
                expect(errors[FIELD_PATHS.contact.postalCode]).toContain("1234AB");
            });

            it("should accept valid Dutch postal code (no space)", () => {
                const bookingData = createBookingData({
                    contact: createValidContact({ postal_code: "1234AB" }),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).not.toHaveProperty(FIELD_PATHS.contact.postalCode);
            });

            it("should accept valid Dutch postal code (with space)", () => {
                const bookingData = createBookingData({
                    contact: createValidContact({ postal_code: "1234 AB" }),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).not.toHaveProperty(FIELD_PATHS.contact.postalCode);
            });

            it("should accept valid Belgian postal code", () => {
                const bookingData = createBookingData({
                    contact: createValidContact({ postal_code: "1000" }),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).not.toHaveProperty(FIELD_PATHS.contact.postalCode);
            });
        });

        describe("city validation", () => {
            it("should return error when city is empty", () => {
                const bookingData = createBookingData({
                    contact: createValidContact({ city: "" }),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.contact.city);
                expect(errors[FIELD_PATHS.contact.city]).toContain("Plaatsnaam ontbreekt");
            });

            it("should return error when city is too short", () => {
                const bookingData = createBookingData({
                    contact: createValidContact({ city: "AB" }),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.contact.city);
                expect(errors[FIELD_PATHS.contact.city]).toContain("te kort");
            });

            it("should accept valid city", () => {
                const bookingData = createBookingData({
                    contact: createValidContact(),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).not.toHaveProperty(FIELD_PATHS.contact.city);
            });
        });

        describe("email validation", () => {
            it("should return error when email is empty", () => {
                const bookingData = createBookingData({
                    contact: createValidContact({ email: "" }),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.contact.email);
                expect(errors[FIELD_PATHS.contact.email]).toContain("E-mail adres ontbreekt");
            });

            it("should return error when email is invalid", () => {
                const bookingData = createBookingData({
                    contact: createValidContact({ email: "invalid-email" }),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.contact.email);
                expect(errors[FIELD_PATHS.contact.email]).toContain("ongeldig");
            });

            it("should return error when email is missing @", () => {
                const bookingData = createBookingData({
                    contact: createValidContact({ email: "testexample.com" }),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.contact.email);
                expect(errors[FIELD_PATHS.contact.email]).toContain("ongeldig");
            });

            it("should return error when email is missing domain", () => {
                const bookingData = createBookingData({
                    contact: createValidContact({ email: "test@" }),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.contact.email);
                expect(errors[FIELD_PATHS.contact.email]).toContain("ongeldig");
            });

            it("should accept valid email", () => {
                const bookingData = createBookingData({
                    contact: createValidContact({ email: "test@example.com" }),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).not.toHaveProperty(FIELD_PATHS.contact.email);
            });

            it("should accept email containing subdomains", () => {
                const bookingData = createBookingData({
                    contact: createValidContact({ email: "test@mail.example.com" }),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).not.toHaveProperty(FIELD_PATHS.contact.email);
            });
        });

        describe("phone validation", () => {
            it("should return error when phone is empty", () => {
                const bookingData = createBookingData({
                    contact: createValidContact({ phone: "" }),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.contact.phone);
                expect(errors[FIELD_PATHS.contact.phone]).toContain("Telefoonnummer ontbreekt");
            });

            it("should return error when phone is invalid", () => {
                const bookingData = createBookingData({
                    contact: createValidContact({ phone: "123456" }),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).toHaveProperty(FIELD_PATHS.contact.phone);
                expect(errors[FIELD_PATHS.contact.phone]).toContain("ongeldig");
            });

            it("should accept valid Dutch mobile (06)", () => {
                const bookingData = createBookingData({
                    contact: createValidContact({ phone: "0612345678" }),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).not.toHaveProperty(FIELD_PATHS.contact.phone);
            });

            it("should accept valid Dutch mobile (+31)", () => {
                const bookingData = createBookingData({
                    contact: createValidContact({ phone: "+31612345678" }),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).not.toHaveProperty(FIELD_PATHS.contact.phone);
            });

            it("should accept valid Belgian mobile", () => {
                const bookingData = createBookingData({
                    contact: createValidContact({ phone: "+32412345678" }),
                });

                const errors = validator.validateContactStep(bookingData);

                expect(errors).not.toHaveProperty(FIELD_PATHS.contact.phone);
            });
        });

        it("should return multiple errors when multiple fields are invalid", () => {
            const bookingData = createBookingData({
                contact: createValidContact({
                    street: "",
                    house_number: "-5",
                    postal_code: "invalid",
                    city: "AB",
                    email: "invalid-email",
                    phone: "123",
                }),
            });

            const errors = validator.validateContactStep(bookingData);

            expect(Object.keys(errors).length).toBe(6);
            expect(errors).toHaveProperty(FIELD_PATHS.contact.street);
            expect(errors).toHaveProperty(FIELD_PATHS.contact.houseNumber);
            expect(errors).toHaveProperty(FIELD_PATHS.contact.postalCode);
            expect(errors).toHaveProperty(FIELD_PATHS.contact.city);
            expect(errors).toHaveProperty(FIELD_PATHS.contact.email);
            expect(errors).toHaveProperty(FIELD_PATHS.contact.phone);
        });

        it("should return empty errors when all contact fields are valid", () => {
            const bookingData = createBookingData({
                contact: createValidContact(),
            });

            const errors = validator.validateContactStep(bookingData);

            expect(errors).toEqual({});
        });

        it("should return error when contact object is missing", () => {
            const errors = validator.validateContactStep({});

            expect(errors).toHaveProperty("contact");
            expect(errors.contact).toBe("Contactgegevens ontbreken.");
        });

        it("should return error when bookingData is null", () => {
            const errors = validator.validateContactStep(null);

            expect(errors).toHaveProperty("contact");
            expect(errors.contact).toContain("ontbreken");
        });
    });

    describe("validateOverviewStep", () => {
        describe("is_confirmed validation", () => {
            it("should return error when is_confirmed is false", () => {
                const bookingData = {
                    is_confirmed: false,
                    conditions_accepted: true,
                };

                const errors = validator.validateOverviewStep(bookingData);

                expect(errors).toHaveProperty("is_confirmed");
                expect(errors.is_confirmed).toBe("Je moet nog akkoord gaan.");
            });

            it("should accept when is_confirmed is true", () => {
                const bookingData = {
                    is_confirmed: true,
                    conditions_accepted: true,
                };

                const errors = validator.validateOverviewStep(bookingData);

                expect(errors).not.toHaveProperty("is_confirmed");
            });
        });

        describe("conditions_accepted validation", () => {
            it("should return error when conditions_accepted is false", () => {
                const bookingData = {
                    is_confirmed: true,
                    conditions_accepted: false,
                };

                const errors = validator.validateOverviewStep(bookingData);

                expect(errors).toHaveProperty("conditions_accepted");
                expect(errors.conditions_accepted).toBe(
                    "Je moet nog akkoord gaan met de algemene voorwaarden."
                );
            });

            it("should accept when conditions_accepted is true", () => {
                const bookingData = {
                    is_confirmed: true,
                    conditions_accepted: true,
                };

                const errors = validator.validateOverviewStep(bookingData);

                expect(errors).not.toHaveProperty("conditions_accepted");
            });
        });

        describe("combined validation", () => {
            it("should return multiple errors when both are false", () => {
                const bookingData = {
                    is_confirmed: false,
                    conditions_accepted: false,
                };

                const errors = validator.validateOverviewStep(bookingData);

                expect(errors).toHaveProperty("is_confirmed");
                expect(errors).toHaveProperty("conditions_accepted");
                expect(Object.keys(errors).length).toBe(2);
            });

            it("should return empty errors when both are accepted", () => {
                const bookingData = {
                    is_confirmed: true,
                    conditions_accepted: true,
                };

                const errors = validator.validateOverviewStep(bookingData);

                expect(errors).toEqual({});
            });
        });

        describe("bookingData validation", () => {
            it("should return error when bookingData is null", () => {
                const errors = validator.validateOverviewStep(null);

                expect(errors).toHaveProperty("overview");
                expect(errors.overview).toBe("Boekinggegevens ontbreken.");
            });

            it("should return error when bookingData is undefined", () => {
                const errors = validator.validateOverviewStep(undefined);

                expect(errors).toHaveProperty("overview");
                expect(errors.overview).toContain("ontbreken");
            });
        });
    });

    describe("edge cases and defensive programming", () => {
        it("should handle null values gracefully", () => {
            const bookingData = createBookingData({
                adults: [createValidTraveler({
                    first_name: null,
                    last_name: null,
                    birthdate: null,
                    nationality: null,
                })],
            });

            const errors = validator.validateTravelersStep(bookingData);

            expect(errors).toHaveProperty(FIELD_PATHS.travelers.adult(0, "first_name"));
            expect(errors).toHaveProperty(FIELD_PATHS.travelers.adult(0, "last_name"));
            expect(errors).toHaveProperty(FIELD_PATHS.travelers.adult(0, "birthdate"));
            expect(errors).toHaveProperty(FIELD_PATHS.travelers.adult(0, "nationality"));
        });

        it("should handle undefined values gracefully", () => {
            const bookingData = createBookingData({
                adults: [createValidTraveler({
                    first_name: undefined,
                    last_name: undefined,
                    birthdate: undefined,
                    nationality: undefined,
                })],
            });

            const errors = validator.validateTravelersStep(bookingData);

            expect(errors).toHaveProperty(FIELD_PATHS.travelers.adult(0, "first_name"));
            expect(errors).toHaveProperty(FIELD_PATHS.travelers.adult(0, "last_name"));
            expect(errors).toHaveProperty(FIELD_PATHS.travelers.adult(0, "birthdate"));
            expect(errors).toHaveProperty(FIELD_PATHS.travelers.adult(0, "nationality"));
        });

        it("should trim whitespace before validation", () => {
            const bookingData = createBookingData({
                contact: createValidContact({
                    street: "  Hoofdstraat  ",
                    house_number: "123",
                    postal_code: "  1234AB  ",
                    city: "  Amsterdam  ",
                    email: "  test@example.com  ",
                    phone: "  0612345678  ",
                }),
            });

            const errors = validator.validateContactStep(bookingData);

            expect(errors).toEqual({});
        });
    });
});
