<script setup>
import { computed, ref } from 'vue';
import { ExternalLink } from '@lucide/vue';
import RowActions from '@/Travel/RowActions.vue';
import { destroy } from '@/Travel/destroy.js';
import LibraryCard from '@/Travel/Library/LibraryCard.vue';
import LineForm from '@/Travel/Library/LineForm.vue';
import { matches } from '@/Travel/Library/search.js';

const props = defineProps({
    transitLines: {
        type: Array,
        required: true,
    },
    transitModeOptions: {
        type: Array,
        required: true,
    },
});

const modes = computed(() => Object.fromEntries(props.transitModeOptions.map((option) => [option.id, option.name])));

const search = ref('');
const shown = computed(() => props.transitLines.filter((line) => matches(search.value, line.name, modes.value[line.mode])));

// The line in the form, as { line }, with null for a new one.
const lineForm = ref(null);
</script>

<template>
    <LibraryCard v-model:search="search" title="Lijnen"
        description="De trein-, metro-, tram-, bus- en bootlijnen, met hun kleur zoals de vervoerder die gebruikt."
        add-label="Lijn toevoegen" :shown="shown.length" :total="transitLines.length" @add="lineForm = { line: null }">
        <ul class="divide-y divide-gray-100">
            <li v-for="line in shown" :key="line.id" class="flex items-start justify-between gap-4 px-6 py-3 text-sm">
                <div class="flex min-w-0 items-center gap-2">
                    <span class="h-3 w-3 flex-shrink-0 rounded-full" :class="{ 'bg-gray-300': !line.color }"
                        :style="line.color ? { backgroundColor: line.color } : null" aria-hidden="true" />
                    <p class="font-medium text-gray-900">{{ line.name }}</p>
                    <p class="text-gray-500">{{ modes[line.mode] }}</p>
                    <!-- A link with variables, such as {%leg_number%}, only leads somewhere for a leg: here it is no link. -->
                    <span v-if="line.info_link_has_variables" class="text-gray-400" :title="line.info_link" role="img"
                        :aria-label="`Informatielink per etappe: ${line.info_link}`">
                        <ExternalLink class="h-4 w-4" aria-hidden="true" />
                    </span>
                    <a v-else-if="line.info_link" :href="line.info_link" target="_blank" rel="noopener"
                        class="text-gray-400 hover:text-brand-link" :aria-label="`Informatie over ${line.name}`">
                        <ExternalLink class="h-4 w-4" aria-hidden="true" />
                    </a>
                </div>
                <RowActions :subject="`Lijn ${line.name}`" @edit="lineForm = { line }"
                    @delete="destroy(route('admin.transit-lines.destroy', line.id), `Lijn ${line.name} verwijderen?`)" />
            </li>
        </ul>

        <Modal :open="lineForm !== null" @close="lineForm = null">
            <LineForm v-if="lineForm" :line="lineForm.line" :transit-mode-options="transitModeOptions"
                @close="lineForm = null" />
        </Modal>
    </LibraryCard>
</template>
