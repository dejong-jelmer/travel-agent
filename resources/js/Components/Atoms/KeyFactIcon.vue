<script setup>
import { computed, markRaw } from 'vue';
import {
    TrainFront, MoonStar, Clock, ArrowLeftRight, MapPin, BedDouble, Coffee, MountainSnow, CalendarDays, Sun, Info,
} from '@lucide/vue';

const props = defineProps({
    // Value of the KeyFactIcon enum; an unknown or missing value falls back to the neutral icon
    icon: {
        type: String,
        required: false,
        default: null,
    },
});

const icons = {
    train: markRaw(TrainFront),
    night: markRaw(MoonStar),
    clock: markRaw(Clock),
    transfer: markRaw(ArrowLeftRight),
    location: markRaw(MapPin),
    bed: markRaw(BedDouble),
    breakfast: markRaw(Coffee),
    mountain: markRaw(MountainSnow),
    calendar: markRaw(CalendarDays),
    sun: markRaw(Sun),
    info: markRaw(Info),
};

const currentIcon = computed(() => icons[props.icon] ?? icons.info);
</script>

<template>
    <component :is="currentIcon" aria-hidden="true" />
</template>
