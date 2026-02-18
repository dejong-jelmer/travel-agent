<script setup>
import { ref, computed, watch } from 'vue';
import { Combobox, ComboboxInput, ComboboxButton, ComboboxOptions, ComboboxOption } from '@headlessui/vue';
import { ChevronUpDownIcon, CheckIcon } from '@heroicons/vue/20/solid';
import FormFeedback from "@/Components/Atoms/FormFeedback.vue";

const props = defineProps({
    modelValue: String,
    name: String,
    label: String,
    required: {
        type: Boolean,
        default: false,
    },
    feedback: {
        type: [String, Array],
        required: false,
    },
    placeholder: String,
    options: {
        type: Array,
        required: true,
    },
    showLabel: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['update:modelValue']);

const query = ref('');

const selectedCountry = computed(() =>
    props.options.find(c => c.code === props.modelValue) || null
);

const filteredCountries = computed(() => {
    if (!query.value) return props.options;

    const search = query.value.toLowerCase();
    return props.options.filter(country =>
        country.name.toLowerCase().includes(search) ||
        country.code.toLowerCase().includes(search)
    );
});

const updateSelection = (country) => {
    emit('update:modelValue', country?.code || '');
};

watch(selectedCountry, () => {
    query.value = '';
});
</script>

<template>
    <div class="flex flex-col gap-1 w-full">
        <Label v-if="(label && showLabel) || $slots.label" :forField="name" :required="required">
            <slot name="label">{{ label }}</slot>
        </Label>

        <Combobox :modelValue="selectedCountry" @update:modelValue="updateSelection" nullable>
            <div class="relative">
                <ComboboxInput
                    :id="name"
                    :displayValue="(country) => country?.name || ''"
                    @change="query = $event.target.value"
                    :placeholder="placeholder"
                    class="form-input pr-10"
                    :class="!!feedback ? 'ring-[2px] ring-status-error ring-offset-2 bg-status-error/20' : ''"
                />

                <ComboboxButton class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                    <ChevronUpDownIcon class="h-5 w-5 text-gray-400" />
                </ComboboxButton>

                <ComboboxOptions class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                    <div v-if="filteredCountries.length === 0" class="px-4 py-2 text-sm text-gray-500">
                        {{ $t('common.no_results') }}
                    </div>

                    <ComboboxOption
                        v-for="country in filteredCountries"
                        :key="country.code"
                        :value="country"
                        v-slot="{ active, selected }"
                    >
                        <li :class="[
                            'relative cursor-pointer select-none py-2 pl-3 pr-9',
                            active ? 'bg-brand-primary text-white' : 'text-gray-900'
                        ]">
                            <div class="flex items-center">
                                <span :class="['block truncate', selected && 'font-semibold']">
                                    {{ country.name }}
                                </span>
                                <span :class="[
                                    'ml-2 text-sm',
                                    active ? 'text-white/70' : 'text-gray-500'
                                ]">
                                    ({{ country.code }})
                                </span>
                            </div>

                            <span v-if="selected" :class="[
                                'absolute inset-y-0 right-0 flex items-center pr-4',
                                active ? 'text-white' : 'text-brand-primary'
                            ]">
                                <CheckIcon class="h-5 w-5" />
                            </span>
                        </li>
                    </ComboboxOption>
                </ComboboxOptions>
            </div>
        </Combobox>

        <template v-if="feedback">
            <FormFeedback :message="feedback" />
        </template>
    </div>
</template>
