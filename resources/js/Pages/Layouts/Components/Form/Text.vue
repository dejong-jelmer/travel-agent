<script setup>
    import Feedback from "./Feedback.vue";

    defineProps({
        modelValue: String,
        name: String,
        label: String,
        feedback: String,
        rows: {
            type: Number,
            default: 10
        },
    });
</script>
<template>
    <div>
        <label :for="name" class="form-label">{{ label }}</label>
        <textarea :id="name" :value="modelValue"
            :rows="rows"
            @input="$emit('update:modelValue', $event.target.value)"
            class="form-input"
            :placeholder="label" required>
        </textarea>
        <Feedback :message="feedback" />
    </div>
</template>
