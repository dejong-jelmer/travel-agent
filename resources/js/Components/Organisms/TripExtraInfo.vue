<script setup>
import { Euro, FileText, CreditCard, Wallet, PhoneCall, ShieldCheck } from 'lucide-vue-next';
</script>

<template>
    <div class="space-y-6">
        <!-- Betalingsgarantie -->
        <div class="bg-brand-subtle/10 rounded-lg border border-brand-subtle/30 p-4 tablet:p-6">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 mt-1">
                    <ShieldCheck class="w-5 h-5 text-brand-subtle" />
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base tablet:text-lg font-semibold text-brand-primary mb-2">
                        Betalingsgarantie via STO Garant
                    </h3>
                    <p class="text-sm tablet:text-base text-brand-text leading-relaxed">
                        Dit aanbod valt onder de garantie van STO Garant. U kunt de voorwaarden van deze
                        garantieregeling vinden op de website van STO Garant <a class="text-brand-link underline"
                            target="_blank" rel="noopener noreferrer"
                            aria-label="STO Garant - STO Garant Garantieregeling voor pakketreizen"
                            href="https://www.sto-garant.nl/downloads">(www.sto-garant.nl/downloads)</a>
                    </p>
                </div>
            </div>
        </div>
        <!-- Prijs & Offerte -->
        <div
            class="bg-white rounded-lg border border-brand-primary/20 px-4 tablet:px-6 py-6 tablet:py-12 space-y-6 tablet:space-y-12">
            <div class="flex items-start gap-3 mb-3">
                <div class="flex-shrink-0 mt-1">
                    <FileText class="w-5 h-5 text-brand-accent" />
                </div>
                <div>
                    <h3 class="text-base tablet:text-lg font-semibold text-brand-primary mb-2">
                        {{ $t('trip_show.extra_trip_info.title') }}
                    </h3>
                    <div class="space-y-5 text-sm tablet:text-base text-brand-text leading-relaxed">
                        <div v-for="(step, index) in 4" :key="index" class="flex items-baseline gap-2">
                            <span class="text-sm font-light text-brand-primary flex-shrink-0 tabular-nums">
                                {{ index + 1 }}
                            </span>
                            <p>{{ $t(`trip_show.extra_trip_info.steps-${index + 1}`) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border-t border-brand-primary/10 text-sm text-brand-text/80" role="presentation"></div>
            <div class="flex items-start gap-3 mb-3">
                <div class="flex-shrink-0 mt-1">
                    <Euro class="w-5 h-5 text-brand-accent" />
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base tablet:text-lg font-semibold text-brand-primary mb-2">
                        Prijzen
                    </h3>
                    <p class="text-sm tablet:text-base text-brand-text leading-relaxed">
                        De getoonde prijs is een vanaf prijs per persoon, op basis van twee personen. Inclusief btw,
                        bijdrage garantiefonds en boekingskosten. Wat je uiteindelijk betaalt hangt af van wanneer je
                        gaat en met hoeveel personen je reist.
                        <br>
                        <br>
                        Treinkaartjes worden meestal duurder naarmate de vertrekdatum nadert. Vroeg boeken geeft je de
                        meeste keuze in vertrektijden, en vaak de beste prijs.
                    </p>
                </div>
            </div>

        </div>


    </div>
</template>
