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

/** Maximum length for most string fields */
const DEFAULT_MAX_STRING_LENGTH = 255;

/**
 * Error message templates for traveler validation.
 *
 * @constant {Object}
 */
const ERROR_MESSAGES = {
    MISSING: "{field} ontbreekt — deze hebben we nodig voor je boeking.",
    MISSING_TRAVELER_DATA: "Reizigersgegevens ontbreken.",
    MISSING_CONTACT_DATA: "Contactgegevens ontbreken.",
    MISSING_BOOKING_DATA: "Boekinggegevens ontbreken.",
    MISSING_DEPARTURE_DATE: "Kies een vertrekdatum.",
    MISSING_CONFIRMATION: "Je moet nog akkoord gaan.",
    MISSING_ACCEPTED_CONDITIONS:
        "Je moet nog akkoord gaan met de algemene voorwaarden.",
    TOO_SHORT: "{field} is te kort — vul minimaal {min} tekens in.",
    TOO_LONG: `{field} is te lang vul maximaal {max} tekens in.`,
    INVALID: "{field} is ongeldig - voer een geldige waarde in.",
    INVALID_POSTAL_CODE: "{field} is ongeldig — gebruik het formaat 1234AB.",
    INVALID_BIRTHDATE: "Vul een geldige geboortedatum in.",
    INVALID_HOUSE_NUMBER:
        "Het huisnummer moet een geldig getal groter dan 0 zijn.",
};

/**
 * Fieldnames templates.
 *
 * @constant {Object}
 */
