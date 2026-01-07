<script setup>
import { computed, markRaw } from 'vue';
import { Coffee, Sandwich, HandPlatter } from 'lucide-vue-next';

const props = defineProps({
    meal: {
        type: String,
        required: true,
        validator: (value) => ['breakfast', 'lunch', 'dinner'].includes(value)
    },
    size: {
        type: String,
        default: 'w-4 h-4'
    }
});

const mealIcons = {
    breakfast: markRaw(Coffee),
    lunch: markRaw(Sandwich),
    dinner: markRaw(HandPlatter)
};

const currentIcon = computed(() => {
    return mealIcons[props.meal] ?? markRaw(Sandwich);
});
</script>

<template>
    <component :is="currentIcon" :class="size" role="img" :aria-label="meal" />
</template>
