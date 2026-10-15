<script setup>
import { computed, ref } from 'vue';
import { Combobox, ComboboxButton, ComboboxInput, ComboboxOption, ComboboxOptions } from '@headlessui/vue';
import { Check, ChevronsUpDown } from '@lucide/vue';

// A searchable select for the library: the stations, lines and accommodations run into the hundreds.
const props = defineProps({
    modelValue: {
        type: Number,
        default: null,
    },
    // [{ id, name, hint? }]: the hint, such as the city of a station, is searched too.
    options: {
        type: Array,
        required: true,
    },
    name: String,
    label: String,
    placeholder: String,
    required: Boolean,
    feedback: {
        type: [String, Array],
        default: null,
    },
});

const emit = defineEmits(['update:modelValue']);

const query = ref('');

const selected = computed(() => props.options.find((option) => option.id === props.modelValue) ?? null);

const filtered = computed(() => {
    const search = query.value.trim().toLowerCase();

    return search
        ? props.options.filter((option) => `${option.name} ${option.hint ?? ''}`.toLowerCase().includes(search))
        : props.options;
});

const select = (option) => {
    query.value = '';
    emit('update:modelValue', option?.id ?? null);
};
</script>

<template>
    <div class="flex flex-col gap-1 w-full">
        <Label v-if="label" :forField="name" :required="required">{{ label }}</Label>
        <Combobox :modelValue="selected" by="id" nullable @update:modelValue="select">
            <div class="relative">
                <ComboboxInput :id="name" autocomplete="off" class="form-input pr-10"
                    :class="feedback ? 'ring-[2px] ring-status-error ring-offset-2 bg-status-error/20' : ''"
                    :displayValue="(option) => option?.name ?? ''" :placeholder="placeholder"
                    @change="query = $event.target.value" />
                <ComboboxButton class="absolute inset-y-0 right-0 flex items-center px-2">
                    <ChevronsUpDown class="h-4 w-4 text-gray-400" aria-hidden="true" />
                </ComboboxButton>
                <ComboboxOptions
                    class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-sm shadow-lg ring-1 ring-black/5 focus:outline-none">
                    <li v-if="!filtered.length" class="px-3 py-2 text-gray-500">Niets gevonden.</li>
                    <ComboboxOption v-for="option in filtered" :key="option.id" :value="option" as="template"
                        v-slot="{ active, selected: isSelected }">
                        <li :class="['relative cursor-pointer select-none py-2 pl-3 pr-9',
                            active ? 'bg-brand-primary text-white' : 'text-gray-900']">
                            <span :class="{ 'font-semibold': isSelected }">{{ option.name }}</span>
                            <span v-if="option.hint" :class="['ml-2', active ? 'text-white/70' : 'text-gray-500']">
                                {{ option.hint }}
                            </span>
                            <Check v-if="isSelected" class="absolute inset-y-0 right-3 my-auto h-4 w-4" aria-hidden="true" />
                        </li>
                    </ComboboxOption>
                </ComboboxOptions>
            </div>
        </Combobox>
        <FormFeedback v-if="feedback" :message="feedback" />
    </div>
</template>
