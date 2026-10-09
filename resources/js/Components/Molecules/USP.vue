<script setup>
import { CheckCircle } from '@lucide/vue';

import usp1 from '@/../images/usp1.jpg';
import usp2 from '@/../images/usp2.jpg';
import usp3 from '@/../images/usp3.jpg';

const usps = [
    {
        image: usp1,
        titleKey: 'usp.usp-1.title',
        descriptionKey: 'usp.usp-1.description',
        highlightKey: 'usp.usp-1.highlight',
        alt: 'usp.usp-1.highlight'
    },
    {
        image: usp2,
        titleKey: 'usp.usp-2.title',
        descriptionKey: 'usp.usp-2.description',
        highlightKey: 'usp.usp-2.highlight',
        alt: 'usp.usp-2.highlight'
    },
    {
        image: usp3,
        titleKey: 'usp.usp-3.title',
        descriptionKey: 'usp.usp-3.description',
        highlightKey: 'usp.usp-3.highlight',
        alt: 'usp.usp-3.highlight'
    }
];
</script>
<template>
    <section id="over-de-reizen" class="py-12 tablet:py-24 max-w-6xl mx-auto px-4 scroll-mt-36">

        <!-- Header -->
        <div class="text-center mb-6 laptop:mb-12">
            <h2 class="sr-only">{{ $t('usp.heading') }}</h2>
            <p class="text-brand-text text-sm laptop:text-lg text-left max-w-2xl mx-auto leading-relaxed">
                {{ $t('usp.intro') }}
            </p>
        </div>

        <!-- USP Cards, the cards of a row coming into view one after the other (three columns from laptop on) -->
        <div class="grid grid-cols-1 tablet:grid-cols-2 laptop:grid-cols-3 gap-8">
            <Card v-for="(usp, index) in usps" :key="index" v-reveal="{ delay: (index % 3) * 80 }"
                class="relative overflow-hidden group flex flex-col">
                <!-- Header afbeelding -->
                <div class="h-48 overflow-hidden">
                    <img :src="usp.image"
                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                        :alt="$t(usp.alt || 'usp ' + index)" />
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
        <div v-reveal class="text-center mt-12">
            <div
                class="inline-flex items-center gap-3 bg-white rounded-full px-6 py-3 shadow-sm border border-brand-subtle/20 text-sm laptop:text-base">
                <span class="text-brand-primary font-medium">{{ $t('usp.cta.text') }}</span>
                <div class="w-px h-4 bg-brand-text"></div>
                <DefaultLink :href="route('trips.index')">{{ $t('usp.cta.link') }}&nbsp;→</DefaultLink>
            </div>
        </div>

    </section>
</template>
