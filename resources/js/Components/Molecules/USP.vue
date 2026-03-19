<script setup>
import { CheckCircle } from 'lucide-vue-next';

import routesImage from '@/../images/route.webp';
import trainImage from '@/../images/train.webp';
import uniqueImage from '@/../images/unique.webp';

const usps = [
    {
        image: routesImage,
        titleKey: 'usp.routes.title',
        descriptionKey: 'usp.routes.description',
        highlightKey: 'usp.routes.highlight'
    },
    {
        image: trainImage,
        titleKey: 'usp.train.title',
        descriptionKey: 'usp.train.description',
        highlightKey: 'usp.train.highlight'
    },
    {
        image: uniqueImage,
        titleKey: 'usp.unique.title',
        descriptionKey: 'usp.unique.description',
        highlightKey: 'usp.unique.highlight'
    }
];
</script>
<template>
    <div class="max-w-6xl mx-auto">

        <!-- Header -->
        <div class="text-center mb-12">
            <SectionHeader>{{ $t('usp.section_header') }}</SectionHeader>
            <p class="text-brand-text text-sm laptop:text-base text-left max-w-2xl mx-auto leading-relaxed">
                {{ $t('usp.intro') }}
            </p>
        </div>

        <!-- USP Cards -->
        <div class="grid grid-cols-1 tablet:grid-cols-2 laptop:grid-cols-3 gap-8">
            <Card
                v-for="(usp, index) in usps"
                :key="index"
                class="relative overflow-hidden group flex flex-col"
            >
                <!-- Header afbeelding -->
                <div class="h-48 overflow-hidden">
                    <img :src="usp.image" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                </div>

                <div class="p-6 flex flex-col flex-1">
                <!-- Titel -->
                <h3 class="text-base laptop:text-xl font-bold text-brand-primary mb-3 group-hover:text-brand-accent transition-colors duration-300">
                    {{ $t(usp.titleKey) }}
                </h3>

                <!-- Beschrijving -->
                <p class="text-brand-text text-sm laptop:text-base leading-relaxed mb-6 flex-1">
                    {{ $t(usp.descriptionKey) }}
                </p>

                <!-- Highlight -->
                <div class="flex items-center gap-2.5 pt-4 border-t border-brand-primary/20">
                    <CheckCircle class="w-4 h-4 text-brand-accent flex-shrink-0" />
                    <span class="text-sm text-brand-light font-medium group-hover:text-brand-accent transition-colors duration-300">
                        {{ $t(usp.highlightKey) }}
                    </span>
                </div>
                </div>
            </Card>
        </div>

        <!-- Bottom CTA -->
        <div class="text-center mt-12">
            <div class="inline-flex items-center gap-3 bg-white rounded-full px-6 py-3 shadow-sm border border-brand-subtle/20">
                <span class="text-brand-primary font-medium">{{ $t('usp.cta.text') }}</span>
                <div class="w-px h-4 bg-brand-subtle/30"></div>
                <DefaultLink :href="route('trips')">{{ $t('usp.cta.link') }} →</DefaultLink>
            </div>
        </div>

    </div>
</template>

