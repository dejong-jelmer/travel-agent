<script setup>
import { computed, onMounted, watchEffect, ref } from 'vue';
import { Plus, Minus, ChevronUp, ChevronDown } from '@lucide/vue';


const props = defineProps({
    items: Array,
    label: String,
    name: String,
    placeholder: String,
    // Optional: renders an input per field, items become objects instead of strings.
    // Shape: [{ key, label, placeholder }], where placeholder can also be a function that gets the row's item
    // A field of type 'icon' renders an IconSelect instead: [{ key, label, type: 'icon', options, default, iconComponent }].
    // It starts on its default and does not count when deciding whether a row is blank.
    fields: {
        type: Array,
        required: false,
        default: null,
    },
    // Optional: grid classes for the fields of a row
    fieldsClass: {
        type: String,
        required: false,
        default: 'tablet:grid-cols-2',
    },
    sortable: {
        type: Boolean,
        required: false,
        default: false,
    },
    // Optional: maximum number of filled rows; no empty row is offered once it is reached.
    max: {
        type: Number,
        required: false,
        default: null,
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
    ? Object.fromEntries(props.fields.map((field) => [field.key, field.default ?? '']))
    : ''

const isBlank = (item) => props.fields
    ? props.fields.every((field) => field.type === 'icon' || !item?.[field.key])
    : item === ''

const atMax = () => props.max !== null
    && items.value.filter((item) => !isBlank(item)).length >= props.max

// The empty row at the end that invites the next entry; absent once the maximum is reached.
const isTrailingBlank = (index) => index === items.value.length - 1 && isBlank(items.value[index])

// Index of the last row holding content, which cannot move further down.
const lastFilledIndex = () => items.value.length - (isTrailingBlank(items.value.length - 1) ? 2 : 1)

watchEffect(() => {
    if (items.value.length === 0 || (!isBlank(items.value[items.value.length - 1]) && !atMax())) {
        items.value.push(blankItem())
    }
})


const handleInput = (index) => {
    const isLastItem = index === items.value.length - 1
    const blank = isBlank(items.value[index])

    if (!blank && isLastItem && !atMax()) {
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
        <!-- The border turns red while the delete button is hovered, matching the button's own hover color -->
        <div v-for="(item, index) in items" :key="`${name}-${index}`" role="group" class="flex items-start gap-2 p-4 group border border-transparent hover:border-gray-400/50 has-[[data-delete]:hover]:border-red-500 rounded-lg">
            <div v-if="sortable" class="flex flex-col" :class="{ 'invisible': isTrailingBlank(index) }">
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
                    :disabled="index >= lastFilledIndex()"
                    :title="$t('forms.actions.move_down')"
                    :aria-label="$t('forms.actions.move_down')"
                    class="p-1 text-gray-400 hover:text-gray-700 disabled:opacity-30 disabled:hover:text-gray-400 disabled:cursor-not-allowed"
                    @click="move(index, 1)"
                >
                    <ChevronDown class="h-4 w-4" />
                </button>
            </div>
            <div class="flex-1" :class="fields ? `grid gap-2 ${fieldsClass}` : ''">
                <template v-if="fields">
                    <template v-for="field in fields" :key="field.key">
                        <IconSelect
                            v-if="field.type === 'icon'"
                            :label="field.label"
                            :options="field.options"
                            :icon-component="field.iconComponent"
                            v-model="items[index][field.key]"
                            :feedback="feedback[`${name}.${index}.${field.key}`] ?? null"
                        />
                        <Input
                            v-else
                            type="text"
                            :name="`${name}.${index}.${field.key}`"
                            :label="field.label"
                            :showLabel="false"
                            v-model="items[index][field.key]"
                            :placeholder="typeof field.placeholder === 'function' ? field.placeholder(item) : field.placeholder"
                            :feedback="feedback[`${name}.${index}.${field.key}`] ?? null"
                            @keyup="handleInput(index)"
                        />
                    </template>
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
                v-if="items.length > 1 && !isTrailingBlank(index)"
                data-delete
                @delete="deleteItem(index)"
            />
        </div>
    </div>
</template>
