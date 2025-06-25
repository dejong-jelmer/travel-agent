<script setup>
import Layout from "@/Pages/Layouts/Layout.vue";

const props = defineProps({
    contact: Object
});
console.log(props.contact);

</script>
<template>
    <Layout>
        <section class="section">
            <article>
                <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                        <!-- Header -->
                        <header class="bg-secondary-stone text-white px-6 py-8">
                            <h1 class="text-3xl font-bold mb-2">Privacyverklaring</h1>
                            <p class="text-sm text-gray-600 mb-8">Versie: 1.1 | Laatste update: juni 2025</p>
                        </header>

                        <!-- Content -->
                        <div class="px-6 py-8">
                            <!-- Introduction -->
                            <div class="mb-8">
                                <p class="text-gray-700 leading-relaxed mb-4">
                                    Om uw privacy te waarborgen en zorgvuldig met de door u verstrekte persoonsgegevens
                                    om te gaan, zetten wij een extra stap. Zo bent u er zeker van dat uw
                                    persoonsgegevens bij ons in goede handen zijn.
                                </p>
                                <p class="text-gray-700 leading-relaxed mb-4">
                                    Zo maken we voor gegevensverwerking en -opslag uitsluitend gebruik van Europese
                                    diensten die voldoen aan de Europese privacy wetgeving en wordt onze website gehost
                                    door een duurzame aanbieder uit Nederland (Greenhost).
                                </p>
                                <p class="text-gray-700 leading-relaxed">
                                    In deze privacyverklaring leggen we uit welke persoonsgegevens wij verzamelen,
                                    waarom we dat doen, hoe we daarmee omgaan en wat jouw rechten zijn. Wij handelen in
                                    overeenstemming met de Algemene Verordening Gegevensbescherming (AVG).
                                </p>
                            </div>

                            <!-- Section 1 -->
                            <section class="mb-8">
                                <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">1. Wie
                                    zijn wij?</h2>
                                <p class="text-gray-700 leading-relaxed mb-4">
                                    Deze privacyverklaring is van toepassing op de reisorganisatie Omdat we reizen,
                                    gevestigd op {{ contact.fullAddress }}, ingeschreven bij de Kamer van Koophandel
                                    onder nummer {{ contact.kvk }}.
                                </p>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-gray-700 font-medium mb-2">Voor vragen over privacy kun je contact
                                        opnemen via:</p>
                                    <p class="">E-mail: <a
                                            class="email-field text-blue-600 underline hover:text-blue-800"
                                            href="#" v-html="contact.mail.display"></a></p>
                                </div>
                            </section>

                            <!-- Section 2 -->
                            <section class="mb-8">
                                <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">2.
                                    Welke gegevens verzamelen wij en waarom?</h2>

                                <!-- Subsection A -->
                                <div class="mb-6">
                                    <h3 class="text-xl font-semibold text-gray-800 mb-3">A. Contactformulier</h3>
                                    <p class="text-gray-700 mb-3">Wanneer je ons contactformulier gebruikt, verzamelen
                                        wij:</p>
                                    <ul class="list-disc pl-6 mb-4 text-gray-700 space-y-1">
                                        <li>Naam</li>
                                        <li>E-mailadres</li>
                                        <li>Telefoonnummer (optioneel)</li>
                                        <li>Berichtinhoud</li>
                                    </ul>
                                    <div class="bg-blue-50 p-3 rounded">
                                        <p class="text-sm text-gray-700"><strong>Doel:</strong> om je vraag of opmerking
                                            te kunnen beantwoorden.</p>
                                        <p class="text-sm text-gray-700"><strong>Grondslag:</strong> gerechtvaardigd
                                            belang (klantcontact).</p>
                                    </div>
                                </div>

                                <!-- Subsection B -->
                                <div class="mb-6">
                                    <h3 class="text-xl font-semibold text-gray-800 mb-3">B. Aanmelding voor nieuwsbrief
                                    </h3>
                                    <p class="text-gray-700 mb-3">Bij aanmelding voor onze nieuwsbrief vragen we:</p>
                                    <ul class="list-disc pl-6 mb-4 text-gray-700">
                                        <li>E-mailadres</li>
                                    </ul>
                                    <div class="bg-blue-50 p-3 rounded">
                                        <p class="text-sm text-gray-700"><strong>Doel:</strong> om je periodiek
                                            informatie en aanbiedingen te sturen.</p>
                                        <p class="text-sm text-gray-700"><strong>Grondslag:</strong> toestemming. Je
                                            kunt je altijd uitschrijven via de link onderaan elke e-mail.</p>
                                    </div>
                                </div>

                                <!-- Subsection C -->
                                <div class="mb-6">
                                    <h3 class="text-xl font-semibold text-gray-800 mb-3">C. Boeken van een reis</h3>
                                    <p class="text-gray-700 mb-3">Wanneer je een reis boekt, verwerken wij:</p>
                                    <ul class="list-disc pl-6 mb-4 text-gray-700 space-y-1">
                                        <li>Volledige naam</li>
                                        <li>Geboortedatum</li>
                                        <li>Adresgegevens</li>
                                        <li>Telefoonnummer</li>
                                        <li>E-mailadres</li>
                                        <li>Paspoort- of ID-informatie (indien vereist voor reis)</li>
                                        <li>Eventuele dieetwensen of medische bijzonderheden (optioneel)</li>
                                    </ul>
                                    <div class="bg-blue-50 p-3 rounded">
                                        <p class="text-sm text-gray-700"><strong>Doel:</strong> om jouw reis correct te
                                            kunnen organiseren en uitvoeren.</p>
                                        <p class="text-sm text-gray-700"><strong>Grondslag:</strong> uitvoering van de
                                            overeenkomst.</p>
                                    </div>
                                </div>
                            </section>

                            <!-- Section 3 -->
                            <section class="mb-8">
                                <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">3. Met
                                    wie delen wij gegevens?</h2>
                                <p class="text-gray-700 leading-relaxed mb-4">
                                    Wij delen persoonsgegevens alleen wanneer dat noodzakelijk is voor de uitvoering van
                                    onze diensten. Dit gebeurt met:
                                </p>
                                <ul class="list-disc pl-6 text-gray-700 space-y-2">
                                    <li>Reisdienstverleners zoals vervoerders, hotels of lokale gidsen</li>
                                    <li>Garantie- of waarborgfondsen STO Garant en het Calamiteitenfonds</li>
                                    <li>IT-dienstverleners die onze website, e-mailsystemen of boekingsplatformen
                                        ondersteunen</li>
                                </ul>
                            </section>

                            <!-- Section 4 -->
                            <section class="mb-8">
                                <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">4. Hoe
                                    lang bewaren wij jouw gegevens?</h2>
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="font-medium text-gray-800">Contactformulier:</p>
                                        <p class="text-gray-700">max. 1 jaar na afhandeling</p>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="font-medium text-gray-800">Nieuwsbriefgegevens:</p>
                                        <p class="text-gray-700">tot uitschrijving</p>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="font-medium text-gray-800">Boekingsgegevens:</p>
                                        <p class="text-gray-700">7 jaar (wettelijke bewaarplicht voor administratie)</p>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="font-medium text-gray-800">Medische/dieetgegevens:</p>
                                        <p class="text-gray-700">direct na afloop van de reis verwijderd</p>
                                    </div>
                                </div>
                            </section>

                            <!-- Section 5 -->
                            <section class="mb-8">
                                <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">5.
                                    Jouw rechten</h2>
                                <p class="text-gray-700 mb-4">Je hebt het recht om:</p>
                                <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
                                    <li>Inzage te krijgen in jouw gegevens</li>
                                    <li>Gegevens te laten corrigeren of verwijderen</li>
                                    <li>Bezwaar te maken tegen verwerking</li>
                                    <li>Verwerking te beperken</li>
                                    <li>Gegevens over te dragen (dataportabiliteit)</li>
                                    <li>Toestemming in te trekken (voor nieuwsbrief)</li>
                                </ul>
                                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                                    <p class="text-gray-700">
                                        Je kunt deze rechten uitoefenen door contact op te nemen via
                                        <a
                                            class="email-field text-blue-600 underline hover:text-blue-800"
                                            href="#" v-html="contact.mail.display"></a>.
                                        Wij reageren binnen 30 dagen.
                                    </p>
                                </div>
                            </section>

                            <!-- Section 6 -->
                            <section class="mb-8">
                                <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">6.
                                    Beveiliging van jouw gegevens</h2>
                                <p class="text-gray-700 leading-relaxed mb-4">
                                    Wij nemen passende technische en organisatorische maatregelen om je persoonsgegevens
                                    te beschermen tegen verlies of ongeoorloofde toegang. Voorbeelden:
                                </p>
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2 flex-shrink-0"></div>
                                        <div>
                                            <p class="font-medium text-gray-800">Versleutelde verbinding (SSL)</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2 flex-shrink-0"></div>
                                        <div>
                                            <p class="font-medium text-gray-800">Twee-factor-authenticatie (2FA)</p>
                                            <p class="text-sm text-gray-600">voor extra beveiliging van accounts en
                                                systemen</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2 flex-shrink-0"></div>
                                        <div>
                                            <p class="font-medium text-gray-800">Beveiligde opslag</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2 flex-shrink-0"></div>
                                        <div>
                                            <p class="font-medium text-gray-800">Beperkte toegang tot gegevens</p>
                                            <p class="text-sm text-gray-600">alleen bevoegde medewerkers hebben toegang
                                                tot persoonsgegevens</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3 md:col-span-2">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2 flex-shrink-0"></div>
                                        <div>
                                            <p class="font-medium text-gray-800">Betrouwbare samenwerkingen</p>
                                            <p class="text-sm text-gray-600">wij werken uitsluitend met externe partijen
                                                die aantoonbaar voldoen aan de eisen van de AVG</p>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <!-- Section 7 -->
                            <section class="mb-8">
                                <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">7.
                                    Cookies</h2>
                                <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg">
                                    <ul class="list-disc text-gray-700 pl-4 leading-relaxed mb-2">
                                        <li>
                                            Onze website gebruikt alleen functionele cookies, die nodig zijn om de site goed
                                            te laten werken.
                                        </li>
                                        <li>
                                            Wij gebruiken <strong>geen</strong> analytische cookies en/of tracking cookies van derden.
                                        </li>
                                    </ul>
                                </div>
                            </section>

                            <!-- Section 8 -->
                            <section class="mb-8">
                                <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">8.
                                    Klachten?</h2>
                                <p class="text-gray-700 leading-relaxed">
                                    Als je vindt dat wij niet correct omgaan met jouw gegevens, kun je een klacht
                                    indienen bij de Autoriteit Persoonsgegevens via
                                    <a href="https://autoriteitpersoonsgegevens.nl" target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-blue-600 underline hover:text-blue-800">autoriteitpersoonsgegevens.nl</a>.
                                </p>
                            </section>

                            <!-- Section 9 -->
                            <section class="mb-8">
                                <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">9.
                                    Wijzigingen</h2>
                                <p class="text-gray-700 leading-relaxed">
                                    Wij kunnen deze privacyverklaring van tijd tot tijd aanpassen. Wij adviseren je om
                                    deze verklaring regelmatig te raadplegen. Bij ingrijpende wijzigingen zullen wij dit
                                    duidelijk communiceren.
                                </p>
                            </section>

                            <!-- Footer -->
                            <footer class="border-t border-gray-200 pt-6 mt-8">
                                <p class="text-sm text-gray-500 text-center">
                                    <strong>Laatste update:</strong> juni 2025
                                </p>
                            </footer>
                        </div>
                    </div>
                </div>
            </article>
        </section>
    </Layout>
</template>
