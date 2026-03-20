<script setup>
import { computed, markRaw } from 'vue';
import { TrainFront, Ship, Bus, Car, ArrowLeftRight, Plane, Circle } from 'lucide-vue-next';

const props = defineProps({
    enum: {
        type: String,
        required: false,
        default: null,
        validator: (value) => ['train', 'ferry', 'bus', 'taxi', 'transfer', 'airplane'].includes(value)
    }
});

const enumIcons = {
    train: markRaw(TrainFront),
    ferry: markRaw(Ship),
    bus: markRaw(Bus),
    taxi: markRaw(Car),
    transfer: markRaw(ArrowLeftRight),
    airplane: markRaw(Plane),
};

const currentIcon = computed(() => {
    return enumIcons[props.enum] ?? markRaw(Circle);
});
</script>

<template>
    <component :is="currentIcon" aria-hidden="true" />
</template>
