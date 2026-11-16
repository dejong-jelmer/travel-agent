<script setup>
import { computed } from 'vue';
import { progressParts } from './format.js';

// The occupancy of a departure in two layers: confirmed participants dark, places that are held but not confirmed
// light, with a mark where the minimum lies.
const props = defineProps({
    occupied: { type: Number, required: true },
    confirmed: { type: Number, required: true },
    min: { type: Number, required: true },
    max: { type: Number, required: true },
});

const parts = computed(() => progressParts(props));
const reachedMinimum = computed(() => props.confirmed >= props.min);
</script>

<template>
    <div>
        <div class="relative pt-2 pb-1">
            <div class="h-3 w-full rounded-full bg-gray-100 overflow-hidden flex" role="img"
                :aria-label="$t('admin.departures.progress.aria', { confirmed, held: parts.held, max, min })">
                <div class="h-full transition-all" data-testid="progress-confirmed"
                    :class="reachedMinimum ? 'bg-status-success' : 'bg-brand-primary'"
                    :style="{ width: parts.confirmedWidth + '%' }" />
                <div class="h-full bg-brand-light transition-all" data-testid="progress-held"
                    :style="{ width: parts.heldWidth + '%' }" />
            </div>
            <!-- The minimum -->
            <div class="absolute top-0 bottom-0 w-0.5 bg-brand-text" data-testid="progress-minimum"
                :style="{ left: `calc(${parts.minimumAt}% - 1px)` }" />
        </div>
        <p class="mt-1 text-xs text-gray-600 flex flex-wrap gap-x-3 gap-y-1">
            <span class="font-semibold" :class="reachedMinimum ? 'text-status-success' : 'text-brand-primary'">
                {{ $t('admin.departures.progress.confirmed', { count: confirmed }) }}
            </span>
            <span v-if="parts.held > 0">{{ $t('admin.departures.progress.held', { count: parts.held }) }}</span>
            <span>{{ $t('admin.departures.progress.minimum', { count: min }) }}</span>
            <span>{{ $t('admin.departures.progress.seats', { occupied, max }) }}</span>
        </p>
    </div>
</template>
