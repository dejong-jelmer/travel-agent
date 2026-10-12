<script setup>
import { computed, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import FormActions from '@/Travel/FormActions.vue';
import { GripVertical, Plus, X } from '@lucide/vue';
import SearchSelect from '@/Travel/SearchSelect.vue';
import { formatDuration, formatShortDate, formatTime } from '@/Travel/format.js';
import { hasTicket, legPayload, legValues } from '@/Travel/legPayload.js';
import { minutesBetween, placeTimes } from '@/Travel/times.js';
import { moveItem, useSortable } from '@/Travel/useSortable.js';

const props = defineProps({
    // { type: 'journey' | 'accommodation', id }
    owner: {
        type: Object,
        required: true,
    },
    // Null for a new leg.
    leg: {
        type: Object,
        default: null,
    },
    defaults: {
        type: Object,
        default: () => ({}),
    },
    // A journey leg has times; an arrival route has none.
    timed: {
        type: Boolean,
        default: false,
    },
    // The moment the times follow on: the leg before, or the date of the journey. Null when there is none.
    after: {
        type: String,
        default: null,
    },
    legTypes: {
        type: Array,
        required: true,
    },
    stations: {
        type: Array,
        required: true,
    },
    transitLines: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['close']);

// The lines get a key of their own: they can be dragged before they have an id.
let lineKey = 0;
const values = legValues(props.leg, props.defaults);
const form = useForm({ ...values, transit_lines: values.transit_lines.map((line) => ({ ...line, key: lineKey++ })) });

const hasLines = computed(() => props.legTypes.find((type) => type.id === form.type)?.has_lines ?? false);
const stationOptions = computed(() =>
    props.stations.map((station) => ({ id: station.id, name: station.name, hint: station.city })),
);
const lineOptions = computed(() => props.transitLines.filter((line) => line.mode === form.type));

// A line of another mode would break the leg, so it goes when the type changes.
watch(() => form.type, (type) => {
    form.transit_lines = form.transit_lines.filter(
        (row) => row.id === null || props.transitLines.find((line) => line.id === row.id)?.mode === type,
    );
});

const times = computed(() => placeTimes(props.after, form.departure_time, form.arrival_time));
const timedDuration = computed(() =>
    times.value.departureAt && times.value.arrivalAt ? minutesBetween(times.value.departureAt, times.value.arrivalAt) : null,
);
const moment = (value) => `${formatShortDate(value)} ${formatTime(value)}`;

const lineList = ref(null);
useSortable(lineList, '.line-handle', (from, to) => moveItem(form.transit_lines, from, to));

const addLine = () => form.transit_lines.push({ id: null, direction: '', key: lineKey++ });

const submit = () => {
    const options = { preserveScroll: true, preserveState: 'errors', onSuccess: () => emit('close') };
    const request = form.transform((data) => legPayload(data, { after: props.after, hasLines: hasLines.value }));

    if (props.leg) {
        request.put(route('admin.legs.update', props.leg.id), options);
    } else if (props.owner.type === 'journey') {
        request.post(route('admin.journeys.legs.store', props.owner.id), options);
    } else {
        request.post(route('admin.accommodations.legs.store', props.owner.id), options);
    }
};
</script>

<template>
    <form class="space-y-6" @submit.prevent="submit">
        <h2 class="text-xl font-semibold text-gray-700">{{ leg ? 'Etappe bewerken' : 'Etappe toevoegen' }}</h2>

        <Select v-model="form.type" name="type" label="Soort" :options="legTypes" :feedback="form.errors.type" required />

        <SearchSelect v-if="form.type === 'transfer'" v-model="form.from_station_id" name="station" label="Station"
            placeholder="Zoek een station" :options="stationOptions"
            :feedback="form.errors.from_station_id ?? form.errors.to_station_id" />
        <div v-else class="grid grid-cols-1 tablet:grid-cols-2 gap-4">
            <SearchSelect v-model="form.from_station_id" name="from_station_id" label="Van" placeholder="Zoek een station"
                :options="stationOptions" :feedback="form.errors.from_station_id" />
            <SearchSelect v-model="form.to_station_id" name="to_station_id" label="Naar" placeholder="Zoek een station"
                :options="stationOptions" :feedback="form.errors.to_station_id" />
        </div>

        <div v-if="timed" class="space-y-2">
            <div class="grid grid-cols-2 gap-4">
                <Input v-model="form.departure_time" type="time" name="departure_time" label="Vertrek" :disabled="!after"
                    :feedback="form.errors.departure_at" />
                <Input v-model="form.arrival_time" type="time" name="arrival_time" label="Aankomst" :disabled="!after"
                    :feedback="form.errors.arrival_at" />
            </div>
            <p v-if="!after" class="text-sm text-gray-500">Geef de reis eerst een datum, dan kun je tijden invullen.</p>
            <p v-else-if="timedDuration !== null" class="text-sm text-gray-500">
                {{ moment(times.departureAt) }} → {{ moment(times.arrivalAt) }} · {{ formatDuration(timedDuration) }}
            </p>
        </div>

        <!-- With both times set, the duration follows from them. -->
        <Input v-if="timedDuration === null" v-model="form.duration_minutes" type="number" min="0" name="duration_minutes"
            label="Duur in minuten" :feedback="form.errors.duration_minutes" />

        <div v-if="hasTicket(form.type)" class="grid grid-cols-1 tablet:grid-cols-3 gap-4">
            <Input v-model="form.number" name="number" label="Treinnummer" :feedback="form.errors.number" />
            <Input v-model="form.carriage" name="carriage" label="Rijtuig" :feedback="form.errors.carriage" />
            <Input v-model="form.seat" name="seat" label="Stoel" :feedback="form.errors.seat" />
        </div>

        <fieldset v-if="hasLines" class="space-y-3">
            <legend class="font-bold">Lijnen</legend>
            <ol ref="lineList" class="space-y-3">
                <li v-for="(line, index) in form.transit_lines" :key="line.key" class="flex items-start gap-2">
                    <button type="button" aria-label="Lijn verslepen"
                        class="line-handle mt-2 p-1 rounded text-gray-400 hover:text-gray-600 hover:bg-gray-100 cursor-grab active:cursor-grabbing">
                        <GripVertical class="h-4 w-4" aria-hidden="true" />
                    </button>
                    <SearchSelect v-model="line.id" :name="`transit_line_${index}`" placeholder="Zoek een lijn"
                        :options="lineOptions" :feedback="form.errors[`transit_lines.${index}.id`]" />
                    <Input v-model="line.direction" :name="`direction_${index}`" placeholder="Richting"
                        :feedback="form.errors[`transit_lines.${index}.direction`]" class="w-full" />
                    <button type="button" aria-label="Lijn weghalen"
                        class="mt-2 p-1 rounded text-gray-400 hover:text-status-error hover:bg-gray-100"
                        @click="form.transit_lines.splice(index, 1)">
                        <X class="h-4 w-4" aria-hidden="true" />
                    </button>
                </li>
            </ol>
            <p v-if="!lineOptions.length" class="text-sm text-gray-500">Er staan nog geen lijnen van deze soort in de bibliotheek.</p>
            <button v-else type="button" class="inline-flex items-center gap-1 text-sm font-medium text-brand-primary hover:text-brand-link"
                @click="addLine">
                <Plus class="h-4 w-4" aria-hidden="true" />
                Lijn toevoegen
            </button>
            <FormFeedback v-if="form.errors.transit_lines" :message="form.errors.transit_lines" />
        </fieldset>

        <TextArea v-model="form.note" name="note" label="Opmerking" :rows="2" :feedback="form.errors.note" />

        <FormActions :processing="form.processing" @cancel="emit('close')" />
    </form>
</template>
