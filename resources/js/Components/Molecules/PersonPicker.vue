<script setup>
import { ref, computed } from 'vue'
import { UserPlus } from 'lucide-vue-next'

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({ adults: 1, children: 0 })
    },
    minAdults: { type: Number, default: 1 },
    maxAdults: { type: Number, default: 8 },
    minChildren: { type: Number, default: 0 },
    maxChildren: { type: Number, default: 8 }
})
const open = ref(false)
// Emits
const emit = defineEmits(['update:modelValue'])

// Computed refs for adults and children
const adults = computed({
    get: () => props.modelValue.adults,
    set: (val) => emit('update:modelValue', { ...props.modelValue, adults: val })
})

const children = computed({
    get: () => props.modelValue.children,
    set: (val) => emit('update:modelValue', { ...props.modelValue, children: val })
})

const increment = (key) => {
    if (key === 'adults' && adults.value < props.maxAdults) adults.value++
    if (key === 'children' && children.value < props.maxChildren) children.value++
}

const decrement = (key) => {
    if (key === 'adults' && adults.value > props.minAdults) adults.value--
    if (key === 'children' && children.value > props.minChildren) children.value--
}
</script>
<template>
    <div class="relative space-y-2" >
        <div class="relative" @click="open = !open">
            <span class="absolute inset-y-0 left-0 flex items-center text-gray-400">
                <UserPlus class="ml-1 h-5 w-auto text-accent-primary" />
            </span>
            <input readonly :value="`${modelValue.adults} volwassenen - ${modelValue.children} kinderen`" type="text"
                class="w-full pl-10 pr-3 py-2 pt-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none cursor-pointer" />
        </div>
        <div v-if="open" class="space-y-2">
            <!-- Adults -->
            <div class="flex items-center justify-between">
                <label class="text-brand-primary font-medium">Volwassenen</label>
                <div class="flex items-center gap-2">
                    <button @click="decrement('adults')" :disabled="adults.value <= minAdults"
                        class="w-8 h-8 flex items-center justify-center rounded-full border border-brand-primary text-brand-primary disabled:opacity-40">
                        −
                    </button>
                    <span class="w-6 text-center text-lg font-semibold">{{ adults }}</span>
                    <button @click="increment('adults')" :disabled="adults.value >= maxAdults"
                        class="w-8 h-8 flex items-center justify-center rounded-full border border-brand-primary text-brand-primary disabled:opacity-40">
                        +
                    </button>
                </div>
            </div>

            <!-- Childeren -->
            <div class="flex items-center justify-between">
                <label class="text-brand-primary font-medium">Kinderen (tot 12 jaar)</label>
                <div class="flex items-center gap-2">

                    <button @click="decrement('children')" :disabled="children.value <= minChildren"
                        class="w-8 h-8 flex items-center justify-center rounded-full border border-brand-primary text-brand-primary disabled:opacity-40">
                        −
                    </button>
                    <span class="w-6 text-center text-lg font-semibold">{{ children }}</span>
                    <button @click="increment('children')"
                        class="w-8 h-8 flex items-center justify-center rounded-full border border-brand-primary text-brand-primary disabled:opacity-40">
                        +
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
