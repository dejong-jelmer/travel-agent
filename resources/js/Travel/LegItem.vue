<script setup>
import { ArrowLeftRight, Bus, CarTaxiFront, Footprints, Ship, TrainFront, TrainFrontTunnel, TramFront } from '@lucide/vue';
import { formatDuration, formatTime } from '@/Travel/format.js';

defineProps({
    leg: {
        type: Object,
        required: true,
    },
    // The Dutch label of each leg type, by its value.
    types: {
        type: Object,
        required: true,
    },
});

const icons = {
    train: TrainFront,
    metro: TrainFrontTunnel,
    tram: TramFront,
    bus: Bus,
    ferry: Ship,
    taxi: CarTaxiFront,
    transfer: ArrowLeftRight,
    walk: Footprints,
};

// A transfer stays at one station; an arrival route can start without a station of its own.
const stations = (leg) => {
    const from = leg.from_station?.name;
    const to = leg.to_station?.name;

    if (from && to) {
        return from === to ? from : `${from} → ${to}`;
    }

    return from ?? (to ? `→ ${to}` : '');
};

const timing = (leg) =>
    [
        [leg.departure_at, leg.arrival_at].filter(Boolean).map(formatTime).join(' – '),
        formatDuration(leg.duration),
    ]
        .filter(Boolean)
        .join(' · ');

const ticket = (leg) =>
    [
        leg.number && `Nr. ${leg.number}`,
        leg.carriage && `Rijtuig ${leg.carriage}`,
        leg.seat && `Stoel ${leg.seat}`,
    ]
        .filter(Boolean)
        .join(' · ');
</script>

<template>
    <div class="flex gap-3 min-w-0">
        <component :is="icons[leg.type]" class="h-4 w-4 mt-0.5 text-gray-400 flex-shrink-0" aria-hidden="true" />
        <div class="min-w-0 flex-1 text-sm">
            <div class="flex flex-wrap items-baseline justify-between gap-x-4">
                <p class="font-medium text-gray-900">
                    {{ types[leg.type] }}<template v-if="stations(leg)"> · {{ stations(leg) }}</template>
                </p>
                <p v-if="timing(leg)" class="text-gray-500 tabular-nums">{{ timing(leg) }}</p>
            </div>
            <ul v-if="leg.transit_lines.length" class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-gray-700">
                <li v-for="line in leg.transit_lines" :key="line.id" class="inline-flex items-center gap-1.5">
                    <span class="h-2.5 w-2.5 rounded-full flex-shrink-0"
                        :class="{ 'bg-gray-300': !line.color }"
                        :style="line.color ? { backgroundColor: line.color } : null" />
                    {{ line.name }}
                    <span v-if="line.direction" class="text-gray-500">richting {{ line.direction }}</span>
                </li>
            </ul>
            <p v-if="ticket(leg)" class="mt-1 text-gray-500">{{ ticket(leg) }}</p>
            <p v-if="leg.note" class="mt-1 text-gray-500 italic">{{ leg.note }}</p>
        </div>
    </div>
</template>
