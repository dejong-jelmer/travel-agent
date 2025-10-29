/**
 * @fileoverview Complete test suite for BookingForm component
 * Step 3: Full test coverage with all important scenarios
 */

import { describe, it, expect, beforeEach, vi } from "vitest";
import { mount } from "@vue/test-utils";
import { ref, computed } from "vue";
import BookingForm from "@/Components/Organisms/BookingForm.vue";

// ========================================
// MOCK SETUP
// ========================================

// Mock the useBookingSteps composable
const mockNextStep = vi.fn();
const mockPrevStep = vi.fn();
const mockGoToStep = vi.fn();
const mockValidateAllSteps = vi.fn();

// Create a configurable mock that we can update per test
const mockStepsData = {
    currentStepId: ref("trip"),
    isFirstStep: ref(true),
    isLastStep: ref(false),
    progress: ref(25),
    stepStates: ref([
        {
            id: "trip",
            label: "Reis",
            isActive: true,
            isCompleted: false,
            isAccessible: true,
            isLocked: false,
        },
        {
            id: "travelers",
            label: "Reisgezelschap",
            isActive: false,
            isCompleted: false,
            isAccessible: false,
            isLocked: true,
        },
        {
            id: "contact",
            label: "Contactgegevens",
            isActive: false,
            isCompleted: false,
            isAccessible: false,
            isLocked: true,
        },
        {
            id: "overview",
            label: "Bekijken & bevestigen",
            isActive: false,
            isCompleted: false,
            isAccessible: false,
            isLocked: true,
        },
    ]),
};

vi.mock("@/Composables/useBookingSteps.js", () => ({
    useBookingSteps: vi.fn(() => ({
        steps: ref([
            {
                id: "trip",
                label: "Reis",
                fields: ["departure_date"],
                validate: () => ({}),
            },
            {
                id: "travelers",
                label: "Reisgezelschap",
                fields: ["travelers"],
                validate: () => ({}),
            },
            {
                id: "contact",
                label: "Contactgegevens",
                fields: ["contact"],
                validate: () => ({}),
            },
            {
                id: "overview",
                label: "Bekijken & bevestigen",
                fields: ["is_confirmed", "conditions_accepted"],
                validate: () => ({}),
            },
        ]),
        stepStates: mockStepsData.stepStates,
        currentStep: computed(() => ({
            id: mockStepsData.currentStepId.value,
            label: "Current Step",
            validate: () => ({}),
        })),
        nextStep: mockNextStep,
        prevStep: mockPrevStep,
        goToStep: mockGoToStep,
        validateAllSteps: mockValidateAllSteps,
        isFirstStep: mockStepsData.isFirstStep,
        isLastStep: mockStepsData.isLastStep,
        progress: mockStepsData.progress,
    })),
}));

// ========================================
// CONSTANTS
// ========================================

const STEP_INDICES = {
    TRIP: 0,
    TRAVELERS: 1,
    CONTACT: 2,
    OVERVIEW: 3,
};

// ========================================
// HELPER FUNCTIONS
// ========================================

/**
 * Reset step states to initial configuration
 * Prevents test pollution from state mutations
 */
function resetStepStates() {
    mockStepsData.stepStates.value = [
        {
            id: "trip",
            label: "Reis",
            isActive: true,
            isCompleted: false,
            isAccessible: true,
            isLocked: false,
        },
        {
            id: "travelers",
            label: "Reisgezelschap",
            isActive: false,
            isCompleted: false,
            isAccessible: false,
            isLocked: true,
        },
        {
            id: "contact",
            label: "Contactgegevens",
            isActive: false,
            isCompleted: false,
            isAccessible: false,
            isLocked: true,
        },
        {
            id: "overview",
            label: "Bekijken & bevestigen",
            isActive: false,
            isCompleted: false,
            isAccessible: false,
            isLocked: true,
        },
    ];
}

/**
 * Make a specific step accessible for testing
 */
function setStepAccessible(stepIndex) {
    if (mockStepsData.stepStates.value[stepIndex]) {
        mockStepsData.stepStates.value[stepIndex].isAccessible = true;
        mockStepsData.stepStates.value[stepIndex].isLocked = false;
    }
}

// ========================================
// TEST SUITE
// ========================================

