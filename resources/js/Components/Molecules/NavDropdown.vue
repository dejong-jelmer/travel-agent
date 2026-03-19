<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    label: { type: String, required: true },
    href: { type: String, required: true },
    items: { type: Array, default: () => [] }, // [{ label, href }]
    variant: {
        type: String,
        default: 'desktop',
        validator: (v) => ['desktop', 'mobile'].includes(v),
    },
});

const emit = defineEmits(['close']);

// Desktop: hover-based open/close with slight close delay
const isOpen = ref(false);
let closeTimer = null;

function onMouseEnter() {
    clearTimeout(closeTimer);
    isOpen.value = true;
}

function onMouseLeave() {
    closeTimer = setTimeout(() => { isOpen.value = false; }, 150);
}

// Mobile: click-based accordion
const expanded = ref(false);

function handleItemClick() {
    expanded.value = false;
    emit('close');
}
</script>

<template>
    <!-- ── Desktop variant ─────────────────────────────────────── -->
    <div
        v-if="variant === 'desktop'"
        class="relative"
        @mouseenter="onMouseEnter"
        @mouseleave="onMouseLeave"
    >
        <!-- Trigger row -->
        <div class="flex items-center gap-0.5">
            <Link
                :href="href"
                class="relative text-brand-primary text-sm laptop:text-base transition-all duration-200 after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-brand-accent after:transition-all after:duration-300 hover:after:w-full"
            >
                {{ label }}
            </Link>
            <!-- Chevron toggle -->
            <button
                @click="isOpen = !isOpen"
                class="p-0.5 text-brand-primary hover:text-brand-accent transition-colors duration-200 focus:outline-none"
                :aria-expanded="isOpen"
                :aria-label="label"
            >
                <svg
                    class="w-3.5 h-3.5 transition-transform duration-200"
                    :class="{ 'rotate-180': isOpen }"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </div>

        <!-- Dropdown panel -->
        <Transition
            enter-active-class="transition-all duration-200 ease-out"
            leave-active-class="transition-all duration-150 ease-in"
            enter-from-class="opacity-0 -translate-y-1"
            leave-to-class="opacity-0 -translate-y-1"
        >
            <div
                v-show="isOpen"
                class="absolute top-full left-1/2 -translate-x-1/2 mt-3 z-50 bg-white rounded-2xl shadow-xl border border-brand-primary/10 py-2 min-w-48"
                @mouseenter="onMouseEnter"
                @mouseleave="onMouseLeave"
            >
                <!-- All trips link -->
                <Link
                    :href="href"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-brand-primary hover:bg-brand-secondary transition-colors duration-150 rounded-lg mx-1"
                    @click="isOpen = false"
                >
                    {{ items[0]?.label ?? label }}
                    <svg class="w-3.5 h-3.5 ml-auto opacity-50" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </Link>

                <hr class="my-1 border-brand-primary/10 mx-3" />

                <!-- Country items (skip first since it's the "all" link) -->
                <Link
                    v-for="item in items.slice(1)"
                    :key="item.href"
                    :href="item.href"
                    class="block px-4 py-2 text-sm text-brand-text hover:bg-brand-secondary hover:text-brand-primary transition-colors duration-150 rounded-lg mx-1"
                    @click="isOpen = false"
                >
                    {{ item.label }}
                </Link>
            </div>
        </Transition>
    </div>

    <!-- ── Mobile variant (accordion) ───────────────────────────── -->
    <div v-else class="w-full">
        <!-- Accordion trigger -->
        <button
            @click="expanded = !expanded"
            class="flex items-center justify-between w-full px-4 py-3 text-brand-primary text-base font-medium rounded-lg mx-auto my-1.5 max-w-[200px] hover:bg-brand-secondary transition-colors duration-200"
        >
            <span>{{ label }}</span>
            <svg
                class="w-4 h-4 transition-transform duration-200 flex-none"
                :class="{ 'rotate-180': expanded }"
                xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- Accordion items -->
        <Transition
            enter-active-class="transition-all duration-200 ease-out"
            leave-active-class="transition-all duration-150 ease-in"
            enter-from-class="opacity-0 -translate-y-1"
            leave-to-class="opacity-0 -translate-y-1"
        >
            <div v-show="expanded" class="pb-1">
                <Link
                    v-for="item in items"
                    :key="item.href"
                    :href="item.href"
                    class="block px-4 py-2 text-sm text-brand-text hover:text-brand-primary hover:bg-brand-secondary transition-colors duration-150 rounded-lg mx-auto max-w-[200px] text-center"
                    @click="handleItemClick"
                >
                    {{ item.label }}
                </Link>
            </div>
        </Transition>
    </div>
</template>
