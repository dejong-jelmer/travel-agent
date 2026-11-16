<script setup>
// A quiet label for a trait of a trip, such as its type or format.
// - outline: no fill, so it reads as information rather than as a sticker. Brand-primary on white: 7:1.
// - highlight: a warm earth fill, for a trait that is the exception and deserves a little more attention, such as a
//   group trip. Brand-text on earth/45 over white: 11:1.
defineProps({
    label: { type: String, required: true },
    variant: {
        type: String,
        default: 'outline',
        validator: (value) => ['outline', 'highlight'].includes(value),
    },
});
</script>

<template>
    <span
        :class="variant === 'highlight'
            ? 'border-transparent bg-brand-earth/45 font-medium text-brand-text'
            : 'border-brand-primary/25 text-brand-primary'"
        class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[13px] leading-5 whitespace-nowrap">
        {{ label }}
    </span>
</template>
