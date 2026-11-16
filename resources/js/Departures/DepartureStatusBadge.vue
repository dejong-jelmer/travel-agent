<script setup>
import { CircleDot, ShieldCheck, Users, Ban, Flag } from '@lucide/vue';

// The status of a departure. A full departure that is guaranteed shows both.
defineProps({
    status: { type: Object, required: true },
    isGuaranteed: { type: Boolean, default: false },
});

const icons = { open: CircleDot, guaranteed: ShieldCheck, full: Users, cancelled: Ban, completed: Flag };
const pillType = { open: 'info', guaranteed: 'success', full: 'accent', cancelled: 'error', completed: 'sage' };
</script>

<template>
    <span class="inline-flex flex-wrap gap-2">
        <Pill :type="pillType[status.value]" variant="transparent">
            <component :is="icons[status.value]" class="h-4 w-4 flex-shrink-0" />
            <span class="ml-2 text-gray-600">{{ status.label }}</span>
        </Pill>
        <Pill v-if="isGuaranteed && status.value === 'full'" type="success" variant="transparent">
            <ShieldCheck class="h-4 w-4 flex-shrink-0" />
            <span class="ml-2 text-gray-600">{{ $t('admin.departures.status.guaranteed_too') }}</span>
        </Pill>
    </span>
</template>
