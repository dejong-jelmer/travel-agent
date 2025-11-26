<script setup>
const props = defineProps({
    modelValue: Boolean,
    name: String,
    label: String,
    feedback: {
        type: [String, Array],
        required: false,
    },
})
</script>
<template>
    <div>
        <label class="flex items-center gap-x-2 cursor-pointer">
            <input type="checkbox" :checked="modelValue" @change="$emit('update:modelValue', $event.target.checked)"
                class="hidden peer" />
            <div
                class="w-5 h-5 flex flex-none items-center justify-center border-2 border-brand-primary/30 rounded-md transition-all peer-checked:bg-white peer-checked:border-brand-primary">
                <svg v-if="modelValue" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="black"
                    class="w-4 h-4">
                    <path fill-rule="evenodd"
                        d="M20.707 5.293a1 1 0 0 1 0 1.414l-10 10a1 1 0 0 1-1.414 0l-4-4a1 1 0 1 1 1.414-1.414L10 14.586l9.293-9.293a1 1 0 0 1 1.414 0z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <span class="block text-base">
                <slot></slot>
            </span>
        </label>
        <template v-if="feedback">
            <FormFeedback :message="feedback" />
        </template>
    </div>
</template>
