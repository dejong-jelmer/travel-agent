<script setup>
import {
    Hourglass,
    Coins,
    BanknoteArrowUp,
    BanknoteArrowDown,
    HandCoins,
    BanknoteX,
} from 'lucide-vue-next';

const props = defineProps({
    status: {
        type: String,
        required: true,
        validator: (value) => [
            'pending',
            'partial_paid',
            'paid',
            'refunded',
            'partially_refunded',
            'failed',
        ].includes(value)
    }
})

const icons = {
    'pending': Hourglass,
    'partial_paid': Coins,
    'paid': BanknoteArrowUp,
    'refunded': BanknoteArrowDown,
    'partially_refunded': HandCoins,
    'failed': BanknoteX,

}

const pillType = {
    'pending': 'warning',
    'partial_paid': 'accent',
    'paid': 'success',
    'refunded': 'info',
    'partially_refunded': 'warning',
    'failed': 'error',
}

</script>

<template>
    <Pill :type="pillType[props.status]" variant="transparent">
        <component :is="icons[props.status]" class="h-5 w-5 flex-shrink-0" />
        <span class="ml-2 w-full text-center text-gray-600">
            <slot></slot>
        </span>
    </Pill>
</template>
