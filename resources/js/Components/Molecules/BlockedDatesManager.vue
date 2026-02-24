<script setup>
import { computed, ref } from 'vue'
import { Plus } from 'lucide-vue-next'
import { useI18n } from 'vue-i18n'
import FormFeedback from '../Atoms/FormFeedback.vue'
import { useDateFormatter } from '@/Composables/useDateFormatter'

const { toDateString } = useDateFormatter()

const { t, locale } = useI18n()

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({ dates: [], weekdays: [] }),
    },
    errors: {
        type: Object,
        default: null
    }
})

const emit = defineEmits(['update:modelValue'])

const weekdaystranslated = computed(() => {
    return t('common.weekdays').split('_').map((label, index) => ({ value: index, label }))
})

const dates = computed({
    get: () => props.modelValue?.dates ?? [],
    set: (val) => emit('update:modelValue', { ...props.modelValue, dates: val }),
})

const weekdays = computed({
    get: () => (props.modelValue?.weekdays ?? []).map(Number),
    set: (val) => emit('update:modelValue', { ...props.modelValue, weekdays: val }),
})

// Weekday toggles
function toggleWeekday(day) {
    const current = [...weekdays.value]
    const index = current.indexOf(day)
    if (index >= 0) {
        current.splice(index, 1)
    } else {
        current.push(day)
    }
    weekdays.value = current
}

// Date management
const showDatePicker = ref(false)
const showRangePicker = ref(false)
const newDate = ref(null)
const rangeStart = ref(null)
const rangeEnd = ref(null)

function addDate() {
    if (!newDate.value) return
    dates.value = [...dates.value, toDateString(newDate.value)]
    newDate.value = null
    showDatePicker.value = false
}

function addRange() {
    if (!rangeStart.value || !rangeEnd.value) return
    dates.value = [...dates.value, {
        start: toDateString(rangeStart.value),
        end: toDateString(rangeEnd.value),
    }]
    rangeStart.value = null
    rangeEnd.value = null
    showRangePicker.value = false
}

function removeDate(index) {
    const updated = [...dates.value]
    updated.splice(index, 1)
    dates.value = updated
}

function displayDate(entry) {
    const _locale = locale.value
    if (typeof entry === 'string') {
        return new Intl.DateTimeFormat(_locale, { day: '2-digit', month: 'long', year: 'numeric' }).format(new Date(entry + 'T00:00:00'))
    }
    if (entry.start && entry.end) {
        const fmt = (d) => new Intl.DateTimeFormat(_locale, { day: '2-digit', month: 'short', year: 'numeric' }).format(new Date(d + 'T00:00:00'))
        return `${fmt(entry.start)} — ${fmt(entry.end)}`
    }
    return ''
}
</script>

<template>
    <div class="space-y-6">
        <!-- Weekday toggles -->
        <div>
            <Label>{{ t('forms.trip.fields.blocked_dates.weekdays_label') }}</Label>
            <p class="text-xs text-gray-700/30 mt-1 mb-3">{{ t('forms.trip.fields.blocked_dates.weekdays_help') }}</p>
            <div class="flex flex-wrap gap-2">
                <button v-for="day in weekdaystranslated" :key="day.value" type="button" @click="toggleWeekday(day.value)"
                    class="px-3 py-1.5 rounded-md text-sm font-medium border transition-colors" :class="weekdays.includes(day.value)
                        ? 'bg-status-error/10 border-status-error text-status-error'
                        : 'bg-white border-gray-200 text-gray-700 hover:border-gray-300'">
                    {{ day.label }}
                </button>
            </div>
        </div>

        <!-- Specific dates -->
        <div>
            <Label>{{ t('forms.trip.fields.blocked_dates.dates_label') }}</Label>
            <p class="text-xs text-gray-700/30 mt-1 mb-3">{{ t('forms.trip.fields.blocked_dates.dates_help') }}</p>

            <!-- Existing dates list -->
            <div v-if="dates.length" class="space-y-2 mb-3">
                <template v-for="(entry, index) in dates" :key="index">
                    <div
                        class="flex items-center justify-between p-2 bg-gray-50 rounded-md border border-gray-200 group">
                        <span class="text-sm text-gray-700">{{ displayDate(entry) }}</span>
                        <DeleteButton @delete="removeDate(index)" />
                    </div>
                    <FormFeedback v-if="errors[`blocked_dates.dates.${index}`]"
                        :message="errors[`blocked_dates.dates.${index}`]" />
                </template>
            </div>

            <!-- Add buttons -->
            <div class="flex flex-wrap gap-2">
                <button type="button" @click="showDatePicker = !showDatePicker; showRangePicker = false"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-primary-default bg-primary-default/5 rounded-md border border-primary-default/20 hover:bg-primary-default/10 transition-colors">
                    <Plus class="w-4 h-4" />
                    {{ t('forms.trip.fields.blocked_dates.add_date') }}
                </button>
                <button type="button" @click="showRangePicker = !showRangePicker; showDatePicker = false"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-primary-default bg-primary-default/5 rounded-md border border-primary-default/20 hover:bg-primary-default/10 transition-colors">
                    <Plus class="w-4 h-4" />
                    {{ t('forms.trip.fields.blocked_dates.add_range') }}
                </button>
            </div>

            <!-- Single date picker -->
            <div v-if="showDatePicker" class="mt-3 p-3 bg-gray-50 rounded-md border border-gray-200 space-y-3">
                <DatePicker v-model="newDate" :min-date="new Date()" />
                <div class="flex gap-2">
                    <Button type="button" size="small" @click="addDate" :disabled="!newDate">
                        {{ t('forms.trip.fields.blocked_dates.confirm') }}
                    </Button>
                    <Button type="button" size="small" variant="ghost" @click="showDatePicker = false; newDate = null">
                        {{ t('forms.trip.fields.blocked_dates.cancel') }}
                    </Button>
                </div>
            </div>

            <!-- Range picker -->
            <div v-if="showRangePicker" class="mt-3 p-3 bg-gray-50 rounded-md border border-gray-200 space-y-3">
                <div class="grid grid-cols-1 gap-3">
                    <div>
                        <Label>{{ t('forms.trip.fields.blocked_dates.range_start') }}</Label>
                        <DatePicker v-model="rangeStart" :min-date="new Date()" />
                    </div>
                    <div>
                        <Label>{{ t('forms.trip.fields.blocked_dates.range_end') }}</Label>
                        <DatePicker v-model="rangeEnd" :min-date="rangeStart" />
                    </div>
                </div>
                <div class="flex gap-2">
                    <Button type="button" size="small" @click="addRange" :disabled="!rangeStart || !rangeEnd">
                        {{ t('forms.trip.fields.blocked_dates.confirm') }}
                    </Button>
                    <Button type="button" size="small" variant="ghost"
                        @click="showRangePicker = false; rangeStart = null; rangeEnd = null">
                        {{ t('forms.trip.fields.blocked_dates.cancel') }}
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
