<script setup>
import DownLine from '@/Icons/DownLine.vue'
import { ref } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: false
    },
    initiallyOpen: {
        type: Boolean,
        default: false
    }
});

const isOpen = ref(props.initiallyOpen);

const toggle = () => {
    isOpen.value = !isOpen.value;
};

defineExpose({
    toggle,
    isOpen
});
</script>

<template>
    <div class="border-b py-2">
        <button class="flex justify-between items-center w-full text-left" @click="toggle">
            <slot name="trigger" :isOpen="isOpen">
                <span class="text-brand-primary">
                    {{ title }}
                </span>
            </slot>
            <span>
                <DownLine
                    :class="isOpen ? 'rotate-180' : ''"
                    class="w-4 h-4 transform transition-transform"
                 />
            </span>
        </button>

        <div v-if="isOpen" class="mt-2 ml-4">
            <slot :isOpen="isOpen"></slot>
        </div>
    </div>
</template>
