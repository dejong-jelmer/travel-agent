<script setup>
    import { Phone } from 'lucide-vue-next';
</script>

<template>
    <a href="#"
        class="tel-field has-icon contact-button">
        <Phone class="w-5 h-5" />
        {{ $t('button.call') }}
    </a>
</template>
