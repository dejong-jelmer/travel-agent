/**
 * @fileoverview Booking validation composable for multi-step form validation
 *
 * Provides validation functions for each step of the booking process:
 * - Trip selection (departure date)
 * - Traveler information (names, birthdate, nationality)
 * - Contact details (address, email, phone)
 * - Overview/confirmation (terms acceptance)
 *
 * @module useBookingValidation
 */

import { useDateFormatter } from "@/Composables/useDateFormatter.js";
import { emailRegex, phoneRegex, postalCodeRegex } from "@/validators/regex.js";
const { isValidDate } = useDateFormatter();

/** Minimum length for most string fields (names, city, street) */
const DEFAULT_MIN_STRING_LENGTH = 3;

/** Minimum length for nationality field (2-letter country codes) */
const MIN_NATIONALITY_LENGTH = 2;

/**
 * Array of random reasons shown when traveler fields are missing.
 * Adds variety to error messages to make the form feel more human.
 * @constant {string[]}
 */
const MISSING_REASONS_TRAVELER = [
    "anders kunnen we je ticket niet opmaken",
    "deze hebben we nodig voor je boeking",
    "dit veld is verplicht voor de reservering",
    "zonder deze informatie kunnen we niet verder",
    "deze gegevens zijn noodzakelijk",
];

/**
 * Returns a random reason from MISSING_REASONS_TRAVELER array.
 * Used to generate varied error messages for missing traveler fields.
 *
 * @returns {string} A random missing field reason
 */
function getRandomMissingReason() {
    const randomIndex = Math.floor(
        Math.random() * MISSING_REASONS_TRAVELER.length
    );
    return MISSING_REASONS_TRAVELER[randomIndex];
}

/**
 * Error message templates for traveler validation.
 * MISSING is a function to generate random messages on each call.
 *
 * @constant {Object}
 * @property {Function} MISSING - Returns error message with random reason
 * @property {string} TOO_SHORT - Template for too short field errors
 */
const ERROR_MESSAGES_TRAVELER = {
    MISSING: () => `{field} ontbreekt — ${getRandomMissingReason()}.`,
    TOO_SHORT: "{field} is te kort — vul minimaal {min} tekens in.",
};

/**
 * Error message templates for contact validation.
 * Uses simpler messages without random variations.
 *
 * @constant {Object}
 * @property {string} MISSING - Template for missing field errors
 * @property {string} TOO_SHORT - Template for too short field errors
 */
const ERROR_MESSAGES_CONTACT = {
    MISSING: "{field} ontbreekt.",
    TOO_SHORT: "{field} is te kort — vul minimaal {min} tekens in.",
};

/**
 * Composable for validating booking form data across multiple steps.
 *
 * Provides four validation functions corresponding to the booking form steps:
 * - validateTripStep: Validates departure date selection
 * - validateTravelersStep: Validates traveler details (names, birthdate, nationality)
 * - validateContactStep: Validates contact information (address, email, phone)
 * - validateOverviewStep: Validates final confirmations and terms acceptance
 *
 * @returns {Object} Object containing validation functions
 * @returns {Function} validateTripStep - Validates trip selection
 * @returns {Function} validateTravelersStep - Validates traveler information
 * @returns {Function} validateContactStep - Validates contact details
 * @returns {Function} validateOverviewStep - Validates confirmation checkboxes
 */
