<script setup>
import vvkrLogo from '@/../images/vvkr.png'

</script>
<template>
    <Layout :showContact="false">
        <section class="section">
            <article>
                <div class="max-w-4xl mx-auto py-12 tablet:py-24 px-4 sm:px-6 lg:px-8">
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                        <!-- Header -->
                        <header class="bg-brand-primary text-white px-6 py-8">
                            <h1 class="text-3xl font-bold mb-2">Over VvKR</h1>
                        </header>
                        <!-- Content -->
                        <div class="px-6 py-8">
                            <!-- Introduction -->
                            <div class="mb-8">
                                <h2
                                    class="text-2xl font-bold text-brand-text mb-4 border-b-2 border-brand-primary/20 pb-2 inline-flex gap-2">
                                    <a href="https://www.vvkr.nl/" target="_blank" rel="noopener noreferrer"
                                        aria-label="VvKR - Vereniging van Kleinschalige Reisorganisaties"
                                        class="block flex-none transition hover:scale-110 ease-in-out duration-300">
                                        <img :src="vvkrLogo" alt="VvKR - Vereniging van Kleinschalige Reisorganisaties"
                                            class="h-7" aria-hidden="true">
                                    </a>
                                    De VvKR - Vereniging van Kleinschalige Reisorganisaties
                                </h2>
                                <div class="grid gap-4 laptop:gap-6">

                                    <p class="text-brand-text/80 leading-relaxed">
                                        Ik ben aangesloten bij de Vereniging van Kleinschalige Reisorganisaties
                                        (VvKR). VvKR komt op voor
                                        de belangen van kleine, vaak specialistische reisorganisaties. Via VvKR bundelen
                                        we onze krachten
                                        en delen we onze expertise om zowel voor de reisorganisatie als de consument tot
                                        een zo groot mogelijke zekerheid en tevredenheid te komen.
                                    </p>
                                </div>
                            </div>

                            <!-- Section 1 -->
                            <section class="mb-8">
                                <h2
                                    class="text-2xl font-bold text-brand-text mb-4 border-b-2 border-brand-primary/20 pb-2">
                                    Missie & visie van VvKR
                                </h2>
                                <h3 class="font-bold">Missie</h3>
                                <p class="text-brand-text/80 leading-relaxed mb-4">
                                    De vereniging biedt een platform aan kleinschalige gespecialiseerde reisorganisaties
                                    en behartigt de
                                    belangen van deze organisaties. De vereniging staat ervoor in dat haar leden voldoen
                                    aan de
                                    wettelijk gestelde eisen voor reisorganisaties.
                                </p>
                                <h3 class="font-bold">Visie</h3>
                                <p class="text-brand-text/80 leading-relaxed mb-4">
                                    De vereniging wil een kennis- en inspiratieplatform zijn voor haar leden en wil
                                    verder
                                    professionaliseren. De vereniging zal de wettelijke zekerheden die haar
                                    professionele leden bieden
                                    onder de aandacht brengen bij de consument.
                                </p>

                            </section>
                            <!-- Section 2 -->
                            <section class="mb-8">
                                <h2
                                    class="text-2xl font-bold text-brand-text mb-4 border-b-2 border-brand-primary/20 pb-2">
                                    Duurzaamheid
                                </h2>
                                <p class="text-brand-text/80 leading-relaxed mb-4">
                                    De VvKR bevordeerd duurzaam ondernemen onder haar leden.
                                    Zo heeft de VvKR een samenwerkingsovereenkomst met Trees for
                                    All, een Handboek Duurzaamheid voor kleinschalige reisorganisaties en zijn alle
                                    leden verplicht het Kompas Duurzaam Reizen te onderschrijven.
                                    Het handboek en het Kompas Duurzaam Reizen vind je hier:
                                </p>
                                <ul class="list-disc list-inside">
                                    <li>
                                        <a href="https://www.vvkr.nl/handboek-duurzaamheid"
                                            class="text-brand-link underline" target="_blank" rel="noopener noreferrer"
                                            aria-label="VvKR - Vereniging van Kleinschalige Reisorganisaties - Handboek duurzaamheid">Handboek
                                            duurzaamheid</a>
                                    </li>
                                    <li>
                                        <a href="https://www.vvkr.nl/kompas-duurzaam-reizen-vvkr"
                                            class="text-brand-link underline" target="_blank" rel="noopener noreferrer"
                                            aria-label="VvKR - Vereniging van Kleinschalige Reisorganisaties - Kompas Duurzaam Reizen">Kompas
                                            Duurzaam Reizen</a>
                                    </li>
                                </ul>
                            </section>

                        </div>
                    </div>
                </div>
            </article>
        </section>
    </Layout>


</template>
