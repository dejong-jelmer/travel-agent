<script setup>
import { computed, markRaw } from 'vue';
import {
    Landmark, Church, Castle, Palette, Drama, Binoculars, Store, Footprints, MountainSnow, Flower2, Waves, UtensilsCrossed,
    Grape, Signpost, Rose, MapPin,
} from '@lucide/vue';
import CaveIcon from '@/Icons/CaveIcon.vue';
import LascauxHorseIcon from '@/Icons/LascauxHorseIcon.vue';
import HistoricTownIcon from '@/Icons/HistoricTownIcon.vue';
import HistoricCityIcon from '@/Icons/HistoricCityIcon.vue';

const props = defineProps({
    // Icon name from HighlightCategory::icon(); a missing or unknown name renders nothing
    icon: {
        type: String,
        required: false,
        default: null,
    },
});

const icons = {
    Landmark: markRaw(Landmark),
    Church: markRaw(Church),
    Castle: markRaw(Castle),
    Palette: markRaw(Palette),
    Drama: markRaw(Drama),
    Binoculars: markRaw(Binoculars),
    Store: markRaw(Store),
    Footprints: markRaw(Footprints),
    MountainSnow: markRaw(MountainSnow),
    Flower2: markRaw(Flower2),
    Waves: markRaw(Waves),
    UtensilsCrossed: markRaw(UtensilsCrossed),
    Grape: markRaw(Grape),
    Signpost: markRaw(Signpost),
    MapPin: markRaw(MapPin),
    Rose: markRaw(Rose),
    Cave: markRaw(CaveIcon),
    LascauxHorse: markRaw(LascauxHorseIcon),
    HistoricTown: markRaw(HistoricTownIcon),
    HistoricCity: markRaw(HistoricCityIcon),
};

const currentIcon = computed(() => icons[props.icon] ?? null);
</script>

<template>
    <component v-if="currentIcon" :is="currentIcon" aria-hidden="true" />
</template>
