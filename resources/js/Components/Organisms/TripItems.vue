<script setup>
import { computed, markRaw } from 'vue';

import { Check, X, Plus } from '@lucide/vue';

const props = defineProps({
    tripItems: {
        type: Object,
        default: () => ({})
    }
});

const icons = {
    inclusion: markRaw(Check),
    exclusion: markRaw(X),
    optional: markRaw(Plus),
};

</script>

<template>
    <BaseCard aria-labelledby="trip-items-heading">
        <SectionHeader id="trip-items-heading">{{ $t('trip_show.trip_items_heading') }}</SectionHeader>

        <!-- One column per type (Inclusief/Exclusief/Optioneel): side by side from tablet up, stacked on phones -->
        <div v-if="tripItems && Object.keys(tripItems).length > 0" class="grid grid-cols-1 tablet:grid-cols-2 gap-8">
            <div v-for="(items, type) in tripItems" :key="type">
                <h3 class="text-lg font-semibold text-brand-primary mb-3">
                    {{ type }}
                </h3>

                <ul class="space-y-3">
                    <li v-for="(tripItem, index) in items" :key="index" class="flex items-start gap-2">
                        <!-- mt-1 centres the 16px icon on the first 24px line of text instead of the whole item -->
                        <component
                            :is="icons[tripItem.type]"
                            :class="{
                                'text-brand-primary': tripItem.type === 'inclusion',
                                'text-brand-light': tripItem.type === 'exclusion',
                                'text-brand-accent': tripItem.type === 'optional',
                            }"
                            class="w-4 h-4 mt-1 flex-shrink-0"
                        />
                        <span class="text-[15.5px] leading-[1.55] text-brand-text flex-1">
                            {{ tripItem.item }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Empty State -->
        <p v-else class="text-brand-light">
            {{ $t('trip_show.tab_content.inclusive_placeholder') }}
        </p>
    </BaseCard>
</template>
