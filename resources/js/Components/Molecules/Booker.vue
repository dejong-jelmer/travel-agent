<template>
    <Accordion>
        <template #trigger="{ isOpen }">
            <span class="text-primary-dark">
                <strong>{{ booker.full_name }}</strong> ({{ booker.birthdate_formatted }})
            </span>
        </template>

        <template #default>
            <div class="text-primary-default">
                <p><span class="font-medium">Geboortedatum:</span> {{ booker.birthdate_formatted }}</p>
                <p><span class="font-medium">Nationaliteit:</span> {{ booker.nationality }}</p>
            </div>
        </template>
    </Accordion>
</template>

<script setup>
defineProps({
    booker: Object
});
</script>
