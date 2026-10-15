<script setup>
import { computed, ref, watch } from 'vue';
import { Plus } from '@lucide/vue';
import LegForm from '@/Travel/LegForm.vue';
import LegItem from '@/Travel/LegItem.vue';
import RowActions from '@/Travel/RowActions.vue';
import { destroy } from '@/Travel/destroy.js';
import { saveOrder } from '@/Travel/saveOrder.js';
import { momentBefore } from '@/Travel/times.js';
import { moveItem, useSortable } from '@/Travel/useSortable.js';

// The legs of a journey, or of the arrival route of an accommodation: listed, added, edited, deleted and dragged into
// order.
const props = defineProps({
    legs: {
        type: Array,
        required: true,
    },
    // { type: 'journey' | 'accommodation', id }
    owner: {
        type: Object,
        required: true,
    },
    // The date of the journey, which the times follow on.
    date: {
        type: String,
        default: null,
    },
    // { legTypes, stations, transitLines }
    library: {
        type: Object,
        required: true,
    },
});

// An arrival route is reused for every booking, so its legs have no times.
const timed = computed(() => props.owner.type === 'journey');
const types = computed(() => Object.fromEntries(props.library.legTypes.map((type) => [type.id, type.name])));

const items = ref([...props.legs]);
watch(() => props.legs, (legs) => {
    items.value = [...legs];
});

const list = ref(null);
useSortable(list, '.leg-handle', (from, to) => {
    moveItem(items.value, from, to);
    saveOrder(
        timed.value ? route('admin.journeys.legs.order', props.owner.id) : route('admin.accommodations.legs.order', props.owner.id),
        items.value,
    );
});

// The leg in the form, as { leg, index }; a new leg is null, at the end.
const editing = ref(null);

// A new leg departs where the last one arrived.
const defaults = computed(() => ({
    type: timed.value ? 'train' : 'walk',
    from_station_id: items.value.at(-1)?.to_station?.id ?? null,
}));

const remove = (leg) => {
    const reach = timed.value ? '' : ' De aankomstroute geldt voor elke boeking bij deze accommodatie.';

    destroy(route('admin.legs.destroy', leg.id), `Deze etappe verwijderen?${reach}`);
};
</script>

<template>
    <div>
        <ol ref="list" class="space-y-4">
            <li v-for="(leg, index) in items" :key="leg.id" class="flex items-start gap-2">
                <LegItem :leg="leg" :types="types" class="flex-1" />
                <RowActions handle="leg-handle" :subject="`Etappe ${index + 1}`" @edit="editing = { leg, index }"
                    @delete="remove(leg)" />
            </li>
        </ol>
        <button type="button" :class="['inline-flex items-center gap-1 text-sm font-medium text-brand-primary hover:text-brand-link', { 'mt-4': items.length }]"
            @click="editing = { leg: null, index: items.length }">
            <Plus class="h-4 w-4" aria-hidden="true" />
            Etappe toevoegen
        </button>

        <Modal :open="editing !== null" @close="editing = null">
            <LegForm v-if="editing" :owner="owner" :leg="editing.leg" :defaults="defaults" :timed="timed"
                :after="timed ? momentBefore(items, editing.index, date) : null" :leg-types="library.legTypes"
                :stations="library.stations" :transit-lines="library.transitLines" @close="editing = null" />
        </Modal>
    </div>
</template>
