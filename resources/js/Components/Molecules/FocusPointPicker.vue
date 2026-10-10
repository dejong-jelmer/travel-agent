<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
    // Object-position of the chosen point, e.g. '0% 100%'; null keeps the image centred
    modelValue: {
        type: String,
        default: null,
    },
    // The photo to pick the point on: the URL of a saved image or a newly selected File
    image: {
        type: [String, File],
        required: true,
    },
    // Shape: [{ id, name }], the nine positions row by row from top left to bottom right, see HeroFocus
    options: {
        type: Array,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    feedback: [String, Array],
})

const emit = defineEmits(['update:modelValue'])

// Without a choice the image is centred, the CSS default of object-position
const selected = computed(() => props.modelValue ?? '50% 50%')

// A newly selected file is shown through an object URL, revoked once the file changes or the picker unmounts
const src = ref(null)
watch(() => props.image, (image, _, onCleanup) => {
    if (!(image instanceof File)) {
        src.value = image
        return
    }

    const url = URL.createObjectURL(image)
    src.value = url
    onCleanup(() => URL.revokeObjectURL(url))
}, { immediate: true })
</script>

<template>
    <div>
        <div class="relative w-full max-w-xs overflow-hidden rounded-lg border border-gray-200">
            <img :src="src" alt="" class="block w-full h-auto" />
            <div class="absolute inset-0 grid grid-cols-3 grid-rows-3" role="group" :aria-label="label">
                <button v-for="option in options" :key="option.id" type="button" :title="option.name"
                    :aria-label="option.name" :aria-pressed="option.id === selected"
                    class="flex items-center justify-center border border-white/40 transition-colors hover:bg-white/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-brand-accent"
                    :class="{ 'bg-brand-accent/20': option.id === selected }"
                    @click="emit('update:modelValue', option.id)">
                    <span v-if="option.id === selected" class="h-4 w-4 rounded-full bg-brand-accent ring-2 ring-white shadow"
                        aria-hidden="true"></span>
                </button>
            </div>
        </div>
        <FormFeedback v-if="feedback" :message="feedback" />
    </div>
</template>
