<script setup>
import { computed, toRef } from 'vue';
import { useBookingSteps } from '@/Composables/useBookingSteps.js';
import Trip from '@/Components/Organisms/BookingSteps/Trip.vue';
import Travelers from '@/Components/Organisms/BookingSteps/Travelers.vue';
import Contact from '@/Components/Organisms/BookingSteps/Contact.vue';
import Overview from '@/Components/Organisms/BookingSteps/Overview.vue';
import { LoaderCircle, TrainFront, Users, Home, ClipboardList } from 'lucide-vue-next'

const stepIcons = {
    trip: TrainFront,
    travelers: Users,
    contact: Home,
    overview: ClipboardList,
}


const props = defineProps({
    booking: { type: Object, required: true },
    constraints: { type: Object, required: true },
    disabledDates: { type: [Array, Function], default: null },
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
    return booking.value.has_confirmed && booking.value.has_accepted_conditions && !booking.value.processing && !booking.value.hasErrors;
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
    <div class="bg-white rounded-2xl shadow-sm border border-accent-sage/20 overflow-hidden">
        <div class="px-6 pb-4 border-b border-accent-sage/20">
            <div class="flex justify-between mb-2 pt-4">
                <button v-for="(step, index) in stepStates" :key="step.id" type="button" @click="goToStep(index)"
                    :disabled="step.isLocked" :data-testid="`step-button-${step.id}`"
                    class="inline-flex items-center gap-1.5 text-xs tablet:text-sm font-medium transition-all relative group" :class="[
                        step.isActive && 'text-brand-primary font-bold scale-105',
                        step.isCompleted && 'text-accent-primary font-bold',
                        step.isAccessible && !step.isActive && !step.isCompleted && 'text-brand-light hover:text-brand-primary cursor-pointer',
                        step.isLocked && 'text-brand-light/30 cursor-not-allowed'
                    ]">

                    <component :is="stepIcons[step.id]" class="w-5 h-5 shrink-0" />
                    <span class="hidden tablet:inline">{{ step.label }}</span>

                    <!-- Tooltip on hover -->
                    <span v-if="step.isLocked" data-testid="step-tooltip"
                        class="absolute top-full left-1/2 translate-x-[-50%] mt-2
                        px-2 py-1 bg-gray-900 text-white text-xs rounded
                        opacity-0 group-hover:opacity-100 transition-opacity
                        whitespace-nowrap pointer-events-none z-50">
                        {{ $t('forms.booking.tooltip_locked') }}
                    </span>


                </button>
            </div>

            <div class="h-2 bg-white rounded-full overflow-hidden">
                <div class="h-full bg-status-success transition-all duration-500 ease-out"
                    data-testid="progress-bar"
                    :style="{ width: progress + '%' }"></div>
            </div>
        </div>
        <div class="p-2 laptop:p-4 min-h-[400px]">
            <Transition mode="out-in" enter-active-class="transition duration-200 ease"
                leave-active-class="transition duration-200 ease" enter-from-class="opacity-0 translate-x-[10px]"
                leave-to-class="opacity-0 -translate-x-[10px]">
                <component :is="currentStepComponent" :booking="booking" :constraints="constraints"
                    :disabled-dates="disabledDates" v-bind="currentStep.props" />
            </Transition>
        </div>
        <div class="flex justify-between items-center px-6 py-4 border-t border-accent-sage/20 bg-white">
            <button type="button" data-testid="prev-button"
                class="px-4 py-2 rounded-xl text-brand-primary hover:bg-neutral-100 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                :disabled="isFirstStep" @click="prevStep">
                {{ $t('forms.booking.button_prev') }}
            </button>

            <button v-if="!isLastStep" type="button" data-testid="next-button"
                class="px-6 py-2 rounded-xl bg-accent-primary text-white font-medium hover:bg-white hover:text-brand-primary border border-transparent hover:border-brand-primary transition-all disabled:hover:text-brand-primary disabled:hover:bg-accent-earth disabled:opacity-40 disabled:cursor-not-allowed"
                :disabled="booking.processing" @click="handleNext">
                {{ $t('forms.booking.button_next') }}
            </button>

            <button v-else type="button" data-testid="submit-button"
                v-tippy="canSubmit ? $t('forms.booking.button_submit_tooltip') : null"
                class="px-6 py-2 rounded-xl inline-flex gap-2 items-center bg-accent-primary text-white font-medium hover:bg-white hover:text-brand-primary border border-transparent hover:border-brand-primary transition-all disabled:opacity-40 disabled:hover:bg-accent-primary disabled:hover:text-white disabled:cursor-not-allowed"
                :disabled="!canSubmit || booking.processing" @click="handleSubmit">
                <LoaderCircle v-if="booking.processing" class="size-5 animate-spin" viewBox="0 0 24 24" />
                <span>{{ booking.processing ? $t('forms.booking.submitting') : $t('forms.booking.button_submit') }}</span>
            </button>
        </div>
    </div>
</template>
