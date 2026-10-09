<script setup>
import { Phone, ShieldCheck } from '@lucide/vue';
import portrait from '@/../images/me_thumb.webp';

defineEmits(['request']);
</script>

<template>
    <BaseCard aria-labelledby="trip-plan-heading">
        <!-- Phones: photo next to the heading, content below it. From tablet up: photo in its own column. -->
        <div
            class="grid grid-cols-[88px_minmax(0,1fr)] tablet:grid-cols-[120px_minmax(0,1fr)] items-center tablet:items-start gap-x-4 gap-y-6 tablet:gap-x-7 tablet:gap-y-3">
            <img :src="portrait" :alt="$t('about.founder.name')" width="120" height="120" loading="lazy"
                class="w-[88px] h-[88px] tablet:w-[120px] tablet:h-[120px] rounded-full object-cover tablet:row-span-2" />

            <h2 id="trip-plan-heading" class="text-[26px] font-semibold text-brand-primary">
                {{ $t('trip_show.inquiry.title') }}
            </h2>

            <div class="col-span-2 tablet:col-span-1 tablet:col-start-2 space-y-8">
                <p class="text-base text-brand-text leading-relaxed">
                    {{ $t('trip_show.inquiry.explanation') }}
                </p>

                <!-- How a request works -->
                <div>
                    <h3 class="text-lg font-semibold text-brand-primary mb-4">
                        {{ $t('trip_show.extra_trip_info.title') }}
                    </h3>
                    <ol class="space-y-3">
                        <li v-for="step in 4" :key="step" class="flex items-start gap-3">
                            <span
                                class="flex-shrink-0 flex items-center justify-center w-7 h-7 rounded-full bg-brand-primary/10 text-sm font-semibold text-brand-primary">
                                {{ step }}
                            </span>
                            <!-- leading-7 matches the 28px badge, so it lines up with the first line -->
                            <span class="text-base leading-7 text-brand-text">
                                {{ $t(`trip_show.extra_trip_info.steps-${step}`) }}
                            </span>
                        </li>
                    </ol>
                </div>

                <!-- Prices -->
                <div>
                    <h3 class="text-lg font-semibold text-brand-primary mb-2">
                        Prijzen
                    </h3>
                    <div class="space-y-4 text-base text-brand-text leading-relaxed">
                        <p>
                            De getoonde prijs is een vanaf prijs per persoon, op basis van twee personen. Inclusief btw,
                            bijdrage garantiefonds en boekingskosten. Wat je uiteindelijk betaalt hangt af van wanneer
                            je gaat en met hoeveel personen je reist.
                        </p>
                        <p>
                            Treinkaartjes worden meestal duurder naarmate de vertrekdatum nadert. Vroeg boeken geeft je
                            de meeste keuze in vertrektijden, en vaak de beste prijs.
                        </p>
                    </div>
                </div>

                <!-- Request button with the phone number next to it -->
                <div class="flex flex-wrap items-center gap-x-6 gap-y-3">
                    <Button @click="$emit('request')" class="flex justify-center items-center">
                        {{ $t('trip_show.inquiry.cta_make_request') }}
                    </Button>
                    <span class="flex items-center gap-2 text-brand-primary">
                        <Phone class="w-4 h-4 flex-shrink-0" />
                        <!-- Filled with the decoded phone number by Layout (anti-spam) -->
                        <a href="#" class="tel-field font-semibold text-brand-primary"></a>
                    </span>
                </div>

                <!-- Payment guarantee -->
                <div class="bg-brand-subtle/10 rounded-lg border border-brand-subtle/30 p-4 tablet:p-6">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 mt-1">
                            <ShieldCheck class="w-5 h-5 text-brand-primary" />
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
            </div>
        </div>
    </BaseCard>
</template>
