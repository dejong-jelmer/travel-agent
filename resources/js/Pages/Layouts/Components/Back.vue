<script setup>
import { Back } from '@/Pages/Icons'
const goBack = () => {
    window.history.back();
};
</script>

<template>
    <button @click="goBack" class="p-1 w-9 mb-2 info-button">
        <Back class="h-5"/>
    </button>
</template>
