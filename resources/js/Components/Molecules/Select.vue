<script setup>
import FormFeedback from "@/Components/Atoms/FormFeedback.vue";

const props = defineProps({
    name: String,
    label: String,
    modelValue: {
        type: [String, Array],
        required: true,
    },
    options: {
        type: Object,
        required: true,
    },
    placeholder: {
        type: String,
        required: false,
    },
    feedback: {
        type: [String, Array],
        required: false,
    },
    required: {
        type: Boolean,
        required: false,
        default: null,
    },
    multiple: {
        type: Boolean,
        default: false,
    },
    showLabel: {
        type: Boolean,
        required: false,
        default: true,
    },
});
const emit = defineEmits(["update:modelValue"]);
const handleChange = (event) => {
    const selectedValues = Array.from(
        event.target.selectedOptions,
        (option) => option.value
    );

    const value = props.multiple ? selectedValues : (selectedValues[0] ?? null);
    emit("update:modelValue", value);
};

const isSelected = (value) => {
    if (!props.modelValue) return false;
    return Array.isArray(props.modelValue)
        ? props.modelValue.includes(value)
        : props.modelValue === value;
};

</script>
<template>
    <div class="flex flex-col gap-1 w-full">
        <Label v-if="(label && showLabel) || $slots.label" :forField="name" :required="required">
            <slot name="label">{{ label }}</slot>
        </Label>
        <select :class="['form-input', $attrs.class]" :id="props.name" :required="required" :multiple="multiple" @change="handleChange">
            <option v-if="placeholder" value="" disabled>{{ placeholder }}</option>
            <option v-for="(option, index) in options" :selected="isSelected(option['id'])" :key="index"
                :value="option.id" :disabled="option.disabled ?? false" >
                {{ option.name }}
            </option>
        </select>
        <template v-if="feedback">
            <FormFeedback :message="feedback" />
        </template>
    </div>
</template>
