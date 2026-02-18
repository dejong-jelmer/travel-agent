<script setup>
import { computed, onMounted, watchEffect, ref } from 'vue';
import { Plus, Minus } from 'lucide-vue-next';


const props = defineProps({
    items: Array,
    label: String,
    name: String,
    placeholder: String,
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

watchEffect(() => {
    if (items.value.length === 0 || items.value[items.value.length - 1] !== '') {
        items.value.push('')
    }
})


const handleInput = (index) => {
    const currentValue = items.value[index]
    const isLastItem = index === items.value.length - 1

    if (currentValue !== '' && isLastItem) {
        items.value.push('')
    }
    if (currentValue === '' && items.value.length > 1 && !isLastItem) {
        items.value.splice(index, 1)
    }
}

const deleteItem = (index) => {
    if (items.value.length > 1) {
        items.value.splice(index, 1)
    }
}

</script>
<template>
    <div class="grid gap-2">
        <Label :for="label" :required="required">
            <slot name="label">{{ label }}</slot>
        </Label>
        <div v-for="(item, index) in items" :key="`${name}-${index}`" role="group" class="flex items-start gap-2 group">
            <div class="flex-1">
                <Input
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
