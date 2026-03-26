<script setup>
defineProps({
    booker: Object
});
</script>

<template>
    <Accordion>
        <template #header>
            <span><strong>{{ booker.full_name }}</strong> ({{ booker.birthdate_formatted }})</span>
        </template>
        <div class="text-brand-primary">
            <p><span class="font-medium">{{ $t('booker.birthdate') }}:</span> {{ booker.birthdate_formatted }}</p>
            <p><span class="font-medium">{{ $t('booker.nationality') }}:</span> {{ booker.nationality }}</p>
<<<<<<< HEAD
            <p v-if="booker.special_requests"><span class="font-medium">{{ $t('booker.special_requests') }}:</span> {{ booker.special_requests }}</p>
=======
>>>>>>> b9e884b3fa401a1a668de43a24b0f92b9502b33e
        </div>
    </Accordion>
</template>
