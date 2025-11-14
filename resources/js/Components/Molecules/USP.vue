<script setup>
import { Leaf, Castle, Handshake } from 'lucide-vue-next';


// import * as exportedIcons from '@/Icons';
// Object.entries(exportedIcons).forEach(([name, exported]) => window[name] = exported);

const usps = [
    {
        icon: Leaf,
        title: 'Duurzaam',
        description: 'Reis comfortabel en ontspannen door Europa\'s mooiste landschappen. Geen stress van vliegvelden, wel het plezier van de reis zelf.',
        highlight: 'Gemiddeld 75% minder CO₂'
    },
    {
        icon: Castle,
        title: 'Met een verhaal',
        description: 'Ontdek de fascinerende verhalen achter elke bestemming. Onze reizen brengen geschiedenis tot leven met verhalen en bijzondere achtergronden bij elke bestemming.',
        highlight: 'Unieke achtergronden bij elke reis'
    },
    {
        icon: Handshake,
        title: 'Zorgeloos',
        description: 'Wij regelen alles van A tot Z. Jij hoeft alleen maar te genieten van je duurzame avontuur door Europa.',
        highlight: '24/7 Nederlandse reisondersteuning'
    }
];
</script>
<template>
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
            <div class="text-center">
                <SectionHeader>kiezen voor ons</SectionHeader>
            </div>
            <p class="text-brand-primary text-lg mt-2 laptop:text-xl max-w-2xl mx-auto leading-relaxed">
                Onze unieke aanpak combineert comfort, cultuur en duurzaamheid voor een reiservaring die verder gaat dan
                toerisme.
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
                        class="absolute -bottom-1 -right-1 w-4 h-4 bg-accent-primary rounded-full border-2 border-brand-secondary">
                    </div>
                </div>
                <h3
                    class="text-xl font-bold text-brand-primary mb-3 group-hover:text-accent-primary transition-colors duration-300 relative">
                    {{ usp.title }}
                </h3>

                <p class="text-brand-primary leading-relaxed min-h-[130px] mb-6 relative">
                    {{ usp.description }}
                </p>
                <div class="flex items-center gap-2.5 pt-4 border-t border-brand-primary/20 relative">
                    <div
                        class="w-1.5 h-1.5 rounded-full bg-accent-primary group-hover:scale-125 transition-transform duration-300">
                    </div>
                    <span
                        class="text-sm text-[#2f4858]/60 font-medium group-hover:text-accent-primary transition-colors duration-300">
                        {{ usp.highlight }}
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
