<script setup>
import { computed, markRaw } from 'vue';
import { TrainFront, Ship, Bus, Car, ArrowLeftRight, Plane } from 'lucide-vue-next';

const props = defineProps({
    transport: {
        type: String,
        required: true,
        validator: (value) => ['train', 'ferry', 'bus', 'taxi', 'transfer', 'airplane'].includes(value)
    },
    size: {
        type: String,
        default: 'w-4 h-4'
    }
});

const transportIcons = {
    train: markRaw(TrainFront),
    ferry: markRaw(Ship),
    bus: markRaw(Bus),
    taxi: markRaw(Car),
    transfer: markRaw(ArrowLeftRight),
    airplane: markRaw(Plane)
};

const currentIcon = computed(() => transportIcons[props.transport]);
</script>

<template>
    <component :is="currentIcon" :class="size" />
</template>
