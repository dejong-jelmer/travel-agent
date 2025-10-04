<template>
    <div class="bg-white rounded-2xl shadow-sm border border-secondary-sage/20 overflow-hidden">
        <!-- Progress indicator (zoals eerder besproken) -->
        <div class="px-6 pb-4 border-b border-secondary-sage/20">
            <div class="flex justify-between mb-2 pt-4">
                <button v-for="(step, index) in stepStates" :key="step.id" type="button" @click="goToStep(index)"
                    :disabled="step.isLocked" class="text-sm font-medium transition-all relative group" :class="[
                        step.isActive && 'text-primary-dark font-bold scale-105',
                        step.isCompleted && 'text-accent-earth',
                        step.isAccessible && !step.isActive && !step.isCompleted && 'text-secondary-stone hover:text-primary-dark cursor-pointer',
                        step.isLocked && 'text-secondary-stone/30 cursor-not-allowed'
                    ]">

                    {{ step.label }}

                    <!-- Tooltip on hover -->
                    <span v-if="step.isLocked" class="absolute top-full left-1/2 translate-x-[-50%] mt-2
                        px-2 py-1 bg-gray-900 text-white text-xs rounded
                        opacity-0 group-hover:opacity-100 transition-opacity
                        whitespace-nowrap pointer-events-none z-50">
                        Vul eerst de vorige stappen in
                    </span>


                </button>
            </div>

            <div class="h-2 bg-neutral-50 rounded-full overflow-hidden">
                <div class="h-full bg-accent-earth transition-all duration-500 ease-out"
                    :style="{ width: progress + '%' }"></div>
            </div>
        </div>

        <!-- Step content -->
        <div class="p-2 laptop:p-4 min-h-[400px]">
            <!-- Trip Step -->
            <Transition name="fade" mode="out-in">
                <component :is="currentStepComponent" :booking="booking" :constraints="constraints"
                    v-bind="currentStep.props" />
            </Transition>
        </div>

        <!-- Navigation buttons -->
        <div class="flex justify-between items-center px-6 py-4 border-t border-secondary-sage/20 bg-neutral-50">
            <button type="button"
                class="px-4 py-2 rounded-xl text-primary-dark hover:bg-neutral-100 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                :disabled="isFirstStep" @click="prevStep">
                Vorige
            </button>

            <!-- Next button for non-final steps -->
            <button v-if="!isLastStep" type="button"
                class="px-6 py-2 rounded-xl bg-accent-earth text-primary-dark font-medium hover:bg-accent-terracotta hover:text-neutral-25 transition-all disabled:hover:text-primary-dark disabled:hover:bg-accent-earth disabled:opacity-40 disabled:cursor-not-allowed"
                :disabled="booking.processing" @click="handleNext">
                Volgende
            </button>

            <!-- Submit button for final step -->
            <button v-else type="button"
                v-tippy="canSubmit ? 'Nu boeken met betalingsverplichting' : null"
                class="px-6 py-2 rounded-xl flex items-center gap-2 bg-accent-earth text-primary-dark font-medium hover:bg-accent-terracotta hover:text-neutral-25 transition-all disabled:hover:text-primary-dark disabled:hover:bg-accent-earth disabled:opacity-40 disabled:cursor-not-allowed"
                :disabled="!canSubmit || booking.processing" @click="handleSubmit">
                <Spinner v-if="booking.processing" class="size-5 animate-spin" viewBox="0 0 24 24" />
                <span>{{ booking.processing ? 'Bezig met verzenden...' : 'Nu boeken' }}</span>
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed, toRef, watch } from 'vue';
import { useBookingSteps } from '@/composables/useBookingSteps.js';
import Trip from '@/Components/Organisms/BookingSteps/Trip.vue';
import Travelers from '@/Components/Organisms/BookingSteps/Travelers.vue';
import Contact from '@/Components/Organisms/BookingSteps/Contact.vue';
import Overview from '@/Components/Organisms/BookingSteps/Overview.vue';


const props = defineProps({
    booking: { type: Object, required: true },
    constraints: { type: Object, required: true }
});

const booking = toRef(props.booking)

const emit = defineEmits(['bookingCompleted']);

const {
    steps,
    stepStates,
    currentStep,
    nextStep,
    prevStep,
    goToStep,
    validateAllSteps,
    isFirstStep,
    isLastStep,
    progress
} = useBookingSteps(booking);

const stepComponents = {
    trip: Trip,
    travelers: Travelers,
    contact: Contact,
    overview: Overview
};

const currentStepComponent = computed(() => stepComponents[currentStep.value.id]);

// Computed property for submit button state
const canSubmit = computed(() => {
    return booking.value.confirmed && booking.value.conditions && !booking.value.processing && !booking.value.hasErrors;
});

function handleNext() {
    const success = nextStep();
    if (!success) {
        // Scroll to first error
        const firstError = document.querySelector('[data-error="true"]');
        firstError?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

function handleSubmit() {
    if (!validateAllSteps()) {
        // Go to first step with errors
        const errorStep = steps.value.findIndex(step => {
            const errors = step.validate();
            return Object.keys(errors).length > 0;
        });

        if (errorStep !== -1) {
            goToStep(errorStep);
        }
        return;
    }

    booking.value.post(route('bookings.store'), {
        forceFormData: true,
        onSuccess: () => { },
        onError: (errors) => {
            // Ga to step with server errors
            const errorKeys = Object.keys(errors);
            const errorStep = steps.value.findIndex(step =>
                step.fields.some(field =>
                    errorKeys.some(key => key.startsWith(field))
                )
            );

            if (errorStep !== -1) {
                goToStep(errorStep);
            }
        }
    });
}

// Test for development only:
// if (import.meta.env.DEV) {
//     watch(
//         () => [booking.value.participants, booking.value.errors],
//         ([participants, errors]) => {
//             console.group('📊 Booking State');
//             console.log('Participants:', participants);
//             console.log('Travelers:', {
//                 adults: booking.value.travelers.adults.length,
//                 children: booking.value.travelers.children.length
//             });
//             console.log('Errors:', errors);
//             console.groupEnd();
//         },
//         { deep: true }
//     );
// }
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.fade-enter-from {
    opacity: 0;
    transform: translateX(10px);
}

.fade-leave-to {
    opacity: 0;
    transform: translateX(-10px);
}
</style>
