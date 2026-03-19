<script setup>
import { computed } from 'vue';

const props = defineProps({
    min: { type: Number, required: true },
    max: { type: Number, required: true },
    step: { type: Number, default: 1 },
    modelMin: { type: Number, required: true },
    modelMax: { type: Number, required: true },
});

const emit = defineEmits(['update:modelMin', 'update:modelMax']);

const range = computed(() => props.max - props.min || 1);

const leftPercent = computed(() => ((props.modelMin - props.min) / range.value) * 100);
const widthPercent = computed(() => ((props.modelMax - props.modelMin) / range.value) * 100);

const trackStyle = computed(() => ({
    left: leftPercent.value + '%',
    width: widthPercent.value + '%',
}));

// When handles overlap, give max-handle higher z-index so it stays reachable
const maxZIndex = computed(() => props.modelMin >= props.modelMax ? 'z-20' : 'z-10');
const minZIndex = computed(() => props.modelMin >= props.modelMax ? 'z-10' : 'z-20');

function onMinInput(e) {
    const val = Math.min(Number(e.target.value), props.modelMax);
    emit('update:modelMin', val);
}

function onMaxInput(e) {
    const val = Math.max(Number(e.target.value), props.modelMin);
    emit('update:modelMax', val);
}
</script>

<template>
    <div class="pt-2 pb-1">
        <!-- Track -->
        <div class="relative h-1.5 rounded-full bg-brand-secondary mb-4">
            <div class="absolute h-full rounded-full bg-brand-primary" :style="trackStyle"></div>
        </div>

        <!-- Dual range inputs -->
        <div class="relative h-4">
            <input
                type="range"
                :min="min"
                :max="max"
                :step="step"
                :value="modelMin"
                @input="onMinInput"
                :class="['absolute w-full top-0 appearance-none bg-transparent cursor-pointer range-thumb', minZIndex]"
            />
            <input
                type="range"
                :min="min"
                :max="max"
                :step="step"
                :value="modelMax"
                @input="onMaxInput"
                :class="['absolute w-full top-0 appearance-none bg-transparent cursor-pointer range-thumb', maxZIndex]"
            />
        </div>
    </div>
</template>

<style scoped>
/* Hide default track, style only the thumb */
input[type="range"].range-thumb {
    pointer-events: none;
    height: 0;
}

input[type="range"].range-thumb::-webkit-slider-thumb {
    pointer-events: all;
    -webkit-appearance: none;
    appearance: none;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background-color: #2d5f6e;
    border: 2px solid white;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
    cursor: pointer;
    margin-top: -7px;
    transition: box-shadow 0.15s ease;
}

input[type="range"].range-thumb::-webkit-slider-thumb:hover {
    box-shadow: 0 0 0 4px rgba(45, 95, 110, 0.15);
}

input[type="range"].range-thumb::-moz-range-thumb {
    pointer-events: all;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background-color: #2d5f6e;
    border: 2px solid white;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
    cursor: pointer;
}

input[type="range"].range-thumb::-webkit-slider-runnable-track {
    background: transparent;
    height: 4px;
}

input[type="range"].range-thumb::-moz-range-track {
    background: transparent;
    height: 4px;
}
</style>
