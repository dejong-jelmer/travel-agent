<script setup>
import { computed } from 'vue';
import { Listbox, ListboxButton, ListboxOptions, ListboxOption } from '@headlessui/vue';
import { ChevronDown } from 'lucide-vue-next';

const props = defineProps({
    modelValue: String,
    // Shape: [{ id, name }], where id is the value of the KeyFactIcon enum
    options: {
        type: Array,
        required: true,
    },
    label: String,
    feedback: {
        type: [String, Array],
        required: false,
        default: null,
    },
});
const emit = defineEmits(['update:modelValue']);

const selected = computed(() => props.options.find((option) => option.id === props.modelValue) ?? null);
</script>

<template>
    <div class="grid gap-1">
        <Listbox :modelValue="modelValue" @update:modelValue="emit('update:modelValue', $event)">
            <div class="relative">
                <ListboxButton :aria-label="label"
                    class="form-input flex w-full items-center gap-2 text-left"
                    :class="!!feedback ? 'ring-[2px] ring-status-error ring-offset-2 bg-status-error/20' : ''">
                    <KeyFactIcon :icon="modelValue" class="h-5 w-5 flex-shrink-0 text-brand-primary" />
                    <span class="flex-1 truncate">{{ selected?.name }}</span>
                    <ChevronDown class="h-4 w-4 flex-shrink-0 text-gray-400" aria-hidden="true" />
                </ListboxButton>
                <ListboxOptions
                    class="absolute z-30 mt-1 max-h-60 w-full min-w-[12rem] overflow-auto rounded-md border border-gray-200 bg-white py-1 text-sm shadow-lg focus:outline-none">
                    <ListboxOption v-for="option in options" :key="option.id" :value="option.id" v-slot="{ active, selected: isSelected }">
                        <div class="flex cursor-pointer items-center gap-2 px-3 py-2"
                            :class="[active ? 'bg-brand-secondary' : '', isSelected ? 'font-semibold' : '']">
                            <KeyFactIcon :icon="option.id" class="h-5 w-5 flex-shrink-0 text-brand-primary" />
                            {{ option.name }}
                        </div>
                    </ListboxOption>
                </ListboxOptions>
            </div>
        </Listbox>
        <FormFeedback v-if="feedback" :message="feedback" />
    </div>
</template>
