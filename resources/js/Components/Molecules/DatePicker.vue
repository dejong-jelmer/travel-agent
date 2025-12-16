<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3';
import VueDatePicker from '@vuepic/vue-datepicker'
import { CalendarDays } from 'lucide-vue-next'
import '@vuepic/vue-datepicker/dist/main.css'

const page = usePage();


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
    enableTimePicker: {
        type: Boolean,
        default: false,
        required: false,
    },
})

const emit = defineEmits(['update:modelValue'])

const model = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val),
})

const locale = computed(() => { return page.props.locale })

const format = (date) =>
    new Intl.DateTimeFormat('nl-NL', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        ...(props.enableTimePicker ? {
            hour: "numeric",
            minute: "numeric",
        } : {})
    }).format(date)
</script>

<template>
    <div>
        <VueDatePicker v-model="model" :locale="locale" :placeholder="$t('date_picker.placeholder')" :enable-time-picker="enableTimePicker"
            teleport="body" :format="format" :min-date="props.minDate || null" :max-date="props.maxDate || null"
            :state="!!feedback ? false : null" arrow-navigation auto-apply>
            <template #input-icon>
                <CalendarDays class="ml-1 h-5 w-auto text-accent-primary" />
            </template>
        </VueDatePicker>
        <template v-if="!!feedback">
            <FormFeedback :message="feedback" />
        </template>
    </div>
</template>
