<script setup>
    import FormFeedback from "@/Components/Atoms/FormFeedback.vue";

    defineProps({
        modelValue: [String, Number, Array],
        type: String,
        name: String,
        label: String,
        feedback: {
            type: [ String, Array ],
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
        }
     });

</script>
<template>
    <div>
        <label v-if="showLabel" :for="name" class="form-label">{{ label }}</label>
        <input :type="type" :id="name" :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            class="form-input"
            :placeholder="!showLabel ? label : ''" :required="required" />
            <template v-if="feedback">
                <FormFeedback :message="feedback" />
            </template>
    </div>
</template>
