<script setup>
    import Feedback from "./Feedback.vue";

    defineProps({
        modelValue: [String, Number],
        type: String,
        name: String,
        label: String,
        feedback: {
            type: String,
            required: false,
        },
        required: {
            type: Boolean,
            required: false,
            default: null,
        }
     });

</script>
<template>
    <div>
        <label :for="name" class="form-label">{{ label }}</label>
        <input :type="type" :id="name" :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            class="form-input"
            :placeholder="label" :required="required" />
            <template v-if="feedback">
                <Feedback :message="feedback" />
            </template>
    </div>
</template>
