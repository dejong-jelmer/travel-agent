<script setup>
import { ref } from 'vue'

const props = defineProps({
    trip: {
        type: Object,
        required: true
    },
    booking: {
        type: Object,
        required: true,
    }
})

const emit = defineEmits(['update:booking'])

const updateField = (group, index, field, value) => {
  const updated = { ...props.booking }
  updated.travelers[group][index][field] = value
  emit('update:booking', updated)
}

const activeStep = ref(0)
const steps = [
    { id: 'trip', label: 'Reis' },
    { id: 'group', label: 'Reisgezelschap' },
    { id: 'details', label: 'Contactgegevens' },
    { id: 'book', label: 'Bekijken & bevestigen' }
]

const error = {
    firstName: ''
}

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
</script>

<template>
    <div class="bg-white rounded-2xl shadow-sm border border-secondary-sage/20 overflow-hidden">
        <!-- Progress indicator -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-secondary-sage/20">
            <div class="flex-1">
                <div class="flex justify-between mb-2">
                    <span v-for="(step, index) in steps" :key="step.id" class="text-sm font-medium"
                        :class="index <= activeStep ? 'text-primary-dark' : 'text-secondary-stone'">
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
                <h2 class="text-xl font-bold text-primary-dark">Reis boeken - {{ trip.name }}</h2>
                <p>
                    Wat leuk dat je de reis <strong>{{ trip.name }}</strong> wilt gaan boeken. We gaan een aantal
                    stappen doorlopen om te zorgen dat de boeken goed door komt.
                    Bedenk wel dat het om een <strong>boekingsaanvraag</strong> gaat. Dat betekend dat we eerst zullen
                    kijken of we
                    aan
                    alle wensen kunnen voldoen en of er voldoende beschikbaarheid is. Na het verzenden van de
                    aanvraag
                    nemen we binnen twee werkdagen contact met u op om de boeking te bevestigen.
                </p>
                <hr>
                <div class="grid grid-cols-3 gap-2">
                    <p>Reis</p>
                    <p><strong>{{ trip.name }}</strong></p>
                    <p class="ml-[150px]">Vanaf <strong>€ {{ trip.price }},-</strong> p.p.</p>
                    <p>Kies een datum voor vertrek</p>
                    <DatePicker v-model="props.booking.selectedDate" :min-date="new Date()"
                        @update:model-value="val => emit('update:booking', { ...props.booking, selectedDate: val })" />
                    <p></p>
                    <p>Kies het aantal reizigers</p>
                    <PersonPicker v-model="props.booking.persons"
                        @update:model-value="val => emit('update:booking', { ...props.booking, persons: val })" />
                    <span></span>
                    <!-- <span>Totale reissom</span>
                    <span></span>
                    <p class="ml-[150px]"><strong>€ {{ (props.booking.persons.adults + props.booking.persons.children) * trip.price }},-</strong></p> -->
                </div>
            </div>

            <div v-else-if="steps[activeStep].id === 'group'" class="space-y-6">
                <h2 class="text-xl font-bold text-primary-dark">Reisgezelschap</h2>
                <hr>
                <div v-for="(adult, index) in props.booking.travelers.adults" :key="index"
                    class="space-y-2 p-4 border rounded-lg">
                    <p>Volwassene {{ index+1 }}</p>
                    <Input type="text" name="firstName[]" label="Eerste voornaam (zoals in paspoort)"
                        :showLabel="true" :required="true" v-model="adult.firstName" :feedback="error.firstName"
                        @input="e => updateField('adults', index, 'firstName', e.target.value)"
                        />
                    <Input type="text" name="lastName[]" label="Achternaam (zoals in paspoort)"
                        :showLabel="true" :required="true" v-model="adult.lastName" :feedback="error.lastName"
                        @input="e => updateField('adults', index, 'lastName', e.target.value)"
                        />
                    <DatePicker v-model="adult.birthDate"
                        @update:model-value="val => updateField('adults', index, 'birthDate', val)" />
                    <Input type="text" name="nationality[]" label="Nationaliteit"
                        :showLabel="true" :required="true" v-model="adult.nationality" :feedback="error.nationality"
                        @input="e => updateField('adults', index, 'nationality', e.target.value)"
                        />
                </div>
                <div v-for="(person, key) in ['adults', 'children']" :key="key" class="gap-2">
                </div>
            </div>

            <div v-else-if="steps[activeStep].id === 'details'" class="space-y-6">
                <h2 class="text-xl font-bold text-primary-dark">Contactgegevens</h2>
                <p>Hier voer je de persoonlijke gegevens in.</p>
            </div>

            <div v-else-if="steps[activeStep].id === 'book'" class="space-y-6">
                <h2 class="text-xl font-bold text-primary-dark">Bekijken & bevestigen</h2>
                <p>Overzicht en bevestiging van de boeking.</p>
            </div>
        </div>

        <!-- Navigation buttons -->
        <div class="flex justify-between items-center px-6 py-4 border-t border-secondary-sage/20 bg-neutral-50">
            <button class="px-4 py-2 rounded-xl text-primary-dark hover:bg-neutral-100 disabled:opacity-40"
                :disabled="activeStep === 0" @click="prevStep">
                Vorige
            </button>
            <button
                class="px-6 py-2 rounded-xl bg-accent-earth text-primary-dark hover:bg-accent-terracotta hover:text-neutral-25 transition disabled:opacity-40"
                :disabled="activeStep === steps.length - 1" @click="nextStep">
                Volgende
            </button>
        </div>
    </div>
</template>
