<script setup>
const props = defineProps({
    destinations: {
        type: Array,
        default: () => []
    },
    travelInfoSections: {
        type: Object,
        required: true
    }
})

// Group destinations by their travel info content
const groupByContent = (key) => {
    const groups = {}

    props.destinations?.forEach(destination => {
        const content = destination.travel_info?.[key]
        if (!content) return

        // Use content as key to group destinations with identical info
        if (!groups[content]) {
            groups[content] = {
                content,
                destinations: []
            }
        }
        groups[content].destinations.push(destination.name)
    })

    return Object.values(groups)
}

// Check if any destination has info for this section
const getDestinationsWithInfo = (key) => {
    return props.destinations?.filter(d => d.travel_info?.[key]) || []
}
</script>

<template>
    <template v-for="(label, key) in travelInfoSections" :key="key">
        <div v-if="getDestinationsWithInfo(key).length > 0" class="p-2 tablet:p-4">
            <h4 class="text-base tablet:text-lg font-semibold text-brand-primary mb-2">
                {{ label }}
            </h4>
            <template v-for="(group, index) in groupByContent(key)" :key="index">
                <div class="mt-4">
                    <h5 v-if="group.destinations > 1"  class="text-sm tablet:text-base font-bold text-brand-primary mb-1">
                        {{ group.destinations.join(', ') }}
                    </h5>
                    <div
                        class="text-sm tablet:text-base text-accent-text leading-relaxed whitespace-pre-line">
                        {{ group.content }}
                    </div>
                </div>
            </template>
        </div>
    </template>
</template>
