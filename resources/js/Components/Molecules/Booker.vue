<script setup>
defineProps({
    booker: Object
});
</script>

<template>
    <Accordion>
        <template #trigger>
            <span>{{ booker.full_name }} ({{ booker.birthdate_formatted }})</span>
        </template>
        <div class="text-primary-default">
            <p><span class="font-medium">Geboortedatum:</span> {{ booker.birthdate_formatted }}</p>
            <p><span class="font-medium">Nationaliteit:</span> {{ booker.nationality }}</p>
        </div>
    </Accordion>
</template>