export function useBookingValidation() {
    /**
     * Validates traveler information for all travelers in the booking.
     *
     * Checks first name, last name, birthdate, and nationality for each traveler.
     * Travelers are grouped by type (e.g., adults, children) and validated individually.
     * Uses random error messages for missing fields to create a more engaging UX.
     *
     * @param {Object} bookingData - The booking data object
     * @param {Object} bookingData.travelers - Travelers grouped by type (e.g., {adults: [...], children: [...]})
     * @param {Array} bookingData.travelers[type] - Array of traveler objects for each type
     * @param {string} bookingData.travelers[type][].first_name - Traveler's first name
     * @param {string} bookingData.travelers[type][].last_name - Traveler's last name
     * @param {string} bookingData.travelers[type][].birthdate - Traveler's birthdate (validated via isValidDate)
     * @param {string} bookingData.travelers[type][].nationality - Traveler's nationality (min 2 chars)
     *
     * @returns {Object} Errors object with field paths as keys (e.g., "travelers.adults.0.first_name") and error messages as values
     */
    function validateTravelersStep(bookingData) {
        const errors = {};

        if (!bookingData?.travelers) {
            return { errors: "Reizigersgegevens ontbreken" };
        }

        for (const [type, travelers] of Object.entries(bookingData.travelers)) {
            travelers.forEach((traveler, index) => {
                const basePath = `travelers.${type}.${index}`;
                const firstNameError = validateStringField(
                    traveler.first_name,
                    "Voornaam",
                    DEFAULT_MIN_STRING_LENGTH,
                    ERROR_MESSAGES_TRAVELER.MISSING(),
                    ERROR_MESSAGES_TRAVELER.TOO_SHORT
                );
                if (firstNameError)
                    errors[`${basePath}.first_name`] = firstNameError;

                const lastNameError = validateStringField(
                    traveler.last_name,
                    "Achternaam",
                    DEFAULT_MIN_STRING_LENGTH,
                    ERROR_MESSAGES_TRAVELER.MISSING(),
                    ERROR_MESSAGES_TRAVELER.TOO_SHORT
                );
                if (lastNameError)
                    errors[`${basePath}.last_name`] = lastNameError;

                if (!traveler.birthdate || !isValidDate(traveler.birthdate)) {
                    errors[`${basePath}.birthdate`] =
                        "Vul een geldige geboortedatum in.";
                }

                const nationalityError = validateStringField(
                    traveler.nationality,
                    "Nationaliteit",
                    MIN_NATIONALITY_LENGTH,
                    ERROR_MESSAGES_TRAVELER.MISSING(),
                    ERROR_MESSAGES_TRAVELER.TOO_SHORT
                );
                if (nationalityError)
                    errors[`${basePath}.nationality`] = nationalityError;
            });
        }

        return errors;
    }

    /**
     * Validates contact information for the booking.
     *
     * Validates all required contact fields including street address, house number,
     * postal code, city, email, and phone number. Uses regex patterns for
     * postal code, email, and phone validation.
     *
     * @param {Object} bookingData - The booking data object
     * @param {Object} bookingData.contact - Contact information object
     * @param {string} bookingData.contact.street - Street name (min 3 chars)
     * @param {string|number} bookingData.contact.house_number - House number (must be > 0)
     * @param {string} bookingData.contact.postal_code - Postal code (format: 1234AB)
     * @param {string} bookingData.contact.city - City name (min 3 chars)
     * @param {string} bookingData.contact.email - Email address (validated via regex)
     * @param {string} bookingData.contact.phone - Phone number (validated via regex)
     *
     * @returns {Object} Errors object with field paths as keys (e.g., "contact.email") and error messages as values
     */
    function validateContactStep(bookingData) {
        const errors = {};

        if (!bookingData?.contact) {
            return { errors: "Contactgegevens ontbreken" };
        }

        const { contact } = bookingData;
        const houseNumber = parseInt(contact.house_number, 10);

        const streetError = validateStringField(
            contact.street,
            "Straatnaam",
            DEFAULT_MIN_STRING_LENGTH,
            ERROR_MESSAGES_CONTACT.MISSING,
            ERROR_MESSAGES_CONTACT.TOO_SHORT
        );
        if (streetError) errors["contact.street"] = streetError;

        if (isNaN(houseNumber) || houseNumber <= 0) {
            errors["contact.house_number"] =
                "Het huisnummer moet een geldig getal groter dan 0 zijn.";
        }

        if (!contact.postal_code || contact.postal_code.trim() === "") {
            errors["contact.postal_code"] = "Postcode ontbreekt.";
        } else if (!postalCodeRegex.test(contact.postal_code)) {
            errors["contact.postal_code"] =
                "Postcode is ongeldig — gebruik het formaat 1234AB.";
        }

        const cityError = validateStringField(
            contact.city,
            "Plaatsnaam",
            DEFAULT_MIN_STRING_LENGTH,
            ERROR_MESSAGES_CONTACT.MISSING,
            ERROR_MESSAGES_CONTACT.TOO_SHORT
        );

        if (cityError) errors["contact.city"] = cityError;

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

    /**
     * Validates the final overview/confirmation step.
     *
     * Ensures the user has confirmed their booking details and accepted
     * the terms and conditions before submission.
     *
     * @param {Object} bookingData - The booking data object
     * @param {boolean} bookingData.is_confirmed - Whether user confirmed booking details
     * @param {boolean} bookingData.conditions_accepted - Whether user accepted terms and conditions
     *
     * @returns {Object} Errors object with field names as keys and error messages as values
     */
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

    /**
     * Validates the trip selection step.
     *
     * Ensures a departure date has been selected for the trip.
     *
     * @param {Object} bookingData - The booking data object
     * @param {string} bookingData.departure_date - The selected departure date
     *
     * @returns {Object} Errors object with field names as keys and error messages as values
     */
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

    /**
     * Generic string field validator with customizable error messages.
     *
     * Validates that a string field is not empty and meets minimum length requirements.
     * Supports template replacement for {field} and {min} placeholders in error messages.
     *
     * @private
     * @param {string} value - The field value to validate
     * @param {string} fieldName - Display name of the field (used in error messages)
     * @param {number} minLength - Minimum required length for the field
     * @param {string} emptyMessage - Error message template when field is empty (use {field} placeholder)
     * @param {string} tooShortMessage - Error message template when field is too short (use {field} and {min} placeholders)
     *
     * @returns {string|null} Error message if validation fails, null if valid
     */
    function validateStringField(
        value,
        fieldName,
        minLength,
        emptyMessage,
        tooShortMessage
    ) {
        const trimmed = value?.trim() || "";
        if (trimmed === "") {
            return emptyMessage.replace("{field}", fieldName);
        }
        if (trimmed.length < minLength) {
            return tooShortMessage
                .replace("{field}", fieldName)
                .replace("{min}", minLength);
        }
        return null;
    }

    return {
        validateTravelersStep,
        validateContactStep,
        validateOverviewStep,
        validateTripStep,
    };
}
