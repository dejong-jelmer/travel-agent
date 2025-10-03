<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    modelValue: [String, Number, Array],
    type: String,
    name: String,
    label: String,
    placeholder: String,
    feedback: {
        type: [String, Array],
        required: false,
        default: null,
    },
    required: {
        type: Boolean,
        required: false,
        default: null,
    },
    showLabel: {
        type: Boolean,
        required: false,
        default: true,
    },
});
const emit = defineEmits(['update:modelValue'])

</script>
<template>
    <div>
        <Label v-if="showLabel" :form-field="name">
            {{ label }}
        </Label>
        <input :type="type" :id="name" :value="modelValue" @input="emit('update:modelValue', $event.target.value)"
            class="form-input" :class="!!feedback ? 'ring-[2px] ring-status-error ring-offset-2 bg-status-error/20' : ''"
            :placeholder="!showLabel ? label : placeholder" :required="required" />
        <template v-if="!!feedback">
            <FormFeedback :message="feedback" />
        </template>
    </div>
</template>
