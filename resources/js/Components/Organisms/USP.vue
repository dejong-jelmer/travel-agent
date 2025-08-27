<script setup>
import Train from '@/Icons/Train.vue';
import Heart from '@/Icons/Heart.vue';
import Shield from '@/Icons/Shield.vue';



// import * as exportedIcons from '@/Icons';
// Object.entries(exportedIcons).forEach(([name, exported]) => window[name] = exported);

const usps = [
    {
        icon: Train,
        title: '100% treinreizen',
        description: 'Reis comfortabel en ontspannen door Europa\'s mooiste landschappen. Geen stress van vliegvelden, wel het plezier van de reis zelf.',
        highlight: 'Gemiddeld 75% minder CO₂',
        accentColor: 'bg-secondary-sage'
    },
    {
        icon: Heart,
        title: 'Lokale beleving',
        description: 'Ontmoet locals, proef authentieke gerechten en ontdek verborgen pareltjes die alleen insiders kennen.',
        highlight: 'Partnerships met 200+ lokale gidsen',
        accentColor: 'bg-accent-terracotta'
    },
    {
        icon: Shield,
        title: 'Zorgeloos reizen',
        description: 'Wij regelen alles van A tot Z. Jij hoeft alleen maar te genieten van je duurzame avontuur door Europa.',
        highlight: '24/7 Nederlandse reisondersteuning',
        accentColor: 'bg-accent-gold'
    }
];
</script>
<template>
    <div class="max-w-6xl mx-auto">
        <!-- Header met subtiele accent lijn -->

        <div class="text-center mb-12">
            <SectionHeader>
                Waarom kiezen voor ons
            </SectionHeader>
            <p class="text-primary-default text-lg laptop:text-xl max-w-2xl mx-auto leading-relaxed">
                Onze unieke aanpak combineert comfort, cultuur en duurzaamheid voor een reiservaring die verder gaat dan
                toerisme.
            </p>
        </div>

        <!-- USP Cards met staggered layout -->
        <div class="grid grid-cols-1 tablet:grid-cols-2 laptop:grid-cols-3 gap-8">
            <div v-for="(usp, index) in usps" :key="usp.title"
                class="group relative bg-white rounded-2xl shadow-sm border border-neutral-200 p-8 hover:shadow-xl hover:-translate-y-1 transition-all duration-500 ease-out">
                <!-- Accent dot in corner -->
                <div class="absolute top-6 right-6 w-3 h-3 rounded-full" :class="usp.accentColor"></div>

                <!-- Icon met gradient background -->
                <div class="relative mb-6">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300"
                        :class="usp.accentColor">
                        <component :is="usp.icon" class="h-8 w-8 text-white" />
                    </div>
                </div>

                <!-- Content -->
                <h3
                    class="text-xl font-bold text-primary-dark mb-3 group-hover:text-accent-terracotta transition-colors duration-300">
                    {{ usp.title }}
                </h3>
                <p class="text-primary-default leading-relaxed mb-4">
                    {{ usp.description }}
                </p>

                <!-- Feature highlight -->
                <div class="flex items-center gap-2 text-sm">
                    <div class="w-2 h-2 rounded-full bg-secondary-sage"></div>
                    <span class="text-secondary-stone font-medium">{{ usp.highlight }}</span>
                </div>
            </div>
        </div>

        <!-- Bottom CTA -->
        <div class="text-center mt-12">
            <div
                class="inline-flex items-center gap-3 bg-white rounded-full px-6 py-3 shadow-sm border border-secondary-sage/20">
                <span class="text-primary-dark font-medium">Klaar voor jouw volgende avontuur?</span>
                <div class="w-px h-4 bg-secondary-sage/30"></div>
                <DefaultLink>
                    Ontdek alle reizen →
                </DefaultLink>
            </div>
        </div>
    </div>
</template>
