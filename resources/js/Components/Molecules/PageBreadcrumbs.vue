<script setup>
import { Link } from '@inertiajs/vue3'
import { ChevronRight } from '@lucide/vue'
import { useI18n } from 'vue-i18n'

// Trail of { label, url } items from BreadcrumbTrail::toArray(); the last item is the current page
const props = defineProps({
    items: {
        type: Array,
        default: () => []
    }
})

const { t } = useI18n()

const isLast = (index) => index === props.items.length - 1
</script>

<template>
    <nav v-if="items.length" :aria-label="t('nav.breadcrumbs')">
        <!-- Always one line: only the current page may shrink, it is cut off with an ellipsis -->
        <ol class="flex items-center min-w-0 text-sm whitespace-nowrap">
            <li v-for="(item, index) in items" :key="index" class="flex items-center"
                :class="isLast(index) ? 'min-w-0' : 'shrink-0'">
                <span v-if="isLast(index)" aria-current="page" class="truncate text-brand-text">
                    {{ item.label }}
                </span>
                <template v-else>
                    <Link :href="item.url" class="text-brand-primary hover:underline">
                        {{ item.label }}
                    </Link>
                    <ChevronRight class="w-3.5 h-3.5 mx-1.5 shrink-0 text-brand-primary/60" aria-hidden="true" />
                </template>
            </li>
        </ol>
    </nav>
</template>
