<script setup>
import { computed } from 'vue'
import { Plus } from 'lucide-vue-next'
import { useI18n } from 'vue-i18n'
import { useDateFormatter } from '@/Composables/useDateFormatter'

const { toDateString } = useDateFormatter()
const { t } = useI18n()

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    errors: {
        type: Object,
        default: null,
    },
    priceLabelOptions: {
        type: Array,
        default: () => [],
    },
})

const emit = defineEmits(['update:modelValue'])

const prices = computed({
    get: () => props.modelValue ?? [],
    set: (val) => emit('update:modelValue', val),
})

function addRow() {
    prices.value = [...prices.value, {
        base_price_pp: '',
        single_supplement: '',
        valid_from: null,
        valid_until: null,
        label: props.priceLabelOptions[0]?.id ?? '',
    }]
}

function removeRow(index) {
    const updated = [...prices.value]
    updated.splice(index, 1)
    prices.value = updated
}

function updateField(index, field, value) {
    const updated = [...prices.value]
    updated[index] = { ...updated[index], [field]: value }
    prices.value = updated
}

function updateDateField(index, field, value) {
    const updated = [...prices.value]
    updated[index] = { ...updated[index], [field]: value ? toDateString(value) : null }
    prices.value = updated
}
</script>

<template>
    <div class="space-y-4">
        <FormFeedback v-if="errors?.prices" :message="errors.prices" />

        <!-- Existing price rows -->
        <div v-if="prices.length" class="space-y-3">
            <div v-for="(row, index) in prices" :key="index"
                class="p-4 bg-gray-50 rounded-md border border-gray-200 space-y-4 group">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">
                        {{ t('forms.trip.fields.prices.season', { n: index + 1 }) }}
                    </span>
                    <DeleteButton @delete="removeRow(index)" />
                </div>

                <div class="grid grid-cols-1 laptop:grid-cols-2 gap-4">
                    <!-- Valid from -->
                    <div>
                        <Label>{{ t('forms.trip.fields.prices.valid_from.label') }}</Label>
                        <DatePicker
                            :modelValue="row.valid_from"
                            @update:modelValue="updateDateField(index, 'valid_from', $event)"
                            :feedback="errors?.[`prices.${index}.valid_from`]"
                        />
                    </div>

                    <!-- Valid until -->
                    <div>
                        <Label>{{ t('forms.trip.fields.prices.valid_until.label') }}</Label>
                        <DatePicker
                            :modelValue="row.valid_until"
                            :minDate="row.valid_from"
                            @update:modelValue="updateDateField(index, 'valid_until', $event)"
                            :feedback="errors?.[`prices.${index}.valid_until`]"
                        />
                    </div>

                    <!-- Base price per person -->
                    <div>
                        <Input
                            type="number"
                            :name="`prices.${index}.base_price_pp`"
                            :label="t('forms.trip.fields.prices.base_price_pp.label')"
                            :placeholder="t('forms.trip.fields.prices.base_price_pp.placeholder')"
                            :modelValue="row.base_price_pp"
                            @update:modelValue="updateField(index, 'base_price_pp', $event)"
                            :feedback="errors?.[`prices.${index}.base_price_pp`]"
                            step="1"
                            min="0"
                        >
                            <template #prefix>
                                <span class="text-gray-700/30">€</span>
                            </template>
                        </Input>
                    </div>

                    <!-- Single supplement -->
                    <div>
                        <Input
                            type="number"
                            :name="`prices.${index}.single_supplement`"
                            :label="t('forms.trip.fields.prices.single_supplement.label')"
                            :placeholder="t('forms.trip.fields.prices.single_supplement.placeholder')"
                            :modelValue="row.single_supplement"
                            @update:modelValue="updateField(index, 'single_supplement', $event)"
                            :feedback="errors?.[`prices.${index}.single_supplement`]"
                            step="1"
                            min="0"
                        >
                            <template #prefix>
                                <span class="text-gray-700/30">€</span>
                            </template>
                        </Input>
                    </div>

                    <!-- Label / season -->
                    <div class="laptop:col-span-2">
                        <Select
                            :name="`prices.${index}.label`"
                            :label="t('forms.trip.fields.prices.label.label')"
                            :modelValue="row.label"
                            @update:modelValue="updateField(index, 'label', $event)"
                            :options="priceLabelOptions"
                            :feedback="errors?.[`prices.${index}.label`]"
                            :placeholder="t('forms.trip.fields.prices.label.placeholder')"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty state -->
        <p v-else class="text-sm text-gray-700/40">
            {{ t('forms.trip.fields.prices.empty') }}
        </p>

        <!-- Add button -->
        <button type="button" @click="addRow"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-primary-default bg-primary-default/5 rounded-md border border-primary-default/20 hover:bg-primary-default/10 transition-colors">
            <Plus class="w-4 h-4" />
            {{ t('forms.trip.fields.prices.add') }}
        </button>
    </div>
</template>
