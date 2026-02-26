<script setup>
import { Leaf, Castle, Handshake } from 'lucide-vue-next';

const usps = [
    {
        icon: Leaf,
        titleKey: 'usp.sustainable.title',
        descriptionKey: 'usp.sustainable.description',
        highlightKey: 'usp.sustainable.highlight'
    },
    {
        icon: Castle,
        titleKey: 'usp.with_story.title',
        descriptionKey: 'usp.with_story.description',
        highlightKey: 'usp.with_story.highlight'
    },
    {
        icon: Handshake,
        titleKey: 'usp.carefree.title',
        descriptionKey: 'usp.carefree.description',
        highlightKey: 'usp.carefree.highlight'
    }
];
</script>
<template>
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
            <div class="text-center">
                <SectionHeader>{{ $t('usp.section_header') }}</SectionHeader>
            </div>
            <p class="text-accent-text text-lg mt-2 laptop:text-xl max-w-2xl mx-auto leading-relaxed">
                {{ $t('usp.intro') }}
            </p>
        </div>

        <!-- USP Cards met staggered layout -->

        <div class="grid grid-cols-1 tablet:grid-cols-2 laptop:grid-cols-3 gap-8">
            <Card v-for="(usp, index) in usps" :key="index" class="p-8 relative overflow-hidden">
                <div class="relative mb-6">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center border border-brand-primary/20">
                        <component :is="usp.icon"
                            class="h-8 w-8 text-brand-primary group-hover:text-accent-primary transition-colors duration-300" />
                    </div>
                    <div
                        class="absolute -bottom-1 -right-1 w-4 h-4 bg-accent-sage rounded-full border-2 border-brand-secondary">
                    </div>
                </div>
                <h3
                    class="text-xl font-bold text-brand-primary mb-3 group-hover:text-accent-primary transition-colors duration-300 relative">
                    {{ $t(usp.titleKey) }}
                </h3>

                <p class="text-accent-text leading-relaxed min-h-[130px] mb-6 relative">
                    {{ $t(usp.descriptionKey) }}
                </p>
                <div class="flex items-center gap-2.5 pt-4 border-t border-brand-primary/20 relative">
                    <div
                        class="w-1.5 h-1.5 rounded-full bg-accent-primary group-hover:scale-125 transition-transform duration-300">
                    </div>
                    <span
                        class="text-sm text-brand-light font-medium group-hover:text-accent-primary transition-colors duration-300">
                        {{ $t(usp.highlightKey) }}
                    </span>
                </div>
            </Card>
        </div>

        <!-- Bottom CTA -->
        <!-- <div class="text-center mt-12">
            <div
                class="inline-flex items-center gap-3 bg-white rounded-full px-6 py-3 shadow-sm border border-accent-sage/20">
                <span class="text-brand-primary font-medium">Klaar voor jouw volgende avontuur?</span>
                <div class="w-px h-4 bg-accent-sage/30"></div>
                <DefaultLink>
                    Ontdek alle reizen →
                </DefaultLink>
            </div>
        </div> -->
    </div>
</template>
