<script>

export default {
    props: {
        itinerary: {
            type: Object
        },
        isAdmin: {
            type: Boolean,
            default: false
        }
    }
}
</script>

<template>
    <div class="relative bg-white rounded-2xl shadow-lg overflow-hidden"
        :class="{ 'cursor-grab': isAdmin }">

        <!-- Admin Controls -->
        <div v-if="isAdmin" class="absolute top-2 right-2 flex flex-col gap-2">
            <IconLink  type="delete" icon="Delete" :href="route('admin.itineraries.destroy', itinerary)" method="delete" :showConfirm="true"
                prompt="Weet je zeker dat je dit reisplan wilt verwijderen?" />
            <IconLink icon="Edit" :href="route('admin.itineraries.edit', itinerary)" />
        </div>

        <div class="grid grid-cols-1 tablet:grid-cols-5 gap-6 items-center ">
            <!-- Text Content -->
            <div class="tablet:col-span-3 order-2 tablet:order-1 p-4 tablet:p-6 laptop:p-8 transition-all duration-300">
                <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wide">
                    Dag {{ itinerary.order }}
                </h2>
                <h1 class="text-2xl font-semibold text-gray-900 mt-1">
                    {{ itinerary.title }}
                </h1>
                <h5 class="text-sm font-medium text-custom-primary mt-2">
                    {{ itinerary.subtitle }}
                </h5>
                <p class="text-gray-700 leading-relaxed mt-4 text-sm tablet:text-base">
                    {{ itinerary.description }}
                </p>

                <!-- Remark Section -->
                <div v-if="itinerary.remark" class="flex items-center mt-4">
                    <div
                        class="w-full p-3 bg-yellow-100 rounded-lg border border-yellow-500 text-yellow-800 text-sm flex items-center gap-3">
                        <Warning class="h-5 w-5" />{{ itinerary.remark }}
                    </div>
                </div>
            </div>

            <!-- Image -->
            <div class="tablet:col-span-2 order-1 tablet:order-2 h-full">
                <img :src="itinerary.image?.path" alt="Reisbeschrijving"
                    class="w-full h-full object-cover rounded-r-xl shadow-md">
            </div>
        </div>
    </div>
</template>
