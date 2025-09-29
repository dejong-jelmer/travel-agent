<script setup>

defineProps({
    modelValue: String,
    name: String,
    label: String,
    feedback: {
        type: [ String, Array ],
        required: false,
    },
    rows: {
        type: Number,
        default: 10,
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
</script>
<template>
    <div>
        <Label v-if="showLabel" :form-field="name">{{ label }}</Label>
        <textarea
            :id="name"
            :value="modelValue"
            :rows="rows"
            @input="$emit('update:modelValue', $event.target.value)"
            class="form-input"
            :placeholder="!showLabel ? label : ''"
            :required="required"
        >
        </textarea>
        <FormFeedback :message="feedback" />
    </div>
</template>
