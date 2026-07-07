<script setup>
import { computed, markRaw } from 'vue';

import { Check, X, Plus } from 'lucide-vue-next';

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
    <div class="space-y-6">
        <template v-if="tripItems && Object.keys(tripItems).length > 0">
            <template v-for="(items, type) in tripItems" :key="type">
                <!-- Type Section (Inclusief/Exclusief) -->
                <div class="space-y-2">
                    <h4 class="text-base tablet:text-lg font-semibold text-brand-primary">
                        {{ type }}
                    </h4>

                    <div class="bg-white p-2 tablet:p-3">
                        <ul class="space-y-3">
                            <li v-for="(tripItem, index) in items" :key="index"
                                class="flex items-start gap-3">
                                <div class="flex-shrink-0 mt-0.5">
                                    <component
                                        :is="icons[tripItem.type]"
                                        :class="{
                                            'text-status-success': tripItem.type === 'inclusion',
                                            'text-status-error': tripItem.type === 'exclusion',
                                            'text-brand-accent': tripItem.type === 'optional',
                                        }"
                                        class="w-5 h-5"
                                    />
                                </div>
                                <span class="text-sm tablet:text-base text-brand-text leading-relaxed flex-1">
                                    {{ tripItem.item }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </template>
        </template>

        <!-- Empty State -->
        <div v-else class="bg-white rounded-lg border border-brand-accent/20 p-6 tablet:p-8 text-center">
            <p class="text-brand-light">
                {{ $t('trip_show.tab_content.inclusive_placeholder') }}
            </p>
        </div>
    </div>
</template>
