<script setup>
import { computed } from 'vue';

const companyName = window.appName;

</script>
<template>
    <Layout>
        <section class="section">
            <article>
                <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                        <!-- Header -->
                        <header class="bg-brand-primary text-white px-6 py-8">
                            <h1 class="text-3xl font-bold mb-2">Garantieregeling</h1>
                        </header>

                        <!-- Content -->
                        <div class="px-6 py-8">
                            <!-- Introduction -->
                            <div class="mb-8">
                                <h2
                                    class="text-2xl font-bold text-brand-text mb-4 border-b-2 border-brand-primary/20 pb-2">
                                    Garantie geregeld: STO Garant
                                </h2>
                                <div class="grid gap-4 laptop:gap-6">

                                <p class="text-brand-text/80 leading-relaxed">
                                    Om te voldoen aan de wettelijk verplichte garantiestelling maakt <strong>{{ companyName }}</strong>
                                    gebruik
                                    van STO Garant, een door de Autoriteit Consument en Markt (ACM) erkende
                                    garantieregeling. U kunt dit controleren via de STO Garant deelnemerspagina
                                    <a class="text-blue-500 hover:text-blue-800 visited:text-pink-900 underline" target="_blank" rel="noopener noreferrer"
                                        aria-label="STO Garant - Welke organisaties zijn aangesloten bij STO Garant?"
                                        href="https://www.sto-garant.nl/deelnemers">(www.sto-garant.nl/deelnemers)</a>.
                                        Alle informatie over STO Garant vindt u op <a class="text-blue-500 hover:text-blue-800 visited:text-pink-900 underline" target="_blank" rel="noopener noreferrer"
                                        aria-label="STO Garant"
                                        href="https://www.sto-garant.nl">www.sto-garant.nl</a>.
                                </p>
                                <p class="text-brand-text/80 leading-relaxed">
                                    Bij elk (reis)aanbod van <strong>{{ companyName }}</strong> op deze website leest u of de garantie
                                    van STO Garant van toepassing is. In de garantieregeling leest u wat de garantie
                                    inhoudt en
                                    welke voorwaarden van toepassing zijn. U vindt deze garantieregeling op de website
                                    van STO
                                    Garant <a class="text-blue-500 hover:text-blue-800 visited:text-pink-900 underline" target="_blank" rel="noopener noreferrer"
                                        aria-label="STO Garant - STO Garant Garantieregeling voor pakketreizen"
                                        href="https://www.sto-garant.nl/downloads">(www.sto-garant.nl/downloads)</a>
                                </p>
                                </div>
                            </div>

                            <!-- Section 1 -->
                            <section class="mb-8">
                                <h2
                                    class="text-2xl font-bold text-brand-text mb-4 border-b-2 border-brand-primary/20 pb-2">
                                    De werking
                                </h2>
                                <p class="text-brand-text/80 leading-relaxed mb-4">
                                    Indien de garantie van STO Garant van toepassing is op uw boeking, dan betaalt u de
                                    reissom niet
                                    aan <strong>{{ companyName }}</strong>, maar aan de derdengeldenrekening van Stichting Derdengelden
                                    Certo
                                    Escrow, een bij De Nederlandsche Bank (DNB) en de Autoriteit Financiële Markten
                                    (AFM)
                                    geregistreerde betaaldienstverlener. Deze stichting derdengelden waarborgt uw
                                    reissom tot na
                                    afloop van uw boeking. De dag na afloop wordt de reissom vrijgegeven aan <strong>{{ companyName }}</strong>.
                                    Mocht <strong>{{ companyName }}</strong> voor het einde van uw boeking in financieel onvermogen
                                    geraken,
                                    dan voert STO Garant de garantie uit. In de garantieregeling leest u hoe u daar in
                                    dat geval
                                    aanspraak op maakt.
                                </p>

                            </section>

                        </div>
                    </div>
                </div>
            </article>
        </section>
    </Layout>
</template>
