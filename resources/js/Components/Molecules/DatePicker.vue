<script setup>
import { ref } from 'vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
// import { Calendar } from "@/Icons";

const props = defineProps({
  modelValue: {
    type: [Date, String, null],
    default: null
  },
  minDate: {
    type: [Date, String, null],
    default: null
  }
})

const emit = defineEmits(['update:modelValue'])

const format = (date) => {
  return new Intl.DateTimeFormat('nl-NL', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  }).format(date)
}
</script>

<template>
    <VueDatePicker
        v-model="props.modelValue"
        locale="nl"
        placeholder="Kies een datum"
        :enable-time-picker="false"
        :format="format"
        :min-date="props.minDate || undefined"
        auto-apply
        @update:model-value="val => emit('update:modelValue', val)"
        >
        <template #input-icon>
            <Calendar class="ml-1 h-5 w-auto text-accent-gold" />
        </template>
    </VueDatePicker>
</template>
