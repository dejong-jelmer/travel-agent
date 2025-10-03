// composables/useBookingSteps.js
import { ref, computed, watch } from "vue";
import { emailRegex, phoneRegex, postalCodeRegex } from "@/validators/regex.js";

export const BOOKING_STEPS = {
    TRIP: "trip",
    TRAVELERS: "travelers",
    CONTACT: "contact",
    OVERVIEW: "overview",
};

export function useBookingSteps(booking) {
    // === Step Definitions ===
    const steps = computed(() => [
        {
            id: BOOKING_STEPS.TRIP,
            label: "Reis",
            fields: ["departure_date"],
            validate: () => validateTripStep(booking.value),
        },
        {
            id: BOOKING_STEPS.TRAVELERS,
            label: "Reisgezelschap",
            fields: ["travelers"],
            validate: () => validateTravelersStep(booking.value),
        },
        {
            id: BOOKING_STEPS.CONTACT,
            label: "Contactgegevens",
            fields: ["contact"],
            validate: () => validateContactStep(booking.value),
        },
        {
            id: BOOKING_STEPS.OVERVIEW,
            label: "Bekijken & bevestigen",
            fields: ["confirmed", "conditions"],
            validate: () => validateOverviewStep(booking.value),
        },
    ]);

    // === Validation Functions ===
    function validateTravelersStep(bookingData) {
        const errors = {};

        for (const [type, travelers] of Object.entries(bookingData.travelers)) {
            travelers.forEach((traveler, index) => {
                const basePath = `travelers.${type}.${index}`;

                if (traveler.first_name.length < 3) {
                    errors[`${basePath}.first_name`] =
                        "Voornaam is verplicht — anders kunnen we je ticket niet opmaken.";
                }

                if (traveler.last_name.length < 2) {
                    errors[`${basePath}.last_name`] =
                        "Achternaam ontbreekt — we hebben deze nodig voor de boeking.";
                }

                if (!traveler.birthdate) {
                    errors[`${basePath}.birthdate`] =
                        "Vul een geldige geboortedatum in.";
                }

                if (traveler.nationality.length < 2) {
                    errors[`${basePath}.nationality`] =
                        "De nationaliteit ontbreekt — we hebben deze nodig voor de boeking.";
                }
            });
        }

        return errors;
    }

    function validateContactStep(bookingData) {
        const errors = {};
        const { contact } = bookingData;

        if (contact.street.length < 3) {
            errors["contact.street"] = "Vul een geldige straatnaam in.";
        }

        if (!contact.house_number || contact.house_number <= 0) {
            errors["contact.house_number"] = "Huisnummer ontbreekt.";
        }

        if (!postalCodeRegex.test(contact.postal_code)) {
            errors["contact.postal_code"] = "Vul een correcte postcode in.";
        }

        if (contact.city.length < 3) {
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

        if (!bookingData.confirmed) {
            errors["confirmed"] = "Je moet nog akkoord gaan.";
        }

        if (!bookingData.conditions) {
            errors["conditions"] =
                "Je moet nog akkoord gaan met de algemene voorwaarden.";
        }

        return errors;
    }

    function validateTripStep(bookingData) {
        const errors = {};

        if (!bookingData.departure_date) {
            errors["departure_date"] = "Selecteer een vertrekdatum.";
        }

        return errors;
    }

    // === Step Navigation ===
    const highestReachedStep = ref(0);

    // Helper: Find highest valid step
    function getMaxAllowedStep() {
        for (let i = 0; i < steps.value.length; i++) {
            const step = steps.value[i];
            const errors = step.validate();

            if (Object.keys(errors).length > 0) {
                return i;
            }
        }
        return steps.value.length - 1;
    }

    function detectStepFromErrors(errors) {
        if (!errors || Object.keys(errors).length === 0) {
            return 0;
        }

        const errorKeys = Object.keys(errors);

        return steps.value.findIndex((step) =>
            step.fields.some((field) =>
                errorKeys.some((key) => key.startsWith(field))
            )
        );
    }

    function detectInitialStep() {
        // Check errors
        if (
            booking.value.errors &&
            Object.keys(booking.value.errors).length > 0
        ) {
            const errorStep = detectStepFromErrors(booking.value.errors);
            if (errorStep !== -1) return errorStep;
        }

        // Check URL
        const params = new URLSearchParams(window.location.search);
        const urlStep = params.get("step");

        if (urlStep) {
            const requestedIndex = steps.value.findIndex(
                (s) => s.id === urlStep
            );

            if (requestedIndex !== -1) {
                const maxAllowed = getMaxAllowedStep();
                const allowedStep = Math.min(requestedIndex, maxAllowed);
                highestReachedStep.value = Math.max(
                    highestReachedStep.value,
                    allowedStep
                );
                return allowedStep;
            }
        }

        return 0;
    }

    const activeStep = ref(detectInitialStep());

    // Update highest reachable step
    watch(activeStep, (newStep) => {
        highestReachedStep.value = Math.max(highestReachedStep.value, newStep);
    });

    // === URL Sync ===
    function updateUrl(stepId) {
        const url = new URL(window.location.href);
        url.searchParams.set("step", stepId);
        window.history.replaceState({}, "", url);
    }

    watch(activeStep, (newIndex) => {
        if (steps.value[newIndex]) {
            updateUrl(steps.value[newIndex].id);
        }
    });

    // === Validation & Navigation ===
    function validateCurrentStep() {
        const step = steps.value[activeStep.value];
        if (!step) return true; // No step = valid (fail-safe)

        const errors = step.validate();

        // Clear previous errors for this step
        step.fields.forEach((field) => {
            booking.value.clearErrors(field);
        });

        // Set new errors
        Object.entries(errors).forEach(([key, message]) => {
            booking.value.setError(key, message);
        });

        return Object.keys(errors).length === 0;
    }

    function validateAllSteps() {
        let allValid = true;

        steps.value.forEach((step, index) => {
            const errors = step.validate();

            if (Object.keys(errors).length > 0) {
                allValid = false;
                Object.entries(errors).forEach(([key, message]) => {
                    booking.value.setError(key, message);
                });
            }
        });

        return allValid;
    }

    function nextStep() {
        if (!validateCurrentStep()) {
            return false;
        }

        if (activeStep.value < steps.value.length - 1) {
            activeStep.value++;
            return true;
        }

        return false;
    }

    function prevStep() {
        if (activeStep.value > 0) {
            activeStep.value--;
            return true;
        }
        return false;
    }

    function goToStep(stepIndex) {
        if (stepIndex < 0 || stepIndex >= steps.value.length) {
            return false;
        }

        // Teruggaan naar eerder bezochte stap: altijd toegestaan
        if (stepIndex <= highestReachedStep.value) {
            activeStep.value = stepIndex;
            return true;
        }

        // Vooruitgaan: valideer alle tussenliggende stappen
        for (let i = activeStep.value; i < stepIndex; i++) {
            const step = steps.value[i];
            const errors = step.validate();

            if (Object.keys(errors).length > 0) {
                // Zet errors
                Object.entries(errors).forEach(([key, message]) => {
                    booking.value.setError(key, message);
                });
                return false;
            }
        }

        activeStep.value = stepIndex;
        return true;
    }

    // Computed: Wich steps are clickable
    const stepStates = computed(() => {
        const maxAllowed = getMaxAllowedStep();

        return steps.value.map((step, index) => ({
            ...step,
            isActive: index === activeStep.value,
            isCompleted: index < activeStep.value,
            isAccessible:
                index <= Math.max(highestReachedStep.value, maxAllowed),
            isLocked: index > Math.max(highestReachedStep.value, maxAllowed),
        }));
    });

    function canSubmit() {
        return validateAllSteps();
    }

    // === Computed Properties ===
    const currentStep = computed(() => steps.value[activeStep.value]);
    const isFirstStep = computed(() => activeStep.value === 0);
    const isLastStep = computed(
        () => activeStep.value === steps.value.length - 1
    );
    const progress = computed(
        () => ((activeStep.value + 1) / steps.value.length) * 100
    );

    return {
        // State
        steps,
        stepStates,
        activeStep,
        currentStep,
        highestReachedStep,

        // Navigation
        nextStep,
        prevStep,
        goToStep,
        getMaxAllowedStep,

        // Validation
        validateCurrentStep,
        validateAllSteps,
        canSubmit,

        // Computed
        isFirstStep,
        isLastStep,
        progress,
    };
}
