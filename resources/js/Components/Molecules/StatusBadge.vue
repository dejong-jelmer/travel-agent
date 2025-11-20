<script setup>
import {
    Sparkles,
    CalendarClock,
    CalendarCheck2,
    RouteOff,
    SquareCheckBig,
} from 'lucide-vue-next';

const props = defineProps({
    status: {
        type: String,
        required: true,
        validator: (value) => [
            'new',
            'pending',
            'confirmed',
            'cancelled',
            'completed',
        ].includes(value)
    }
})

const icons = {
    'new': Sparkles,
    'pending': CalendarClock,
    'confirmed': CalendarCheck2,
    'cancelled': RouteOff,
    'completed': SquareCheckBig,

}

const pillType = {
    'new': 'success',
    'pending': 'accent',
    'confirmed': 'success',
    'cancelled': 'error',
    'completed': 'info',
}

</script>

<template>
    <Pill :type="pillType[status]" variant="transparent">
        <component :is="icons[status]" class="h-5 w-5 flex-shrink-0" />
        <span class="ml-2 w-full text-center text-gray-600">
            <slot></slot>
        </span>
    </Pill>
</template>
