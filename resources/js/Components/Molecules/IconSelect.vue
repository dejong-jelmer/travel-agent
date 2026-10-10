<script setup>
import { computed } from 'vue';
import { Listbox, ListboxButton, ListboxOptions, ListboxOption } from '@headlessui/vue';
import { ChevronDown } from '@lucide/vue';

const props = defineProps({
    modelValue: String,
    // Shape: [{ id, name, icon }], where icon is passed to the icon component; without it the id is passed instead.
    // An option with id '' stands for no choice, and also matches a null value.
    options: {
        type: Array,
        required: true,
    },
    // The component that renders the icon of an option from its icon prop
    iconComponent: {
        type: [String, Object],
        required: false,
        default: 'KeyFactIcon',
    },
    label: String,
    feedback: {
        type: [String, Array],
        required: false,
        default: null,
    },
});
const emit = defineEmits(['update:modelValue']);

const value = computed(() => props.modelValue ?? '');
const selected = computed(() => props.options.find((option) => option.id === value.value) ?? null);
const iconOf = (option) => option?.icon ?? option?.id ?? null;
</script>

<template>
    <div class="grid gap-1">
        <Listbox :modelValue="value" @update:modelValue="emit('update:modelValue', $event)">
            <div class="relative">
                <ListboxButton :aria-label="label"
                    class="form-input flex w-full items-center gap-2 text-left"
                    :class="!!feedback ? 'ring-[2px] ring-status-error ring-offset-2 bg-status-error/20' : ''">
                    <!-- The wrapper keeps the text aligned when an option has no icon -->
                    <span class="h-5 w-5 flex-shrink-0">
                        <component :is="iconComponent" :icon="iconOf(selected)" class="h-5 w-5 text-brand-primary" />
                    </span>
                    <span class="flex-1 truncate">{{ selected?.name }}</span>
                    <ChevronDown class="h-4 w-4 flex-shrink-0 text-gray-400" aria-hidden="true" />
                </ListboxButton>
                <ListboxOptions
                    class="absolute z-30 mt-1 max-h-60 w-full min-w-[12rem] overflow-auto rounded-md border border-gray-200 bg-white py-1 text-sm shadow-lg focus:outline-none">
                    <ListboxOption v-for="option in options" :key="option.id" :value="option.id" v-slot="{ active, selected: isSelected }">
                        <div class="flex cursor-pointer items-center gap-2 px-3 py-2"
                            :class="[active ? 'bg-brand-secondary' : '', isSelected ? 'font-semibold' : '']">
                            <span class="h-5 w-5 flex-shrink-0">
                                <component :is="iconComponent" :icon="iconOf(option)" class="h-5 w-5 text-brand-primary" />
                            </span>
                            {{ option.name }}
                        </div>
                    </ListboxOption>
                </ListboxOptions>
            </div>
        </Listbox>
        <FormFeedback v-if="feedback" :message="feedback" />
    </div>
</template>
