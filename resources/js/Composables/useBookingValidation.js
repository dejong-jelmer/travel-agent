// Composables/useBookingValidation.js
import { useDateFormatter } from "@/Composables/useDateFormatter.js";
import { emailRegex, phoneRegex, postalCodeRegex } from "@/validators/regex.js";
const { isValidDate } = useDateFormatter();

const DEFAULT_MIN_STRING_LENGTH = 3;
const MIN_NATIONALITY_LENGTH = 2;

export function useBookingValidation() {
    function validateTravelersStep(bookingData) {
        const errors = {};

        if (!bookingData?.travelers) {
            return { errors: "Reizigersgegevens ontbreken" };
        }

        for (const [type, travelers] of Object.entries(bookingData.travelers)) {
            travelers.forEach((traveler, index) => {
                const basePath = `travelers.${type}.${index}`;

                if (!traveler.first_name || traveler.first_name.trim() === "") {
                    errors[`${basePath}.first_name`] =
                        "Voornaam ontbreekt — anders kunnen we je ticket niet opmaken.";
                } else if (traveler.first_name.length < DEFAULT_MIN_STRING_LENGTH) {
                    errors[`${basePath}.first_name`] =
                        `Voornaam is te kort — vul minimaal ${DEFAULT_MIN_STRING_LENGTH} tekens in.`;
                }

                if (!traveler.last_name || traveler.last_name.trim() === "") {
                    errors[`${basePath}.last_name`] =
                        "Achternaam ontbreekt — we hebben deze nodig voor de boeking.";
                } else if (traveler.last_name.length < DEFAULT_MIN_STRING_LENGTH) {
                    errors[`${basePath}.last_name`] =
                        `Achternaam is te kort — vul minimaal ${DEFAULT_MIN_STRING_LENGTH} tekens in.`;
                }

                if (!traveler.birthdate || !isValidDate(traveler.birthdate)) {
                    errors[`${basePath}.birthdate`] =
                        "Vul een geldige geboortedatum in.";
                }

                if (!traveler.nationality || traveler.nationality.trim() === "") {
                    errors[`${basePath}.nationality`] =
                        "De nationaliteit ontbreekt — we hebben deze nodig voor de boeking.";
                } else if (traveler.nationality.length < MIN_NATIONALITY_LENGTH) {
                    errors[`${basePath}.nationality`] =
                        `Nationaliteit is te kort — vul minimaal ${MIN_NATIONALITY_LENGTH} tekens in.`;
                }
            });
        }

        return errors;
    }

    function validateContactStep(bookingData) {
        const errors = {};

        if (!bookingData?.contact) {
            return { errors: "Contactgegevens ontbreken" };
        }

        const { contact } = bookingData;
        const houseNumber = Number(contact.house_number);

        if (!contact.street || contact.street.trim() === "") {
            errors["contact.street"] = "Straatnaam ontbreekt.";
        } else if (contact.street.length < DEFAULT_MIN_STRING_LENGTH) {
            errors["contact.street"] = `Straatnaam is te kort — vul minimaal ${DEFAULT_MIN_STRING_LENGTH} tekens in.`;
        }

        if (!contact.house_number || contact.house_number.toString().trim() === "") {
            errors["contact.house_number"] = "Huisnummer ontbreekt.";
        } else if (!houseNumber || houseNumber <= 0) {
            errors["contact.house_number"] = "Huisnummer moet groter zijn dan 0.";
        }

        if (!contact.postal_code || contact.postal_code.trim() === "") {
            errors["contact.postal_code"] = "Postcode ontbreekt.";
        } else if (!postalCodeRegex.test(contact.postal_code)) {
            errors["contact.postal_code"] = "Postcode is ongeldig — gebruik het formaat 1234AB.";
        }

        if (!contact.city || contact.city.trim() === "") {
            errors["contact.city"] = "Plaatsnaam ontbreekt.";
        } else if (contact.city.length < DEFAULT_MIN_STRING_LENGTH) {
            errors["contact.city"] = `Plaatsnaam is te kort — vul minimaal ${DEFAULT_MIN_STRING_LENGTH} tekens in.`;
        }

        if (!contact.email || contact.email.trim() === "") {
            errors["contact.email"] = "E-mailadres ontbreekt.";
        } else if (!emailRegex.test(contact.email)) {
            errors["contact.email"] = "E-mailadres is ongeldig.";
        }

        if (!contact.phone || contact.phone.trim() === "") {
            errors["contact.phone"] = "Telefoonnummer ontbreekt.";
        } else if (!phoneRegex.test(contact.phone)) {
            errors["contact.phone"] = "Telefoonnummer is ongeldig.";
        }

        return errors;
    }

    function validateOverviewStep(bookingData) {
        const errors = {};

        if (!bookingData) {
            return { errors: "Booking data ontbreekt" };
        }

        if (!bookingData.is_confirmed) {
            errors["is_confirmed"] = "Je moet nog akkoord gaan.";
        }

        if (!bookingData.conditions_accepted) {
            errors["conditions_accepted"] =
                "Je moet nog akkoord gaan met de algemene voorwaarden.";
        }

        return errors;
    }

    function validateTripStep(bookingData) {
        const errors = {};

        if (!bookingData) {
            return { errors: "Boeking data ontbreekt" };
        }

        if (!bookingData.departure_date) {
            errors["departure_date"] = "Selecteer een vertrekdatum.";
        }

        return errors;
    }

    return {
        validateTravelersStep,
        validateContactStep,
        validateOverviewStep,
        validateTripStep,
    };
}
