<script setup>
import { coordinatePair } from '@/Travel/Library/search.js';

// Latitude and longitude. A pair pasted from a map into either field fills both.
defineProps({
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const latitude = defineModel('latitude', { type: [String, Number], default: '' });
const longitude = defineModel('longitude', { type: [String, Number], default: '' });

const fill = (value, field) => {
    const pair = coordinatePair(value);

    if (pair) {
        latitude.value = pair.latitude;
        longitude.value = pair.longitude;
    } else if (field === 'latitude') {
        latitude.value = value;
    } else {
        longitude.value = value;
    }
};
</script>

<template>
    <div class="space-y-1">
        <div class="grid grid-cols-1 tablet:grid-cols-2 gap-4">
            <Input :modelValue="latitude" name="latitude" label="Breedtegraad" inputmode="decimal" placeholder="44.8249878"
                :feedback="errors.latitude" @update:modelValue="fill($event, 'latitude')" />
            <Input :modelValue="longitude" name="longitude" label="Lengtegraad" inputmode="decimal" placeholder="-0.5602157"
                :feedback="errors.longitude" @update:modelValue="fill($event, 'longitude')" />
        </div>
        <p class="text-sm text-gray-500">Plak gerust de coördinaten uit een kaart, zoals "44.8249878, -0.5602157".</p>
    </div>
</template>
