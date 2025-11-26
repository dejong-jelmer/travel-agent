<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { ChevronRightIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    breadcrumbs: Object
})

const firstBreadcrumb = computed(() => props.breadcrumbs?.[0] || null);
const lastBreadcrumb = computed(() => {
    const length = props.breadcrumbs?.length || 0;
    return length > 0 ? props.breadcrumbs[length - 1] : null;
});
const hasMultipleBreadcrumbs = computed(() => (props.breadcrumbs?.length || 0) > 2);

</script>
<template>
    <nav v-if="breadcrumbs?.length" class="flex items-center py-6 text-sm laptop:text-base text-gray-500">
        <ul v-if="hasMultipleBreadcrumbs" class="flex items-center laptop:hidden">
            <!-- First breadcrumb -->
            <li>
                <span v-if="firstBreadcrumb.route">
                    <Link :href="route(firstBreadcrumb.route, firstBreadcrumb.params ?? [])" class="text-gray-700 hover:underline cursor-pointer">
                        {{ firstBreadcrumb.label }}
                    </Link>
                </span>
                <span v-else>{{ firstBreadcrumb.label }}</span>
                <ChevronRightIcon class="inline h-4 w-4 mx-1" />
            </li>

            <!-- Ellipsis -->
            <li>
                <span class="text-gray-500">...</span>
                <ChevronRightIcon class="inline h-4 w-4 mx-1" />
            </li>

            <!-- Last breadcrumb -->
            <li>
                <span v-if="lastBreadcrumb.route">
                    <Link :href="route(lastBreadcrumb.route, lastBreadcrumb.params ?? [])" class="text-gray-700 hover:underline cursor-pointer">
                        {{ lastBreadcrumb.label }}
                    </Link>
                </span>
                <span v-else>{{ lastBreadcrumb.label }}</span>
            </li>
        </ul>

        <!-- Mobile versie voor <= 2 items: toon gewoon alle items -->
        <ul v-if="!hasMultipleBreadcrumbs" class="flex items-center laptop:hidden">
            <li v-for="(crumb, index) in breadcrumbs" :key="index">
                <span v-if="crumb.route">
                    <Link :href="route(crumb.route, crumb.params ?? [])" class="text-gray-700 hover:underline cursor-pointer">
                        {{ crumb.label }}
                    </Link>
                </span>
                <span v-else>{{ crumb.label }}</span>

                <span v-if="index < breadcrumbs.length - 1">
                    <ChevronRightIcon class="inline h-4 w-4 mx-1" />
                </span>
            </li>
        </ul>

        <!-- Desktop versie: alle items (verborgen op mobile, zichtbaar op laptop+) -->
        <ul class="hidden laptop:flex items-center">
            <li v-for="(crumb, index) in breadcrumbs" :key="index">
                <span v-if="crumb.route">
                    <Link :href="route(crumb.route, crumb.params ?? [])" class="text-gray-700 hover:underline cursor-pointer">
                        {{ crumb.label }}
                    </Link>
                </span>
                <span v-else>{{ crumb.label }}</span>

                <span v-if="index < breadcrumbs.length - 1">
                    <ChevronRightIcon class="inline h-4 w-4 mx-1 laptop:mx-2" />
                </span>
            </li>
        </ul>
    </nav>
</template>
