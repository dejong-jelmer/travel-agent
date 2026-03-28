<script setup>
import { Leaf, Phone } from 'lucide-vue-next';
</script>
<template>
    <div class="w-full bg-brand-secondary">
        <div
            class="max-w-screen-desktop mx-auto px-6 py-2 flex justify-between">
            <div class="flex items-center gap-x-2 laptop:gap-x-3">
                <p class="text-xs laptop:text-sm text-brand-primary">
                    {{ $t('topbar_text') }}
                </p>
            </div>
            <div class="flex items-center gap-x-2 laptop:gap-x-3">
                <Phone class="h-5 text-brand-accent drop-shadow-sm" />
                <a class="text-xs laptop:text-sm tel-field text-brand-primary underline-offset-4"></a>
            </div>
        </div>
    </div>
</template>
