<script setup>
import Feedback from "./Feedback.vue";

defineProps({
    name: String,
    label: String,
    modelValue: {
        type: Array,
        required: true,
    },
    options: {
        type: Array,
        required: true,
    },
    placeholder: {
        type: String,
        required: false,
    },
    feedback: {
        type: [ String, Array ],
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
    emit("update:modelValue", selectedValues);
};
</script>
<template>
    <div>
        <label v-if="showLabel" :for="name" class="form-label">{{ label }}</label>
        <select
            class="form-input"
            :id="name"
            :required="required"
            :multiple="multiple"
            @change="handleChange"
        >
            <option v-if="placeholder" value="" disabled>{{ placeholder }}</option>
            <option
                v-for="option in options"
                :selected="modelValue.includes(option.id)"
                :key="option.id"
                :value="option.id"
            >
                {{ option.name }}
            </option>
        </select>
        <template v-if="feedback">
            <Feedback :message="feedback" />
        </template>
    </div>
</template>
