<script setup>
defineProps({
    // Shape: [{ label, value, icon }]; a fact without a label shows its value only
    facts: { type: Array, default: () => [] },
});
</script>

<template>
    <section v-if="facts?.length" aria-labelledby="trip-key-facts-heading"
        class="border border-brand-accent/20 rounded-xl p-5 shadow-lg">
        <h3 id="trip-key-facts-heading" class="text-lg font-semibold text-brand-primary mb-4">
            {{ $t('trip_show.key_facts_heading') }}
        </h3>
        <ul class="space-y-5">
            <li v-for="(fact, index) in facts" :key="index" class="flex items-start gap-4">
                <KeyFactIcon :icon="fact.icon" class="w-6 h-6 flex-shrink-0 text-brand-primary" />
                <div class="min-w-0">
                    <span v-if="fact.label" class="block text-sm font-semibold leading-5 text-brand-primary">
                        {{ fact.label }}
                    </span>
                    <span class="block text-base leading-6 text-brand-text">{{ fact.value }}</span>
                </div>
            </li>
        </ul>
    </section>
</template>
