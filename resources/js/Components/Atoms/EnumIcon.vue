<script setup>
import { computed, markRaw } from 'vue';
import { TrainFront, Ship, Bus, Car, ArrowLeftRight, Plane, Coffee, Sandwich, HandPlatter, Circle } from 'lucide-vue-next';

const props = defineProps({
    enum: {
        type: String,
        required: true,
        validator: (value) => ['train', 'ferry', 'bus', 'taxi', 'transfer', 'airplane', 'breakfast', 'lunch', 'dinner'].includes(value)
    }
});

const enumIcons = {
    train: markRaw(TrainFront),
    ferry: markRaw(Ship),
    bus: markRaw(Bus),
    taxi: markRaw(Car),
    transfer: markRaw(ArrowLeftRight),
    airplane: markRaw(Plane),
    breakfast: markRaw(Coffee),
    lunch: markRaw(Sandwich),
    dinner: markRaw(HandPlatter)
};

const currentIcon = computed(() => {
    return enumIcons[props.enum] ?? markRaw(Circle);
});
</script>

<template>
    <component :is="currentIcon" role="img" :aria-label="props.enum" />
</template>