describe("BookingForm - Complete Test Suite", () => {
    let wrapper;

    // Factory function to create mock booking data
    const createMockBooking = (overrides = {}) => ({
        departure_date: "2025-05-15",
        travelers: {
            adults: [],
            children: [],
        },
        contact: {
            street: "",
            house_number: "",
            postal_code: "",
            city: "",
            email: "",
            phone: "",
        },
        is_confirmed: false,
        conditions_accepted: false,
        processing: false,
        hasErrors: false,
        errors: {},
        post: vi.fn(),
        clearErrors: vi.fn(),
        setError: vi.fn(),
        ...overrides,
    });

    const mockConstraints = {
        min_adults: 1,
        max_adults: 10,
        max_children: 5,
    };

    // Default mount options
    const defaultMountOptions = {
        global: {
            stubs: {
                Trip: true,
                Travelers: true,
                Contact: true,
                Overview: true,
                Spinner: true,
            },
            directives: {
                tippy: () => {},
            },
        },
    };

    /**
     * Helper to mount BookingForm component with sensible defaults
     * Reduces code duplication across tests
     */
    const mountComponent = (bookingOverrides = {}, mountOptions = {}) => {
        return mount(BookingForm, {
            props: {
                booking: createMockBooking(bookingOverrides),
                constraints: mockConstraints,
            },
            ...defaultMountOptions,
            ...mountOptions,
        });
    };

    beforeEach(() => {
        // Clean up
        if (wrapper) {
            wrapper.unmount();
        }

        // Reset mocks
        vi.clearAllMocks();

        // Reset mock data to initial state
        mockStepsData.currentStepId.value = "trip";
        mockStepsData.isFirstStep.value = true;
        mockStepsData.isLastStep.value = false;
        mockStepsData.progress.value = 25;

        // Reset step states to prevent test pollution
        resetStepStates();

        // Mock route helper
        global.route = vi.fn((name) => `https://example.com/${name}`);

        // Mock scrollIntoView
        Element.prototype.scrollIntoView = vi.fn();

        // Mock document.querySelector
        document.querySelector = vi.fn();
    });

    // ========================================
    // GROUP 1: Basic Rendering Tests
    // ========================================
    describe("Basic Rendering", () => {
        it("should mount successfully", () => {
            wrapper = mountComponent();
            expect(wrapper.exists()).toBe(true);
        });

        it("should render all step navigation buttons", () => {
            wrapper = mountComponent();

            expect(wrapper.text()).toContain("Reis");
            expect(wrapper.text()).toContain("Reisgezelschap");
            expect(wrapper.text()).toContain("Contactgegevens");
            expect(wrapper.text()).toContain("Bekijken & bevestigen");
        });

        it("should render progress bar", () => {
            wrapper = mountComponent();

            const progressBar = wrapper.find('[data-testid="progress-bar"]');
            expect(progressBar.exists()).toBe(true);
            expect(progressBar.attributes("style")).toContain("25%");
        });

        it("should render 'Vorige' and 'Volgende' buttons", () => {
            wrapper = mountComponent();

            const prevButton = wrapper.find('[data-testid="prev-button"]');
            const nextButton = wrapper.find('[data-testid="next-button"]');

            expect(prevButton.exists()).toBe(true);
            expect(prevButton.text()).toBe("Vorige");
            expect(nextButton.exists()).toBe(true);
            expect(nextButton.text()).toBe("Volgende");
        });

        it("should render 'Nu boeken' button on last step", () => {
            // Set to last step
            mockStepsData.isLastStep.value = true;
            mockStepsData.isFirstStep.value = false;

            wrapper = mountComponent();

            const submitButton = wrapper.find('[data-testid="submit-button"]');
            expect(submitButton.exists()).toBe(true);
            expect(submitButton.text()).toContain("Nu boeken");
        });
    });

    // ========================================
    // GROUP 2: Navigation Tests
    // ========================================
    describe("Navigation", () => {
        it("should disable 'Vorige' button on first step", () => {
            wrapper = mountComponent();

            const prevButton = wrapper.find('[data-testid="prev-button"]');
            expect(prevButton.element.disabled).toBe(true);
        });

        it("should call nextStep when 'Volgende' is clicked", async () => {
            mockNextStep.mockReturnValue(true);
            wrapper = mountComponent();

            const nextButton = wrapper.find('[data-testid="next-button"]');
            await nextButton.trigger("click");

            expect(mockNextStep).toHaveBeenCalledTimes(1);
        });

        it("should call prevStep when 'Vorige' is clicked", async () => {
            mockStepsData.isFirstStep.value = false;
            mockPrevStep.mockReturnValue(true);

            wrapper = mountComponent();

            const prevButton = wrapper.find('[data-testid="prev-button"]');
            await prevButton.trigger("click");

            expect(mockPrevStep).toHaveBeenCalledTimes(1);
        });

        it("should call goToStep when step label is clicked", async () => {
            setStepAccessible(STEP_INDICES.TRAVELERS);

            wrapper = mountComponent();

            const travelersButton = wrapper.find(
                '[data-testid="step-button-travelers"]'
            );
            await travelersButton.trigger("click");

            expect(mockGoToStep).toHaveBeenCalledWith(STEP_INDICES.TRAVELERS);
        });

        it("should disable locked steps", () => {
            wrapper = mountComponent();

            const lockedButton = wrapper.find(
                '[data-testid="step-button-travelers"]'
            );

            expect(lockedButton.element.disabled).toBe(true);
        });

        it("should scroll to first error when nextStep fails", async () => {
            mockNextStep.mockReturnValue(false);
            const mockElement = { scrollIntoView: vi.fn() };
            document.querySelector = vi.fn().mockReturnValue(mockElement);

            wrapper = mountComponent();

            const nextButton = wrapper.find('[data-testid="next-button"]');
            await nextButton.trigger("click");

            expect(document.querySelector).toHaveBeenCalledWith(
                '[data-error="true"]'
            );
            expect(mockElement.scrollIntoView).toHaveBeenCalledWith({
                behavior: "smooth",
                block: "center",
            });
        });
    });

    // ========================================
    // GROUP 3: Submit Functionality Tests
    // ========================================
    describe("Submit Functionality", () => {
        it("should disable submit button when canSubmit is false", () => {
            mockStepsData.isLastStep.value = true;

            wrapper = mountComponent({
                is_confirmed: false,
                conditions_accepted: true,
            });

            const submitButton = wrapper.find('[data-testid="submit-button"]');
            expect(submitButton.element.disabled).toBe(true);
        });

        it("should enable submit button when canSubmit is true", () => {
            mockStepsData.isLastStep.value = true;

            wrapper = mountComponent({
                is_confirmed: true,
                conditions_accepted: true,
            });

            const submitButton = wrapper.find('[data-testid="submit-button"]');
            expect(submitButton.element.disabled).toBe(false);
        });

        it("should call validateAllSteps when submit is clicked", async () => {
            mockStepsData.isLastStep.value = true;
            mockValidateAllSteps.mockReturnValue(true);

            wrapper = mountComponent({
                is_confirmed: true,
                conditions_accepted: true,
            });

            const submitButton = wrapper.find('[data-testid="submit-button"]');
            await submitButton.trigger("click");

            expect(mockValidateAllSteps).toHaveBeenCalledTimes(1);
        });

        it("should call booking.post with correct parameters on submit", async () => {
            mockStepsData.isLastStep.value = true;
            mockValidateAllSteps.mockReturnValue(true);

            const mockBooking = createMockBooking({
                is_confirmed: true,
                conditions_accepted: true,
            });

            wrapper = mountComponent({
                is_confirmed: true,
                conditions_accepted: true,
            });

            const submitButton = wrapper.find('[data-testid="submit-button"]');
            await submitButton.trigger("click");

            // Access the booking from props to check the call
            expect(wrapper.props("booking").post).toHaveBeenCalledWith(
                "https://example.com/bookings.store",
                expect.objectContaining({
                    forceFormData: true,
                })
            );
        });

        it("should not call booking.post when validation fails", async () => {
            mockStepsData.isLastStep.value = true;
            mockValidateAllSteps.mockReturnValue(false);

            wrapper = mountComponent({
                is_confirmed: true,
                conditions_accepted: true,
            });

            const submitButton = wrapper.find('[data-testid="submit-button"]');
            await submitButton.trigger("click");

            expect(wrapper.props("booking").post).not.toHaveBeenCalled();
        });

        it("should show spinner when processing", () => {
            mockStepsData.isLastStep.value = true;

            wrapper = mountComponent({
                is_confirmed: true,
                conditions_accepted: true,
                processing: true,
            });

            const submitButton = wrapper.find('[data-testid="submit-button"]');
            expect(submitButton.text()).toContain("Bezig met verzenden...");
        });

        it("should disable submit button when processing", () => {
            mockStepsData.isLastStep.value = true;

            wrapper = mountComponent({
                is_confirmed: true,
                conditions_accepted: true,
                processing: true,
            });

            const submitButton = wrapper.find('[data-testid="submit-button"]');
            expect(submitButton.element.disabled).toBe(true);
        });
    });

    // ========================================
    // GROUP 4: Props Tests
    // ========================================
    describe("Props", () => {
        it("should accept booking prop", () => {
            wrapper = mountComponent();
            expect(wrapper.props("booking")).toBeDefined();
        });

        it("should accept constraints prop", () => {
            wrapper = mountComponent();
            expect(wrapper.props("constraints")).toEqual(mockConstraints);
        });
    });

    // ========================================
    // GROUP 5: Progress Bar Tests
    // ========================================
    describe("Progress Bar", () => {
        it("should show 25% progress on first step", () => {
            mockStepsData.progress.value = 25;
            wrapper = mountComponent();

            const progressBar = wrapper.find('[data-testid="progress-bar"]');
            expect(progressBar.attributes("style")).toContain("width: 25%");
        });

        it("should show 100% progress on last step", () => {
            mockStepsData.progress.value = 100;
            mockStepsData.isLastStep.value = true;

            wrapper = mountComponent();

            const progressBar = wrapper.find('[data-testid="progress-bar"]');
            expect(progressBar.attributes("style")).toContain("width: 100%");
        });
    });

    // ========================================
    // GROUP 6: Step State Styling Tests
    // ========================================
    describe("Step State Styling", () => {
        it("should apply active styling to current step", () => {
            wrapper = mountComponent();

            const activeButton = wrapper.find(
                '[data-testid="step-button-trip"]'
            );

            expect(activeButton.classes()).toContain("text-primary-dark");
        });

        it("should show tooltip on locked steps", () => {
            wrapper = mountComponent();

            const tooltip = wrapper.find('[data-testid="step-tooltip"]');

            expect(tooltip.exists()).toBe(true);
            expect(tooltip.text()).toContain(
                "Vul eerst de vorige stappen in"
            );
        });
    });

    // ========================================
    // GROUP 7: Edge Cases
    // ========================================
    describe("Edge Cases", () => {
        it("should handle booking with errors", () => {
            mockStepsData.isLastStep.value = true;

            wrapper = mountComponent({
                is_confirmed: true,
                conditions_accepted: true,
                hasErrors: true,
                errors: { departure_date: "Required" },
            });

            expect(wrapper.exists()).toBe(true);

            // Submit should be disabled due to hasErrors
            const submitButton = wrapper.find('[data-testid="submit-button"]');
            expect(submitButton.element.disabled).toBe(true);
        });

        it("should handle minimal booking data", () => {
            wrapper = mountComponent({
                departure_date: null,
                travelers: { adults: [], children: [] },
                contact: {},
            });

            expect(wrapper.exists()).toBe(true);
            expect(wrapper.find('[data-testid="prev-button"]').exists()).toBe(
                true
            );
            expect(wrapper.find('[data-testid="next-button"]').exists()).toBe(
                true
            );
        });
    });

    // ========================================
    // GROUP 8: Submit Error Handling
    // ========================================
    describe("Submit Error Handling", () => {
        it("should navigate to error step on server validation errors", async () => {
            mockStepsData.isLastStep.value = true;
            mockValidateAllSteps.mockReturnValue(true);

            // Mock booking.post to trigger onError callback
            const onErrorCallback = vi.fn();
            const mockBooking = createMockBooking({
                is_confirmed: true,
                conditions_accepted: true,
            });

            mockBooking.post = vi.fn((url, options) => {
                // Simulate server error response
                const errors = { "contact.email": "Email is required" };
                options.onError(errors);
            });

            wrapper = mount(BookingForm, {
                props: {
                    booking: mockBooking,
                    constraints: mockConstraints,
                },
                ...defaultMountOptions,
            });

            const submitButton = wrapper.find('[data-testid="submit-button"]');
            await submitButton.trigger("click");

            // Should call goToStep with the contact step index
            expect(mockGoToStep).toHaveBeenCalledWith(STEP_INDICES.CONTACT);
        });

        it("should not navigate if no errors match step fields", async () => {
            mockStepsData.isLastStep.value = true;
            mockValidateAllSteps.mockReturnValue(true);

            const mockBooking = createMockBooking({
                is_confirmed: true,
                conditions_accepted: true,
            });

            mockBooking.post = vi.fn((url, options) => {
                // Simulate error that doesn't match any step field
                const errors = { "unknown_field": "Some error" };
                options.onError(errors);
            });

            wrapper = mount(BookingForm, {
                props: {
                    booking: mockBooking,
                    constraints: mockConstraints,
                },
                ...defaultMountOptions,
            });

            const submitButton = wrapper.find('[data-testid="submit-button"]');
            await submitButton.trigger("click");

            // Should not call goToStep since error doesn't match any step
            expect(mockGoToStep).not.toHaveBeenCalled();
        });
    });
});
