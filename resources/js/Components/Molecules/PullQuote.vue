<script setup>
import { useRevealEffect } from '@/Composables/useRevealEffect.js';
const { rootRef, visible, reveal } = useRevealEffect();

</script>
<template>
    <section ref="rootRef" class="bg-white py-16 tablet:py-24 px-6 text-left">
        <div class="max-w-4xl mx-auto transition-all duration-1000 ease-out" v-bind="reveal(0)"
            :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'">
            <div class="flex items-center gap-3 mb-5">
                <span class="block w-10 h-0.5 bg-brand-accent"></span>
                <p class="text-xs font-semibold tracking-[0.22em] uppercase text-brand-accent">
                    {{ $t('home.pullquote_eyebrow') }}
                </p>
            </div>
            <div class="font-poppins text-6xl laptop:text-7xl leading-none text-brand-accent select-none mb-2">
                "
            </div>
            <p class="font-poppins text-2xl laptop:text-3xl text-brand-primary leading-snug -mt-8">
                {{ $t('home.pullquote') }}
            </p>
            <p class="mt-6 font-poppins text-sm text-brand-light tracking-widest italic">
                — {{ $t('home.pullquote_source') }}
            </p>
        </div>
    </section>
</template>
