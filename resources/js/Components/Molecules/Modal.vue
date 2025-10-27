<script setup>
import { onMounted, onUnmounted } from 'vue';

const props = defineProps({
    open: {
        type: Boolean,
        required: true,
    },
});

const handleEscape = (e) => {
    if (e.key === 'Escape' && props.open) {
        emitClose();
    }
};

onMounted(() => document.addEventListener('keydown', handleEscape));
onUnmounted(() => document.removeEventListener('keydown', handleEscape));

const emit = defineEmits(["close"]);

const emitClose = () => {
    emit("close");
};
</script>
<template>
    <Transition
        name="modal-fade"
        enter-active-class="transition duration-200"
        leave-active-class="transition duration-200"
        enter-from-class="opacity-0"
        leave-to-class="opacity-0"
        >
        <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center p-4">

            <!-- backdrop -->
            <div class="absolute inset-0 bg-black/50 transition-opacity" @click="emitClose" />

            <Transition
                name="modal-scale"
                enter-active-class="transition duration-200"
                leave-active-class="transition duration-200"
                enter-from-class="opacity-0 scale-95"
                leave-to-class="opacity-0 scale-95"
                >
                <div v-if="open"
                    class="relative w-full max-w-5xl max-h-[90vh] bg-white shadow-xl rounded-2xl p-8 overflow-y-auto">
                    <button aria-label="close"
                        class="absolute top-0 right-0 p-3 rounded-tr-2xl rounded-bl-2xl bg-status-error text-white hover:bg-status-error/90 transition-colors"
                        @click.prevent="emitClose">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10
                   8.586l4.293-4.293a1 1 0 111.414
                   1.414L11.414 10l4.293 4.293a1 1
                   0 01-1.414 1.414L10 11.414l-4.293
                   4.293a1 1 0 01-1.414-1.414L8.586
                   10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="mt-2 text-primary-default">
                        <slot />
                    </div>
                </div>
            </Transition>
        </div>
    </Transition>
</template>
