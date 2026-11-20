<script setup>
defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    name: {
        type: String,
        required: true,
    },
    // What the page uses when the field is left empty, such as the title; shown as placeholder
    fallback: {
        type: String,
        default: '',
    },
    feedback: {
        type: [String, Array],
        default: null,
    },
});

const emit = defineEmits(['update:modelValue']);
</script>

<template>
    <div class="space-y-1">
        <Input type="text" :name="name" :label="$t('forms.image_alt.label')" :model-value="modelValue"
            :placeholder="fallback" :feedback="feedback" maxlength="255"
            @update:model-value="emit('update:modelValue', $event)" />
        <p class="text-xs text-gray-700/50">{{ $t('forms.image_alt.help') }}</p>
    </div>
</template>
