<script setup>
import { computed, toRef } from 'vue';
import { useBookingSteps } from '@/Composables/useBookingSteps.js';
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

const canSubmit = computed(() => {
    return booking.value.is_confirmed && booking.value.conditions_accepted && !booking.value.processing && !booking.value.hasErrors;
});

function handleNext() {
    const success = nextStep();
    if (!success) {
        const firstError = document.querySelector('[data-error="true"]');
        firstError?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

function handleSubmit() {
    if (!validateAllSteps()) {
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
</script>

<template>
    <div class="bg-white rounded-2xl shadow-sm border border-nature-sage/20 overflow-hidden">
        <div class="px-6 pb-4 border-b border-nature-sage/20">
            <div class="flex justify-between mb-2 pt-4">
                <button v-for="(step, index) in stepStates" :key="step.id" type="button" @click="goToStep(index)"
                    :disabled="step.isLocked" :data-testid="`step-button-${step.id}`"
                    class="text-sm font-medium transition-all relative group" :class="[
                        step.isActive && 'text-brand-dark font-bold scale-105',
                        step.isCompleted && 'text-nature-earth',
                        step.isAccessible && !step.isActive && !step.isCompleted && 'text-ui-blue hover:text-brand-dark cursor-pointer',
                        step.isLocked && 'text-ui-blue/30 cursor-not-allowed'
                    ]">

                    {{ step.label }}

                    <!-- Tooltip on hover -->
                    <span v-if="step.isLocked" data-testid="step-tooltip"
                        class="absolute top-full left-1/2 translate-x-[-50%] mt-2
                        px-2 py-1 bg-gray-900 text-white text-xs rounded
                        opacity-0 group-hover:opacity-100 transition-opacity
                        whitespace-nowrap pointer-events-none z-50">
                        Vul eerst de vorige stappen in
                    </span>


                </button>
            </div>

            <div class="h-2 bg-neutral-50 rounded-full overflow-hidden">
                <div class="h-full bg-nature-earth transition-all duration-500 ease-out"
                    data-testid="progress-bar"
                    :style="{ width: progress + '%' }"></div>
            </div>
        </div>
        <div class="p-2 laptop:p-4 min-h-[400px]">
            <Transition mode="out-in" enter-active-class="transition duration-200 ease"
                leave-active-class="transition duration-200 ease" enter-from-class="opacity-0 translate-x-[10px]"
                leave-to-class="opacity-0 -translate-x-[10px]">
                <component :is="currentStepComponent" :booking="booking" :constraints="constraints"
                    v-bind="currentStep.props" />
            </Transition>
        </div>
        <div class="flex justify-between items-center px-6 py-4 border-t border-nature-sage/20 bg-neutral-50">
            <button type="button" data-testid="prev-button"
                class="px-4 py-2 rounded-xl text-brand-dark hover:bg-neutral-100 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                :disabled="isFirstStep" @click="prevStep">
                Vorige
            </button>

            <button v-if="!isLastStep" type="button" data-testid="next-button"
                class="px-6 py-2 rounded-xl bg-nature-earth text-brand-dark font-medium hover:bg-nature-terracotta hover:text-neutral-25 transition-all disabled:hover:text-brand-dark disabled:hover:bg-nature-earth disabled:opacity-40 disabled:cursor-not-allowed"
                :disabled="booking.processing" @click="handleNext">
                Volgende
            </button>

            <button v-else type="button" data-testid="submit-button"
                v-tippy="canSubmit ? 'Nu boeken met betalingsverplichting' : null"
                class="px-6 py-2 rounded-xl flex items-center gap-2 bg-nature-earth text-brand-dark font-medium hover:bg-nature-terracotta hover:text-neutral-25 transition-all disabled:hover:text-brand-dark disabled:hover:bg-nature-earth disabled:opacity-40 disabled:cursor-not-allowed"
                :disabled="!canSubmit || booking.processing" @click="handleSubmit">
                <Spinner v-if="booking.processing" class="size-5 animate-spin" viewBox="0 0 24 24" />
                <span>{{ booking.processing ? 'Bezig met verzenden...' : 'Nu boeken' }}</span>
            </button>
        </div>
    </div>
</template>
