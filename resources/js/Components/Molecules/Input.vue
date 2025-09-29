<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    modelValue: [String, Number, Array],
    type: String,
    name: String,
    label: String,
    feedback: {
        type: [String, Array],
        required: false,
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
    forceShow: { type: Boolean, default: false }
});
const hideError = ref(false)
const touched = ref(false)
const emit = defineEmits(['update:modelValue'])

const showError = computed(() => (touched.value || props.forceShow) && !!props.feedback && !hideError.value)

function onChange() {

        hideError.value = false

}

function onBlur() {
    touched.value = true
}

</script>
<template>
    <div>
        <Label v-if="showLabel" :form-field="name">
            {{ label }}
        </Label>
        <input :type="type" :id="name" :value="modelValue" @input="emit('update:modelValue', $event.target.value)"
            class="form-input" :class="showError ? 'ring-[2px] ring-status-error ring-offset-2 bg-status-error/20' : ''"
            @blur="onBlur" @keyup.delete="onChange" @keyup="onChange" :placeholder="!showLabel ? label : ''" :required="required" />
        <template v-if="showError">
            <FormFeedback :message="feedback" />
        </template>
    </div>
</template>
