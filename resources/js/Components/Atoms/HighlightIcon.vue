<script setup>
import { computed, markRaw } from 'vue';
import {
    Landmark, Church, Castle, Palette, Drama, Binoculars, Store, Footprints, Mountain, Flower2, Waves, UtensilsCrossed,
    Wine, Signpost, MapPin,
} from 'lucide-vue-next';
import CaveIcon from './CaveIcon.vue';
import LascauxHorseIcon from './LascauxHorseIcon.vue';
import HistoricTownIcon from './HistoricTownIcon.vue';
import HistoricCityIcon from './HistoricCityIcon.vue';

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
    Mountain: markRaw(Mountain),
    Flower2: markRaw(Flower2),
    Waves: markRaw(Waves),
    UtensilsCrossed: markRaw(UtensilsCrossed),
    Wine: markRaw(Wine),
    Signpost: markRaw(Signpost),
    MapPin: markRaw(MapPin),
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
