<script setup>
import { useToast } from "vue-toastification";
import { watch } from "vue";
const toast = useToast();

const props = defineProps({
    type: String,
    message: String,
});

watch(
    () => props.message,
    (newMessage) => {
        if (newMessage) {
            toast[props.type](newMessage);
        }
    }
);
</script>

<template>
    <div></div>
</template>
