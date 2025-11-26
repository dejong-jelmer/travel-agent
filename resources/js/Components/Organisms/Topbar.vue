<script setup>
import { Leaf, Phone } from 'lucide-vue-next';
</script>
<template>
    <div class="w-full bg-brand-secondary">
        <div
            class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-6 py-2 flex gap-x-3 laptop:gap-x-8 justify-between tablet:justify-start">
            <div class="flex items-center gap-x-2 laptop:gap-x-3">
                <Leaf class="h-5 laptop:h-6 text-accent-primary drop-shadow-sm" />
                <p class="text-xs laptop:text-sm text-brand-primary">
                    Ga duurzaam op reis
                </p>
            </div>
            <div class="flex items-center gap-x-2 laptop:gap-x-3">
                <Phone class="h-5 laptop:h-6 text-accent-primary drop-shadow-sm" />
                <a class="text-xs laptop:text-sm tel-field text-brand-primary underline-offset-4"></a>
            </div>
        </div>
    </div>
</template>
