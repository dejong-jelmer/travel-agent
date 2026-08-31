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
import { emailRegex, phoneRegex, postalCodeRegex } from "@/Validators/regex.js";
import i18n from "@/plugins/i18n.js";

const { isValidDate } = useDateFormatter();

/** Minimum length for most string fields (, city, street) */
const MIN_FRIST_NAME_LENGTH = 2;

/** Minimum length for most string fields (last names, city, street) */
const DEFAULT_MIN_STRING_LENGTH = 3;

/** Minimum length for nationality field (2-letter destination codes) */
const MIN_NATIONALITY_LENGTH = 2;

/** Maximum length for most string fields */
const DEFAULT_MAX_STRING_LENGTH = 255;

/** Maximum length for special requests */
const MAX_SPECIAL_REQUESTS_LENGTH = 1000;

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
    const t = (key, params) => i18n.global.t(key, params);

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
            return { travelers: t("validation.errors.missing_traveler_data") };
        }

        for (const [type, travelers] of Object.entries(bookingData.travelers)) {
            travelers.forEach((traveler, index) => {
                const basePath = `travelers.${type}.${index}`;
                if (!traveler || typeof traveler !== "object") {
                    errors[`${basePath}`] = t(
                        "validation.errors.invalid_traveler_data",
                    );
                    return;
                }
                const firstNameError = validateStringField(
                    traveler.first_name,
                    t("validation.fields.first_name"),
                    MIN_FRIST_NAME_LENGTH,
                    t("validation.errors.missing", {
                        field: t("validation.fields.first_name"),
                    }),
                    t("validation.errors.too_short", {
                        field: t("validation.fields.first_name"),
                        min: MIN_FRIST_NAME_LENGTH,
                    }),
                );
                if (firstNameError)
                    errors[`${basePath}.first_name`] = firstNameError;

                const lastNameError = validateStringField(
                    traveler.last_name,
                    t("validation.fields.last_name"),
                    DEFAULT_MIN_STRING_LENGTH,
                    t("validation.errors.missing", {
                        field: t("validation.fields.last_name"),
                    }),
                    t("validation.errors.too_short", {
                        field: t("validation.fields.last_name"),
                        min: DEFAULT_MIN_STRING_LENGTH,
                    }),
                );
                if (lastNameError)
                    errors[`${basePath}.last_name`] = lastNameError;

                if (!traveler.birthdate || !isValidDate(traveler.birthdate)) {
                    errors[`${basePath}.birthdate`] = t(
                        "validation.errors.invalid_birthdate",
                    );
                }

                const specialRequests = traveler.special_requests?.trim() || "";
                if (specialRequests.length > MAX_SPECIAL_REQUESTS_LENGTH) {
                    errors[`${basePath}.special_requests`] = t(
                        "validation.errors.too_long",
                        {
                            field: t("validation.fields.special_requests"),
                            max: MAX_SPECIAL_REQUESTS_LENGTH,
                        },
                    );
                }

                if (
                    specialRequests.length > 0 &&
                    !traveler.special_requests_consent
                ) {
                    errors[`${basePath}.special_requests_consent`] = t(
                        "validation.errors.missing_special_requests_consent",
                    );
                }

                const nationalityError = validateStringField(
                    traveler.nationality,
                    t("validation.fields.nationality"),
                    MIN_NATIONALITY_LENGTH,
                    t("validation.errors.missing", {
                        field: t("validation.fields.nationality"),
                    }),
                    t("validation.errors.too_short", {
                        field: t("validation.fields.nationality"),
                        min: MIN_NATIONALITY_LENGTH,
                    }),
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
            return { contact: t("validation.errors.missing_contact_data") };
        }

        const { contact } = bookingData;
        const houseNumber = parseInt(contact.house_number, 10);

        const streetError = validateStringField(
            contact.street,
            t("validation.fields.street_name"),
            DEFAULT_MIN_STRING_LENGTH,
            t("validation.errors.missing", {
                field: t("validation.fields.street_name"),
            }),
            t("validation.errors.too_short", {
                field: t("validation.fields.street_name"),
                min: MIN_NATIONALITY_LENGTH,
            }),
        );
        if (streetError) errors["contact.street"] = streetError;

        if (isNaN(houseNumber) || houseNumber <= 0) {
            errors["contact.house_number"] = t(
                "validation.errors.invalid_house_number",
            );
        }

        const postalCodeError = validateRegexField(
            contact.postal_code,
            t("validation.fields.postal_code"),
            postalCodeRegex,
            t("validation.errors.missing", {
                field: t("validation.fields.postal_code"),
            }),
            t("validation.errors.invalid_postal_code", {
                field: t("validation.fields.postal_code"),
            }),
        );

        if (postalCodeError) errors["contact.postal_code"] = postalCodeError;

        const cityError = validateStringField(
            contact.city,
            t("validation.fields.city"),
            DEFAULT_MIN_STRING_LENGTH,
            t("validation.errors.missing", {
                field: t("validation.fields.city"),
            }),
            t("validation.errors.too_short", {
                field: t("validation.fields.city"),
                min: DEFAULT_MIN_STRING_LENGTH,
            }),
        );

        if (cityError) errors["contact.city"] = cityError;

        const emailError = validateRegexField(
            contact.email,
            t("validation.fields.email"),
            emailRegex,
            t("validation.errors.missing", {
                field: t("validation.fields.email"),
            }),
            t("validation.errors.invalid", {
                field: t("validation.fields.email"),
            }),
        );

        if (emailError) errors["contact.email"] = emailError;

        const phoneError = validateRegexField(
            contact.phone,
            t("validation.fields.phone"),
            phoneRegex,
            t("validation.errors.missing", {
                field: t("validation.fields.phone"),
            }),
            t("validation.errors.invalid", {
                field: t("validation.fields.phone"),
            }),
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
            return { overview: t("validation.errors.missing_booking_data") };
        }

        if (!bookingData.has_confirmed) {
            errors["has_confirmed"] = t(
                "validation.errors.missing_confirmation",
            );
        }

        if (!bookingData.has_accepted_conditions) {
            errors["has_accepted_conditions"] = t(
                "validation.errors.missing_accepted_conditions",
            );
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
    /**
     * Validates the price step: cost items, margin, optional final price.
     *
     * @param {Object} bookingData
     * @returns {Object}
     */
    function validatePriceStep(bookingData) {
        const errors = {};
        const items = bookingData?.cost_items ?? [];

        if (items.length === 0) {
            errors["cost_items"] = t("validation.errors.missing_cost_items");
        }

        items.forEach((item, index) => {
            if (!item.category) {
                errors[`cost_items.${index}.category`] = t(
                    "validation.errors.missing",
                    {
                        field: t("booking_steps.price.column.category"),
                    },
                );
            }
            const label = item.label?.trim() ?? "";
            if (label === "") {
                errors[`cost_items.${index}.label`] = t(
                    "validation.errors.missing",
                    {
                        field: t("booking_steps.price.column.label"),
                    },
                );
            }
            const amount = Number(item.amount_per_person);
            if (!Number.isFinite(amount) || amount <= 0) {
                errors[`cost_items.${index}.amount_per_person`] = t(
                    "validation.errors.invalid_amount",
                );
            }
            const quantity = Number(item.quantity);
            if (!Number.isInteger(quantity) || quantity < 1 || quantity > 20) {
                errors[`cost_items.${index}.quantity`] = t(
                    "validation.errors.invalid_quantity",
                );
            }
        });

        if (bookingData?.margin_in_percentage === false) {
            const marginAmount = Number(bookingData?.margin_amount);
            if (!Number.isFinite(marginAmount) || marginAmount < 0) {
                errors["margin_amount"] = t("validation.errors.invalid_amount");
            }

            const feePerPerson = Number(bookingData?.fee_per_person);
            if (!Number.isFinite(feePerPerson) || feePerPerson < 0) {
                errors["fee_per_person"] = t("validation.errors.invalid_amount");
            }
        } else {
            const margin = Number(bookingData?.margin_percentage);
            if (!Number.isFinite(margin) || margin < 0 || margin > 95) {
                errors["margin_percentage"] = t(
                    "validation.errors.invalid_margin",
                );
            }
        }

        if (
            bookingData?.final_price !== null &&
            bookingData?.final_price !== "" &&
            bookingData?.final_price !== undefined
        ) {
            const finalPrice = Number(bookingData.final_price);
            if (!Number.isFinite(finalPrice) || finalPrice < 0) {
                errors["final_price"] = t("validation.errors.invalid_amount");
            }
        }

        return errors;
    }

    /**
     * @param {Object} bookingData
     * @param {string} bookingData.departure_date
     * @param {string|null} [bookingData.return_date]
     * @returns {Object.<string, string>} Validation errors
     */
    function validateTripStep(bookingData) {
        const errors = {};

        if (!bookingData) {
            return { trip: t("validation.errors.missing_booking_data") };
        }

        if (!bookingData.departure_date) {
            errors["departure_date"] = t(
                "validation.errors.missing_departure_date",
            );
        }

        // return_date is optional; only validate when a value is present.
        if (bookingData.return_date) {
            if (!bookingData.departure_date) {
                errors["return_date"] = t(
                    "validation.errors.departure_required_for_return",
                );
            } else {
                const departure = new Date(bookingData.departure_date);
                const returnDate = new Date(bookingData.return_date);
                if (returnDate <= departure) {
                    errors["return_date"] = t(
                        "validation.errors.invalid_return_date",
                    );
                }
            }
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
        tooShortMessage,
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
            return t("validation.errors.too_long", {
                field: fieldName,
                max: DEFAULT_MAX_STRING_LENGTH,
            });
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
        invalidMessage,
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
        validatePriceStep,
    };
}
