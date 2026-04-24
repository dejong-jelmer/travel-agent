<script setup>
import { Euro, FileText, CreditCard, Wallet, PhoneCall, ShieldCheck } from 'lucide-vue-next';
</script>

<template>
    <div class="space-y-6">
        <!-- Prijs & Offerte -->
        <div
            class="bg-white rounded-lg border border-brand-primary/20 px-4 tablet:px-6 py-6 tablet:py-12 space-y-6 tablet:space-y-12">
            <div class="flex items-start gap-3 mb-3">
                <div class="flex-shrink-0 mt-1">
                    <Euro class="w-5 h-5 text-brand-accent" />
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-base tablet:text-lg font-semibold text-brand-primary mb-2">
                        Prijzen & Offerte
                    </h4>
                    <p class="text-sm tablet:text-base text-brand-text leading-relaxed">
                        De getoonde prijs is een richtprijs per persoon. De definitieve prijs hangt af van wanneer je
                        gaat en met hoeveel mensen.
                        Voor populaire periodes zoals schoolvakanties is het verstandig om vroeg contact op te nemen —
                        dan hebben we meer ruimte om een passend voorstel te maken.
                    </p>
                </div>
            </div>
            <div class="border-t border-brand-primary/10 text-sm text-brand-text/80"></div>
            <div class="flex items-start gap-3 mb-3">
                <div class="flex-shrink-0 mt-1">
                    <FileText class="w-5 h-5 text-brand-accent" />
                </div>
                <div>
                    <h4 class="text-base tablet:text-lg font-semibold text-brand-primary mb-2">
                        {{ $t('trip_show.extra_trip_info.title') }}
                    </h4>
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
            <div class="border-t border-brand-primary/10 text-sm text-brand-text/80"></div>
            <div class="flex items-start gap-3 mb-3">
                <div class="flex-shrink-0 mt-1">
                    <PhoneCall class="w-5 h-5 text-brand-accent" />
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-base tablet:text-lg font-semibold text-brand-primary mb-2">
                        Hulp onderweg
                    </h4>
                    <p class="text-sm tablet:text-base text-brand-text leading-relaxed">
                        Bij je reisdocumenten ontvang je mijn 24/7 bereikbare noodnummer. Hiermee kun je
                        mij altijd bereiken bij problemen of vragen tijdens je reis, ook buiten kantooruren
                        en in het weekend.
                    </p>
                </div>
            </div>
        </div>

        <!-- Betalingsgarantie -->
        <div class="bg-brand-subtle/10 rounded-lg border border-brand-subtle/30 p-4 tablet:p-6">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 mt-1">
                    <ShieldCheck class="w-5 h-5 text-brand-subtle" />
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-base tablet:text-lg font-semibold text-brand-primary mb-2">
                        Betalingsgarantie via STO Garant
                    </h4>
                    <p class="text-sm tablet:text-base text-brand-text leading-relaxed">
                        Dit aanbod valt onder de garantie van STO Garant. U kunt de voorwaarden van deze
                        garantieregeling vinden op de website van STO Garant <a class="text-brand-link underline" target="_blank" rel="noopener noreferrer"
                                        aria-label="STO Garant - STO Garant Garantieregeling voor pakketreizen"
                                        href="https://www.sto-garant.nl/downloads">(www.sto-garant.nl/downloads)</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