const FIELD_NAMES = {
    FIRST_NAME: "Voornaam",
    LAST_NAME: "Achternaam",
    NATIONALITY: "Nationaliteit",
    STREET_NAME: "Straatnaam",
    HOUSE_NUMBER: "Huisnummer",
    POSTAL_CODE: "Postcode",
    CITY: "Plaatsnaam",
    EMAIL: "E-mail adres",
    PHONE: "Telefoonnummer",
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
     *
     * @param {Object} bookingData - The booking data object
     * @returns {Object} Errors object with field paths as keys (e.g., "travelers.adults.0.first_name") and error messages as values
     */
    function validateTravelersStep(bookingData) {
        const errors = {};

        if (!bookingData?.travelers) {
            return { travelers: ERROR_MESSAGES.MISSING_TRAVELER_DATA };
        }

        for (const [type, travelers] of Object.entries(bookingData.travelers)) {
            travelers.forEach((traveler, index) => {
                const basePath = `travelers.${type}.${index}`;
                const firstNameError = validateStringField(
                    traveler.first_name,
                    FIELD_NAMES.FIRST_NAME,
                    DEFAULT_MIN_STRING_LENGTH,
                    ERROR_MESSAGES.MISSING,
                    ERROR_MESSAGES.TOO_SHORT
                );
                if (firstNameError)
                    errors[`${basePath}.first_name`] = firstNameError;

                const lastNameError = validateStringField(
                    traveler.last_name,
                    FIELD_NAMES.LAST_NAME,
                    DEFAULT_MIN_STRING_LENGTH,
                    ERROR_MESSAGES.MISSING,
                    ERROR_MESSAGES.TOO_SHORT
                );
                if (lastNameError)
                    errors[`${basePath}.last_name`] = lastNameError;

                if (!traveler.birthdate || !isValidDate(traveler.birthdate)) {
                    errors[`${basePath}.birthdate`] =
                        ERROR_MESSAGES.INVALID_BIRTHDATE;
                }

                const nationalityError = validateStringField(
                    traveler.nationality,
                    FIELD_NAMES.NATIONALITY,
                    MIN_NATIONALITY_LENGTH,
                    ERROR_MESSAGES.MISSING,
                    ERROR_MESSAGES.TOO_SHORT
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
     * Validates all required contact fields including street address, house number
     *
     * @param {Object} bookingData - The booking data object
     * @returns {Object} Errors object with field paths as keys (e.g., "contact.email") and error messages as values
     */
    function validateContactStep(bookingData) {
        const errors = {};

        if (!bookingData?.contact) {
            return { contact: ERROR_MESSAGES.MISSING_CONTACT_DATA };
        }

        const { contact } = bookingData;
        const houseNumber = parseInt(contact.house_number, 10);

        const streetError = validateStringField(
            contact.street,
            FIELD_NAMES.STREET_NAME,
            DEFAULT_MIN_STRING_LENGTH,
            ERROR_MESSAGES.MISSING,
            ERROR_MESSAGES.TOO_SHORT
        );
        if (streetError) errors["contact.street"] = streetError;

        if (isNaN(houseNumber) || houseNumber <= 0) {
            errors["contact.house_number"] =
                ERROR_MESSAGES.INVALID_HOUSE_NUMBER;
        }

        const postalCodeError = validateRegexField(
            contact.postal_code,
            FIELD_NAMES.POSTAL_CODE,
            postalCodeRegex,
            ERROR_MESSAGES.MISSING,
            ERROR_MESSAGES.INVALID_POSTAL_CODE
        );

        if (postalCodeError) errors["contact.postal_code"] = postalCodeError;

        const cityError = validateStringField(
            contact.city,
            FIELD_NAMES.CITY,
            DEFAULT_MIN_STRING_LENGTH,
            ERROR_MESSAGES.MISSING,
            ERROR_MESSAGES.TOO_SHORT
        );

        if (cityError) errors["contact.city"] = cityError;

        const emailError = validateRegexField(
            contact.email,
            FIELD_NAMES.EMAIL,
            emailRegex,
            ERROR_MESSAGES.MISSING,
            ERROR_MESSAGES.INVALID
        );

        if (emailError) errors["contact.email"] = emailError;

        const phoneError = validateRegexField(
            contact.phone,
            FIELD_NAMES.PHONE,
            phoneRegex,
            ERROR_MESSAGES.MISSING,
            ERROR_MESSAGES.INVALID
        );

        if (phoneError) errors["contact.phone"] = phoneError;

        return errors;
    }

    /**
     * Validates the final overview/confirmation step.
     *
     * Ensures the user has confirmed their booking details and accepted
     * the terms and conditions before submission.
     *
     * @param {Object} bookingData - The booking data object
     * @returns {Object} Errors object with field names as keys and error messages as values
     */
    function validateOverviewStep(bookingData) {
        const errors = {};

        if (!bookingData) {
            return { overview: ERROR_MESSAGES.MISSING_BOOKING_DATA };
        }

        if (!bookingData.is_confirmed) {
            errors["is_confirmed"] = ERROR_MESSAGES.MISSING_CONFIRMATION;
        }

        if (!bookingData.conditions_accepted) {
            errors["conditions_accepted"] =
                ERROR_MESSAGES.MISSING_ACCEPTED_CONDITIONS;
        }

        return errors;
    }

    /**
     * Validates the trip selection step.
     *
     * Ensures a departure date has been selected for the trip.
     *
     * @param {Object} bookingData - The booking data object
     * @returns {Object} Errors object with field names as keys and error messages as values
     */
    function validateTripStep(bookingData) {
        const errors = {};

        if (!bookingData) {
            return { trip: ERROR_MESSAGES.MISSING_BOOKING_DATA };
        }

        if (!bookingData.departure_date) {
            errors["departure_date"] = ERROR_MESSAGES.MISSING_DEPARTURE_DATE;
        }

        return errors;
    }

    /**
     * Validates that a string field is not empty and meets minimum length requirements.
     *
     * @param {string} value - The field value to validate
     * @param {string} fieldName - Display name of the field
     * @param {number} minLength - Minimum required length
     * @param {string} emptyMessage - Error message when empty (use {field} placeholder)
     * @param {string} tooShortMessage - Error message when too short (use {field} and {min} placeholders)
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

        if (trimmed.length > DEFAULT_MAX_STRING_LENGTH) {
            return ERROR_MESSAGES.TOO_LONG.replace(
                "{field}",
                fieldName
            ).replace("{max}", DEFAULT_MAX_STRING_LENGTH);
        }

        return null;
    }

    /**
     * Validates that a string field matches a regex pattern.
     *
     * @param {string} value - The field value to validate
     * @param {string} fieldName - Display name of the field
     * @param {RegExp} regex - Regular expression to test against
     * @param {string} emptyMessage - Error message when empty (use {field} placeholder)
     * @param {string} invalidMessage - Error message when invalid format (use {field} placeholder)
     * @returns {string|null} Error message if validation fails, null if valid
     */
    function validateRegexField(
        value,
        fieldName,
        regex,
        emptyMessage,
        invalidMessage
    ) {
        const trimmed = value?.trim() || "";

        if (trimmed === "") {
            return emptyMessage.replace("{field}", fieldName);
        }

        if (!regex.test(trimmed)) {
            return invalidMessage.replace("{field}", fieldName);
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
