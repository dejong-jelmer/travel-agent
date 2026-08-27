<script setup>
import { computed, onMounted, watchEffect, ref } from 'vue';
import { Plus, Minus, ChevronUp, ChevronDown } from 'lucide-vue-next';


const props = defineProps({
    items: Array,
    label: String,
    name: String,
    placeholder: String,
    // Optional: renders an input per field, items become objects instead of strings.
    // Shape: [{ key, label, placeholder }]
    fields: {
        type: Array,
        required: false,
        default: null,
    },
    sortable: {
        type: Boolean,
        required: false,
        default: false,
    },
    required: {
        type: Boolean,
        required: false,
        default: null,
    },
    feedback: {
        type: Object,
        required: false,
        default:  () => ({})
    }
})

const emit = defineEmits(['update:items'])
const items = computed({
  get() {
    return props.items
  },
  set(newValue) {
    emit('update:items', newValue)
  }
})

const blankItem = () => props.fields
    ? Object.fromEntries(props.fields.map((field) => [field.key, '']))
    : ''

const isBlank = (item) => props.fields
    ? props.fields.every((field) => !item?.[field.key])
    : item === ''

watchEffect(() => {
    if (items.value.length === 0 || !isBlank(items.value[items.value.length - 1])) {
        items.value.push(blankItem())
    }
})


const handleInput = (index) => {
    const isLastItem = index === items.value.length - 1
    const blank = isBlank(items.value[index])

    if (!blank && isLastItem) {
        items.value.push(blankItem())
    }
    if (blank && items.value.length > 1 && !isLastItem) {
        items.value.splice(index, 1)
    }
}

const deleteItem = (index) => {
    if (items.value.length > 1) {
        items.value.splice(index, 1)
    }
}

const move = (index, offset) => {
    const [item] = items.value.splice(index, 1)
    items.value.splice(index + offset, 0, item)
}

</script>
<template>
    <div class="grid gap-2">
        <Label :for="label" :required="required">
            <slot name="label">{{ label }}</slot>
        </Label>
        <div v-for="(item, index) in items" :key="`${name}-${index}`" role="group" class="flex items-start gap-2 group">
            <div v-if="sortable" class="flex flex-col" :class="{ 'invisible': index === items.length - 1 }">
                <button
                    type="button"
                    :disabled="index === 0"
                    :title="$t('forms.actions.move_up')"
                    :aria-label="$t('forms.actions.move_up')"
                    class="p-1 text-gray-400 hover:text-gray-700 disabled:opacity-30 disabled:hover:text-gray-400 disabled:cursor-not-allowed"
                    @click="move(index, -1)"
                >
                    <ChevronUp class="h-4 w-4" />
                </button>
                <button
                    type="button"
                    :disabled="index >= items.length - 2"
                    :title="$t('forms.actions.move_down')"
                    :aria-label="$t('forms.actions.move_down')"
                    class="p-1 text-gray-400 hover:text-gray-700 disabled:opacity-30 disabled:hover:text-gray-400 disabled:cursor-not-allowed"
                    @click="move(index, 1)"
                >
                    <ChevronDown class="h-4 w-4" />
                </button>
            </div>
            <div class="flex-1" :class="fields ? 'grid gap-2 tablet:grid-cols-2' : ''">
                <template v-if="fields">
                    <Input
                        v-for="field in fields"
                        :key="field.key"
                        type="text"
                        :name="`${name}.${index}.${field.key}`"
                        :label="field.label"
                        :showLabel="false"
                        v-model="items[index][field.key]"
                        :placeholder="field.placeholder"
                        :feedback="feedback[`${name}.${index}.${field.key}`] ?? null"
                        @keyup="handleInput(index)"
                    />
                </template>
                <Input
                    v-else
                    type="text"
                    :name="`${name}.${index}`"
                    :label="label"
                    :showLabel="false"
                    v-model="items[index]"
                    :placeholder="placeholder"
                    :feedback="feedback[`${name}.${index}`] ?? null"
                    @keyup="handleInput(index)"
                />
            </div>
            <DeleteButton
                v-if="items.length > 1 && index !== items.length - 1"
                @delete="deleteItem(index)"
            />
        </div>
    </div>
</template>
