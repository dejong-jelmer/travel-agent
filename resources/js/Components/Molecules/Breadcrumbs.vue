<script setup>
import { Link } from '@inertiajs/vue3';
import { ChevronRightIcon } from '@heroicons/vue/24/outline'
import { useI18n } from 'vue-i18n';

// Trail from App\Helpers\Breadcrumbs: { label, route?, params? }; the last item is the current page
const props = defineProps({
    breadcrumbs: {
        type: Array,
        default: () => []
    }
})

const { t } = useI18n()

const isLast = (index) => index === props.breadcrumbs.length - 1
// Below laptop the items between the first and the last collapse into an ellipsis
const isMiddle = (index) => index > 0 && !isLast(index)
</script>
<template>
    <nav v-if="breadcrumbs.length" :aria-label="t('nav.breadcrumbs')" class="py-6 text-sm laptop:text-base text-gray-500">
        <!-- Always one line: only the last item may shrink, it is cut off with an ellipsis -->
        <ol class="flex items-center min-w-0 whitespace-nowrap">
            <template v-for="(crumb, index) in breadcrumbs" :key="index">
                <li v-if="index === 1 && isMiddle(index)" class="flex items-center shrink-0 laptop:hidden"
                    aria-hidden="true">
                    ...
                    <ChevronRightIcon class="h-4 w-4 mx-1" />
                </li>
                <li class="items-center"
                    :class="[isMiddle(index) ? 'hidden laptop:flex' : 'flex', isLast(index) ? 'min-w-0' : 'shrink-0']">
                    <Link v-if="crumb.route" :href="route(crumb.route, crumb.params ?? [])"
                        :aria-current="isLast(index) ? 'page' : undefined"
                        :class="{ truncate: isLast(index) }" class="text-gray-700 hover:underline">
                        {{ crumb.label }}
                    </Link>
                    <span v-else :aria-current="isLast(index) ? 'page' : undefined"
                        :class="{ truncate: isLast(index) }">
                        {{ crumb.label }}
                    </span>
                    <ChevronRightIcon v-if="!isLast(index)" class="h-4 w-4 mx-1 laptop:mx-2 shrink-0" />
                </li>
            </template>
        </ol>
    </nav>
</template>
