<script setup>
import { CheckCircle } from 'lucide-vue-next';
import { useRevealEffect } from '@/Composables/useRevealEffect.js';

import usp1 from '@/../images/usp1.jpg';
import usp2 from '@/../images/usp2.jpg';
import usp3 from '@/../images/usp3.jpg';

const { rootRef, visible, reveal } = useRevealEffect();

const usps = [
    {
        revealDelay: 0,
        image: usp1,
        titleKey: 'usp.usp-1.title',
        descriptionKey: 'usp.usp-1.description',
        highlightKey: 'usp.usp-1.highlight',
        alt: 'usp.usp-1.highlight'
    },
    {
        revealDelay: 50,
        image: usp2,
        titleKey: 'usp.usp-2.title',
        descriptionKey: 'usp.usp-2.description',
        highlightKey: 'usp.usp-2.highlight',
        alt: 'usp.usp-2.highlight'
    },
    {
        revealDelay: 100,
        image: usp3,
        titleKey: 'usp.usp-3.title',
        descriptionKey: 'usp.usp-3.description',
        highlightKey: 'usp.usp-3.highlight',
        alt: 'usp.usp-3.highlight'
    }
];
</script>
<template>
    <section ref="rootRef" id="over-de-reizen" class="py-12 tablet:py-24 max-w-6xl mx-auto px-4 scroll-mt-36">

        <!-- Header -->
        <div class="text-center mb-6 laptop:mb-12">
            <h2 class="sr-only">{{ $t('usp.heading') }}</h2>
            <p class="text-brand-text text-sm laptop:text-lg text-left max-w-2xl mx-auto leading-relaxed">
                {{ $t('usp.intro') }}
            </p>
        </div>

        <!-- USP Cards -->
        <div class="grid grid-cols-1 tablet:grid-cols-2 laptop:grid-cols-3 gap-8">
            <Card v-for="(usp, index) in usps" :key="index"
                class="transition-all duration-1000 ease-out relative overflow-hidden group flex flex-col"
                v-bind="reveal(usp.revealDelay)"
                :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'">
                <!-- Header afbeelding -->
                <div class="h-48 overflow-hidden">
                    <img :src="usp.image"
                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                        :alt="$t(usp.alt)" />
                </div>

                <div class="p-6 flex flex-col flex-1">
                    <!-- Titel -->
                    <h3
                        class="text-base laptop:text-xl font-bold text-brand-primary mb-3 group-hover:text-brand-accent transition-colors duration-300">
                        {{ $t(usp.titleKey) }}
                    </h3>

                    <!-- Beschrijving -->
                    <p class="text-brand-text text-sm laptop:text-base leading-relaxed mb-6 flex-1">
                        {{ $t(usp.descriptionKey) }}
                    </p>

                    <!-- Highlight -->
                    <div class="flex items-center gap-2.5 pt-4 border-t border-brand-primary/20">
                        <CheckCircle class="w-4 h-4 text-brand-accent flex-shrink-0" />
                        <span
                            class="text-sm text-brand-light font-medium group-hover:text-brand-accent transition-colors duration-300">
                            {{ $t(usp.highlightKey) }}
                        </span>
                    </div>
                </div>
            </Card>
        </div>

        <!-- Bottom CTA -->
        <div v-bind="reveal(300)" :class="visible ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-12'"
            class="transition-all duration-1000 ease-out text-center mt-12">
            <div
                class="inline-flex items-center gap-3 bg-white rounded-full px-6 py-3 shadow-sm border border-brand-subtle/20">
                <span class="text-brand-primary font-medium">{{ $t('usp.cta.text') }}</span>
                <div class="w-px h-4 bg-brand-subtle/30"></div>
                <DefaultLink :href="route('trips.index')">{{ $t('usp.cta.link') }} →</DefaultLink>
            </div>
        </div>

    </section>
</template>
