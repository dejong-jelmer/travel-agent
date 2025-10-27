// Composables/useBookingSteps.js
import { ref, computed, watch } from "vue";
import { useBookingValidation } from "@/Composables/useBookingValidation.js";

const {
    validateTravelersStep,
    validateContactStep,
    validateOverviewStep,
    validateTripStep,
} = useBookingValidation();

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
            fields: ["is_confirmed", "conditions_accepted"],
            validate: () => validateOverviewStep(booking.value),
        },
    ]);

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

        // Return to previously visited step: always allowed
        if (stepIndex <= highestReachedStep.value) {
            activeStep.value = stepIndex;
            return true;
        }

        // Moving forward: Validate all intermediate steps
        for (let i = activeStep.value; i < stepIndex; i++) {
            const step = steps.value[i];
            const errors = step.validate();

            if (Object.keys(errors).length > 0) {
                // Set errors
                Object.entries(errors).forEach(([key, message]) => {
                    booking.value.setError(key, message);
                });
                return false;
            }
        }

        activeStep.value = stepIndex;
        return true;
    }

    // Computed: which steps are clickable
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
