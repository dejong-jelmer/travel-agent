<script setup>
import { ref, computed } from 'vue'
import VueDatePicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'

const props = defineProps({
    modelValue: {
        type: [Date, String, null],
        default: null
    },
    maxDate: {
        type: [Date, String, null],
        default: null
    },
    minDate: {
        type: [Date, String, null],
        default: null
    },
    feedback: {
        type: [String, Array],
        required: false,
    },
})

const emit = defineEmits(['update:modelValue'])

const model = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val),
})

const format = (date) =>
    new Intl.DateTimeFormat('nl-NL', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    }).format(date)
</script>

<template>
    <div>
        <VueDatePicker v-model="model" locale="nl" placeholder="Kies een datum" :enable-time-picker="false"
            teleport="body" :format="format" :min-date="props.minDate || null" :max-date="props.maxDate || null"
            arrow-navigation auto-apply :state="!!feedback ? false : null">
            <template #input-icon>
                <Calendar class="ml-1 h-5 w-auto text-ui-gold" />
            </template>
        </VueDatePicker>
        <template v-if="!!feedback">
            <FormFeedback :message="feedback" />
        </template>
    </div>
</template>
<style>
.dp__input_invalid {
    @apply ring-[2px] ring-status-error ring-offset-2 bg-status-error/20;
}

/* Valid state (optioneel, als je dat wilt gebruiken) */
.dp__input_valid {
    @apply ring-[2px] ring-status-success ring-offset-2;
}
</style>
