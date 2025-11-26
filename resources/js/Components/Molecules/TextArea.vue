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
        default: 5,
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
    <div class="grid gap-1">
        <Label v-if="(label && showLabel) || $slots.label" :for="name" :required="required">
            <slot name="label">{{ label }}</slot>
        </Label>
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
