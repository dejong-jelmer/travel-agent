<script setup>
const props = defineProps({
    itinerary: {
        type: Object
    },
    isAdmin: {
        type: Boolean,
        default: false
    }
})

</script>

<template>
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="grid grid-cols-1 tablet:grid-cols-5 gap-6 items-center">
            <!-- Text Content -->
            <div class="tablet:col-span-3 order-2 tablet:order-1 p-4 tablet:p-6 laptop:p-8 transition-all duration-300">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wide">
                            {{ $t('itinerary.day') }} {{ itinerary.order }}
                        </h2>
                        <h1 class="text-2xl font-semibold text-gray-900 mt-1">
                            {{ itinerary.title }}
                        </h1>
                    </div>
                    <!-- Admin Controls -->
                    <div v-if="isAdmin" class="flex gap-2 ml-4">
                        <IconLink type="info" icon="Pencil" :href="route('admin.itineraries.edit', itinerary)"
                            v-tippy="$t('itinerary.edit')" />
                        <IconLink type="delete" icon="Trash2" :href="route('admin.itineraries.destroy', itinerary)"
                            method="delete" :showConfirm="true" :prompt="$t('itinerary.delete_confirm')"
                            v-tippy="$t('itinerary.delete')" />
                    </div>
                </div>
                <h5 class="text-sm font-medium text-brand-primary mt-2">
                    {{ itinerary.subtitle }}
                </h5>
                <p class="text-gray-700 leading-relaxed mt-4 text-sm tablet:text-base">
                    {{ itinerary.description }}
                </p>

                <!-- Remark Section -->
                <div v-if="itinerary.remark" class="flex items-center mt-4">
                    <div
                        class="w-full p-3 bg-status-warning/10 rounded-lg border border-status-warning text-status-warning/80 text-sm flex items-center gap-3">
                        <Warning class="h-5 w-5" />{{ itinerary.remark }}
                    </div>
                </div>
            </div>

            <!-- Image -->
            <div class="tablet:col-span-2 order-1 tablet:order-2 h-full">
                <img :src="itinerary.image?.public_url" alt="itinerary image"
                    class="w-full h-full object-cover rounded-r-xl shadow-md">
            </div>
        </div>
    </div>
</template>
