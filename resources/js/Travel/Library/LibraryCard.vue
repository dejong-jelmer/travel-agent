<script setup>
import { Plus, Search } from '@lucide/vue';

// A section of the library: a searchable list with a button to add to it.
defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        required: true,
    },
    addLabel: {
        type: String,
        required: true,
    },
    // How many items the search leaves, of how many, to say why the list is empty.
    shown: {
        type: Number,
        required: true,
    },
    total: {
        type: Number,
        required: true,
    },
});

defineEmits(['add']);

const search = defineModel('search', { type: String, default: '' });
</script>

<template>
    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex flex-wrap items-start justify-between gap-4 border-b border-gray-200 px-6 py-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-700">{{ title }}</h2>
                <p class="mt-1 text-sm text-gray-700/30">{{ description }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-4">
                <label class="relative">
                    <span class="sr-only">Zoeken in {{ title.toLowerCase() }}</span>
                    <Search class="absolute left-2.5 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" aria-hidden="true" />
                    <input v-model="search" type="search" placeholder="Zoeken" class="form-input w-56 py-1.5 pl-8 text-sm" />
                </label>
                <button type="button"
                    class="inline-flex items-center gap-1 text-sm font-medium text-brand-primary hover:text-brand-link whitespace-nowrap"
                    @click="$emit('add')">
                    <Plus class="h-4 w-4" aria-hidden="true" />
                    {{ addLabel }}
                </button>
            </div>
        </div>
        <p v-if="!total" class="p-6 text-sm text-gray-500 italic">Nog niets toegevoegd.</p>
        <p v-else-if="!shown" class="p-6 text-sm text-gray-500 italic">Niets gevonden voor "{{ search }}".</p>
        <slot />
    </section>
</template>
