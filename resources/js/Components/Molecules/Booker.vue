<script setup>
defineProps({
    booker: Object
});
</script>

<template>
    <Accordion>
        <template #trigger>
            <span><strong>{{ booker.full_name }}</strong> ({{ booker.birthdate_formatted }})</span>
        </template>
        <div class="text-brand-primary">
            <p><span class="font-medium">Geboortedatum:</span> {{ booker.birthdate_formatted }}</p>
            <p><span class="font-medium">Nationaliteit:</span> {{ booker.nationality }}</p>
        </div>
    </Accordion>
</template>
