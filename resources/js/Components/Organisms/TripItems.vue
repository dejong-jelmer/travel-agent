<script setup>
import { Check, X } from 'lucide-vue-next';

const props = defineProps({
    tripItems: {
        type: Object,
        default: () => ({})
    }
});
</script>

<template>
    <div class="space-y-6">
        <template v-if="tripItems && Object.keys(tripItems).length > 0">
            <template v-for="(categories, type) in tripItems" :key="type">
                <!-- Type Section (Inclusief/Exclusief) -->
                <div class="space-y-4">
                    <h4 class="text-base tablet:text-lg font-semibold text-brand-primary mb-4">
                        {{ type }}
                    </h4>

                    <!-- Categories within type -->
                    <div v-for="(items, category) in categories" :key="category"
                         class="bg-white rounded-lg border border-accent-primary/20 p-4 tablet:p-6">
                        <h4 class="text-base tablet:text-lg font-semibold text-brand-primary mb-4">
                            {{ category }}
                        </h4>

                        <ul class="space-y-3">
                            <li v-for="(tripItem, index) in items" :key="index"
                                class="flex items-start gap-3">
                                <div class="flex-shrink-0 mt-0.5">
                                    <component
                                        :is="tripItem.is_inclusive ? Check : X"
                                        :class="tripItem.is_inclusive ? 'text-status-success' : 'text-status-error'"
                                        class="w-5 h-5"
                                    />
                                </div>
                                <span class="text-sm tablet:text-base text-accent-text leading-relaxed flex-1">
                                    {{ tripItem.item }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </template>
        </template>

        <!-- Empty State -->
        <div v-else class="bg-white rounded-lg border border-accent-primary/20 p-6 tablet:p-8 text-center">
            <p class="text-brand-light">
                {{ $t('trip_show.tab_content.inclusive_placeholder') }}
            </p>
        </div>
    </div>
</template>
