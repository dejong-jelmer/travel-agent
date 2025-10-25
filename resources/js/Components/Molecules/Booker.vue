<script setup>
defineProps({
    booker: Object
});
</script>

<template>
    <Accordion>
        <template #trigger="{ isOpen }">
            <span class="text-primary-dark">
                <strong>{{ booker.full_name }}</strong> ({{ booker.birthdate_formatted }})
            </span>
        </template>
    </Accordion>
</template>
