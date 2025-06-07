<script setup>
import { Link } from '@inertiajs/vue3';
import { ChevronRightIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    breadcrumbs: Object
})

</script>
<template>
    <nav v-if="breadcrumbs?.length" class="text-base text-gray-500 m-[20px]">
        <ul class="flex">
            <li v-for="(crumb, index) in breadcrumbs" :key="index">
                <span v-if="crumb.route">
                    <Link :href="route(crumb.route, crumb.params ?? [])" class="text-blue-600 hover:underline cursor-pointer">
                    {{ crumb.label }}
                    </Link>
                </span>
                <span v-else>{{ crumb.label }}</span>

                <span v-if="index < breadcrumbs.length - 1">
                    <ChevronRightIcon class="inline h-4 w-4 mx-2"/>
                </span>
            </li>
        </ul>
    </nav>
</template>
