<script setup>
import { ref, computed, toRef, watch } from 'vue'

const props = defineProps({
    booking: { type: Object, required: true },
    validator: { type: Object, required: true }
})

const emit = defineEmits(['bookingCompleted'])
const booking = toRef(props.booking);
const steps = [
    { id: 'trip', label: 'Reis' },
    { id: 'travelerDetails', label: 'Reisgezelschap' },
    { id: 'contactDetails', label: 'Contactgegevens' },
    { id: 'overview', label: 'Bekijken & bevestigen' }
]

// Errors
const hasErrors = (value) => {
    if (Array.isArray(value)) {
        return value.some(v => hasErrors(v))
    }
    if (typeof value === 'object' && value !== null) {
        if (
            typeof value.hasError === 'function'
            && typeof value.message === 'function'
        ) {
            return value.hasError() || !!value.message()
        }
        return Object.values(value).some(v => hasErrors(v))
    }
    return false
}

const tripErrors = () => hasErrors(props.validator.departure_date)
const travelersDetailsErrors = () => hasErrors(props.validator.travelers.adults) || hasErrors(props.validator.travelers.children)
const contactDetailsErrors = () => hasErrors(props.validator.contact)

const bookingFormErrors = computed(() => [
    tripErrors(),
    travelersDetailsErrors(),
    contactDetailsErrors()
])

// Steps
function detectStepFromErrors(errors) {
    if (!errors || Object.keys(errors).length === 0) {
        return 0
    }

    const stepMap = {
        trip: ['departure_date'],
        travelerDetails: [
            'main_booker',
            'travelers.adults',
            'travelers.children'
        ],
        contactDetails: [
            'contact.street',
            'contact.house_number',
            'contact.addition',
            'contact.postal',
            'contact.city',
            'contact.email',
            'contact.phone',
        ],
        overview: ['confirmed']
    }

    const keys = Object.keys(errors)

    for (const [step, fields] of Object.entries(stepMap)) {
        if (keys.some(key => fields.some(field => key.startsWith(field)))) {
            return steps.findIndex(s => s.id === step)
        }
    }
    return 0
}

function detectInitialStep(errors) {
    if (errors && Object.keys(errors).length > 0) {
        return detectStepFromErrors(errors)
    }

    const params = new URLSearchParams(window.location.search)
    const urlStep = params.get('step')
    const index = steps.findIndex(s => s.id === urlStep)

    return index !== -1 ? index : 0
}

const activeStep = ref(detectInitialStep(booking.value.errors))

function updateUrl(stepId) {
    const url = new URL(window.location.href)
    url.searchParams.set('step', stepId)
    window.history.replaceState({}, '', url)
}

watch(activeStep, (newIndex) => {
    updateUrl(steps[newIndex].id)
})

function nextStep() {
    if (activeStep.value < steps.length - 1) {
        activeStep.value++
    }
}

function prevStep() {
    if (activeStep.value > 0) {
        activeStep.value--
    }
}

function toStep(step) {
    if (activeStep.value > step) {
        activeStep.value = step
    }
}

function submit() {
    emit("bookingCompleted")
    booking.value.clearErrors();
    booking.value.post(route("bookings.store"), { forceFormData: true });
}

</script>

<template>
    <div class="bg-white rounded-2xl shadow-sm border border-secondary-sage/20 overflow-hidden">
        <!-- Progress indicator -->
        <div class="flex items-center justify-between px-6 pb-4 border-b border-secondary-sage/20">
            <div class="flex-1">
                <div class="flex justify-between mb-2">
                    <span v-for="(step, index) in steps" :key="step.id" class="text-sm font-medium pt-4 w-full"
                        @click="toStep(index)"
                        :class="index <= activeStep ? 'text-primary-dark cursor-pointer' : 'text-secondary-stone'">
                        {{ step.label }}
                    </span>
                </div>
                <div class="h-2 bg-neutral-50 rounded-full overflow-hidden">
                    <div class="h-2 bg-accent-earth transition-all duration-500"
                        :style="{ width: ((activeStep + 1) / steps.length * 100) + '%' }"></div>
                </div>
            </div>
        </div>

        <!-- Step content -->
        <div class="p-2 laptop:p-4">
            <div v-if="steps[activeStep].id === 'trip'" class="space-y-4">
                <h2 class="text-xl font-bold text-primary-dark">Reis boeken - {{ booking.name }}</h2>
                <p>
                    Wat leuk dat je de reis <strong>{{ booking.name }}</strong> wilt gaan boeken. We gaan een aantal
                    stappen doorlopen om te zorgen dat de boeken goed door komt.
                    Bedenk wel dat het om een <strong>boekingsaanvraag</strong> gaat. Dat betekend dat we eerst zullen
                    kijken of we
                    aan
                    alle wensen kunnen voldoen en of er voldoende beschikbaarheid is. Na het verzenden van de
                    aanvraag
                    nemen we binnen twee werkdagen contact met u op om de boeking te bevestigen.
                </p>
                <hr>
                <Trip v-model:booking="booking" :validator="validator" />
            </div>

            <div v-else-if="steps[activeStep].id === 'travelerDetails'" class="space-y-6">
                <h2 class="text-xl font-bold text-primary-dark">Reisgezelschap</h2>
                <hr>
                <TravelerDetails v-model:booking="booking" :validator="validator.travelers.adults"
                    :max-date="booking.constrains.birthdate" type="adults" label="Volwassene" />
                <TravelerDetails v-model:booking="booking" :validator="validator.travelers.children"
                    :min-date="booking.constrains.birthdate" :max-date="Date()" type="children" label="Kind" />
            </div>

            <div v-else-if="steps[activeStep].id === 'contactDetails'" class="space-y-6">
                <h2 class="text-xl font-bold text-primary-dark">Contactgegevens</h2>
                <ContactDetails v-model:booking="booking" v-model:main_booker="booking.main_booker"
                    :validator="validator.contact" />
            </div>

            <div v-else-if="steps[activeStep].id === 'overview'" class="space-y-6">
                <h2 class="text-xl font-bold text-primary-dark">Bekijken & bevestigen</h2>
                <Overview :booking="booking" />
            </div>
        </div>

        <!-- Navigation buttons -->
        <div class="flex justify-between items-center px-6 py-4 border-t border-secondary-sage/20 bg-neutral-50">
            <button class="px-4 py-2 rounded-xl text-primary-dark hover:bg-neutral-100" :disabled="activeStep === 0"
                @click="prevStep">
                Vorige
            </button>
            <template v-if="activeStep <= steps.length - 2">
                <button
                    class="px-6 py-2 rounded-xl bg-accent-earth text-primary-dark hover:bg-accent-terracotta hover:text-neutral-25 transition disabled:hover:text-primary-dark disabled:hover:bg-accent-earth disabled:opacity-40 disabled:cursor-not-allowed"
                    :disabled="bookingFormErrors[activeStep]" @click="nextStep">
                    Volgende
                </button>
            </template>
            <template v-else>
                <button
                    class="px-6 py-2 rounded-xl flex justify-center bg-accent-earth text-primary-dark hover:bg-accent-terracotta hover:text-neutral-25 transition disabled:hover:text-primary-dark disabled:hover:bg-accent-earth disabled:opacity-40 disabled:cursor-not-allowed"
                    :disabled="!booking.confirmed || booking.processing" @click="submit">
                    <Spinner v-if="booking.processing" class="size-6 animate-spin ..." viewBox="0 0 24 24" />
                    <span v-else>Boekingsaanvraag bevestigen</span>
                </button>
            </template>
        </div>
    </div>
</template>
