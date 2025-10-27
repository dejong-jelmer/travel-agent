// Composables/useBookingValidation.js
import { ref } from 'vue';
import { useDateFormatter } from "@/Composables/useDateFormatter.js";
import { emailRegex, phoneRegex, postalCodeRegex } from "@/validators/regex.js";
const { isValidDate } = useDateFormatter();

const DEFAULT_MIN_STRING_LENGTH = 3;
const MIN_NATIONALITY_LENGTH = 2;

export function useBookingValidation() {

    function validateTravelersStep(bookingData) {
        const errors = {};

        if (!bookingData?.travelers) {
            return errors;
        }

        for (const [type, travelers] of Object.entries(bookingData.travelers)) {
            travelers.forEach((traveler, index) => {
                const basePath = `travelers.${type}.${index}`;

                if ((traveler.first_name?.length || 0) < DEFAULT_MIN_STRING_LENGTH) {
                    errors[`${basePath}.first_name`] =
                        "Voornaam is verplicht — anders kunnen we je ticket niet opmaken.";
                }

                if ((traveler.last_name?.length || 0) < DEFAULT_MIN_STRING_LENGTH) {
                    errors[`${basePath}.last_name`] =
                        "Achternaam ontbreekt — we hebben deze nodig voor de boeking.";
                }

                if (!isValidDate(traveler.birthdate)) {
                    errors[`${basePath}.birthdate`] =
                        "Vul een geldige geboortedatum in.";
                }

                if ((traveler.nationality?.length || 0) < MIN_NATIONALITY_LENGTH) {
                    errors[`${basePath}.nationality`] =
                        "De nationaliteit ontbreekt — we hebben deze nodig voor de boeking.";
                }
            });
        }

        return errors;
    }

    function validateContactStep(bookingData) {
        const errors = {};

        if (!bookingData?.contact) {
            return errors;
        }
        const { contact } = bookingData;

        if ((contact.street?.length || 0) < DEFAULT_MIN_STRING_LENGTH) {
            errors["contact.street"] = "Vul een geldige straatnaam in.";
        }

        if ((contact.house_number || 0) <= 0) {
            errors["contact.house_number"] = "Huisnummer ontbreekt.";
        }

        if (!postalCodeRegex.test(contact.postal_code)) {
            errors["contact.postal_code"] = "Vul een correcte postcode in.";
        }

        if ((contact.city?.length || 0) < DEFAULT_MIN_STRING_LENGTH) {
            errors["contact.city"] = "Een plaatsnaam ontbreekt.";
        }

        if (!emailRegex.test(contact.email)) {
            errors["contact.email"] = "Vul een geldig e-mailadres in.";
        }

        if (!phoneRegex.test(contact.phone)) {
            errors["contact.phone"] = "Vul een geldig telefoonnummer in.";
        }

        return errors;
    }

    function validateOverviewStep(bookingData) {
        const errors = {};

        if (!bookingData) {
            return errors;
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
            return errors;
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
