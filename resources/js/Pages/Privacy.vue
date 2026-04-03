<script setup>
import { computed } from 'vue';

const props = defineProps({
    contact: Object,
    newsletterRetentionMonths: {
        type: Number,
        default: 3,
    },
    bookingRetentionYears: {
        type: Number,
        default: 7,
    },
    specialRequestsRetentionDays: {
        type: Number,
        default: 7,
    },
    privacy: {
        type: Object,
        default: { version: null, updated: null }
    }
});

const retentionLabel = computed(() =>
    props.newsletterRetentionMonths === 1
        ? '1 maand'
        : `${props.newsletterRetentionMonths} maanden`
);

const specialRequestsRetentionLabel = computed(() =>
    props.specialRequestsRetentionDays === 1
        ? '1 dag'
        : `${props.specialRequestsRetentionDays} dagen`
);
</script>
<template>
    <Layout>
        <section class="section">
            <article>
                <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                        <!-- Header -->
                        <header class="bg-brand-primary text-white px-6 py-8">
                            <h1 class="text-3xl font-bold mb-2">Privacyverklaring</h1>
                            <p class="text-sm text-white/70">Versie: {{ props.privacy.version }} | Laatste update: {{
                                props.privacy.updated }}
                            </p>
                        </header>

                        <!-- Content -->
                        <div class="px-6 py-8">
                            <!-- Introduction -->
                            <div class="mb-8">
                                <p class="text-brand-text/80 leading-relaxed">
                                    Bij het boeken van een reis of inschrijven voor de nieuwsbrief verzamel ik
                                    persoonsgegevens. Met deze gegevens ga ik zorgvuldig om. Voor gegevensverwerking en
                                    -opslag maak ik uitsluitend gebruik van diensten die voldoen aan de AVG en waarmee
                                    ik een verwerkersovereenkomst (DPA) heb gesloten. In deze privacyverklaring lees je
                                    welke gegevens ik verzamel, waarom, hoe ik daarmee omga en wat jouw rechten zijn.
                                </p>
                            </div>

                            <!-- Section 1 -->
                            <section class="mb-8">
                                <h2
                                    class="text-2xl font-bold text-brand-text mb-4 border-b-2 border-brand-primary/20 pb-2">
                                    1. Wie ben ik?
                                </h2>
                                <p class="text-brand-text/80 leading-relaxed mb-4">
                                    Deze privacyverklaring is van toepassing op de reisorganisatie Omdat We Reizen,
                                    gevestigd op {{ contact.fullAddress }}, ingeschreven bij de Kamer van Koophandel
                                    onder nummer {{ contact.kvk }}.
                                </p>
                                <div class="bg-brand-secondary/50 p-4 rounded-lg">
                                    <p class="text-brand-text font-medium mb-2">Voor vragen over privacy kun je contact
                                        opnemen via:</p>
                                    <p>E-mail: <a class="text-brand-link underline hover:text-brand-accent email-field"
                                            href="#" v-html="contact.mail.display"></a></p>
                                </div>
                            </section>

                            <!-- Section 2 -->
                            <section class="mb-8">
                                <h2
                                    class="text-2xl font-bold text-brand-text mb-4 border-b-2 border-brand-primary/20 pb-2">
                                    2. Welke gegevens verzamel ik en waarom?
                                </h2>

                                <!-- Subsection A -->
                                <div class="mb-6">
                                    <h3 class="text-xl font-semibold text-brand-text mb-3">A. Contactformulier</h3>
                                    <p class="text-brand-text/80 mb-3">Wanneer je het contactformulier gebruikt,
                                        verzamel ik:</p>
                                    <ul class="list-disc pl-6 mb-4 text-brand-text/80 space-y-1">
                                        <li>Naam</li>
                                        <li>E-mailadres</li>
                                        <li>Telefoonnummer (optioneel)</li>
                                        <li>Berichtinhoud</li>
                                    </ul>
                                    <div class="bg-brand-secondary p-3 rounded">
                                        <p class="text-sm text-brand-text/80"><strong>Doel:</strong> om je vraag of
                                            opmerking te kunnen beantwoorden.</p>
                                        <p class="text-sm text-brand-text/80"><strong>Grondslag:</strong>
                                            gerechtvaardigd belang (klantcontact).</p>
                                    </div>
                                </div>

                                <!-- Subsection B -->
                                <div class="mb-6">
                                    <h3 class="text-xl font-semibold text-brand-text mb-3">B. Aanmelding voor
                                        nieuwsbrief</h3>
                                    <p class="text-brand-text/80 mb-3">Bij aanmelding voor de nieuwsbrief vraag ik:</p>
                                    <ul class="list-disc pl-6 mb-4 text-brand-text/80">
                                        <li>E-mailadres</li>
                                        <li>Naam (optioneel)</li>
                                    </ul>
                                    <div class="bg-brand-secondary p-3 rounded">
                                        <p class="text-sm text-brand-text/80"><strong>Doel:</strong> om je periodiek
                                            informatie te sturen.</p>
                                        <p class="text-sm text-brand-text/80"><strong>Grondslag:</strong> toestemming.
                                            Je kunt je altijd uitschrijven via de link onderaan elke e-mail.</p>
                                    </div>
                                </div>

                                <!-- Subsection C -->
                                <div class="mb-6">
                                    <h3 class="text-xl font-semibold text-brand-text mb-3">C. Boeken van een reis</h3>
                                    <p class="text-brand-text/80 mb-3">Wanneer je een reis boekt, verwerk ik:</p>
                                    <ul class="list-disc pl-6 mb-4 text-brand-text/80 space-y-1">
                                        <li>Volledige naam (voornamen en achternaam)</li>
                                        <li>Geboortedatum</li>
                                        <li>Adresgegevens (straat, huisnummer, woonplaats, postcode)</li>
                                        <li>Telefoonnummer</li>
                                        <li>E-mailadres</li>
                                        <li>Nationaliteit (om te beoordelen of aanvullende reisdocumenten of visa
                                            vereist zijn)</li>
                                        <li>Reisgegevens en reisschema met datum van vertrek</li>
                                        <li>Eventuele opmerkingen, dieetwensen of medische bijzonderheden (optioneel)
                                        </li>
                                    </ul>
                                    <div class="bg-brand-secondary p-3 rounded">
                                        <p class="text-sm text-brand-text/80"><strong>Doel:</strong> om jouw reis
                                            correct te kunnen organiseren en uitvoeren.</p>
                                        <p class="text-sm text-brand-text/80"><strong>Grondslag:</strong> uitvoering van
                                            de overeenkomst. Voor dieetwensen en medische bijzonderheden wordt bij het
                                            boekingsproces afzonderlijk om jouw uitdrukkelijke toestemming gevraagd (AVG
                                            art. 9 lid 2a).</p>
                                    </div>
                                </div>
                            </section>

                            <!-- Section 3 -->
                            <section class="mb-8">
                                <h2
                                    class="text-2xl font-bold text-brand-text mb-4 border-b-2 border-brand-primary/20 pb-2">
                                    3. Met wie deel ik je gegevens?
                                </h2>
                                <p class="text-brand-text/80 leading-relaxed mb-4">
                                    Ik deel je persoonsgegevens alleen wanneer dat noodzakelijk is voor de uitvoering
                                    van mijn diensten. Dit gebeurt met:
                                </p>
                                <ul class="list-disc pl-6 text-brand-text/80 space-y-2 mb-6">
                                    <li>
                                        <strong>Reisdienstverleners</strong> — zoals vervoerders en accommodaties, voor
                                        zover nodig om jouw reis uit te voeren.
                                    </li>
                                    <li>
                                        <strong>STO Garant</strong> — het garantiefonds waarmee ik wettelijk verplicht
                                        samenwerk.
                                    </li>
                                    <li>
                                        <strong>Hosting- en serverpartijen</strong> — voor het veilig opslaan en
                                        beschikbaar
                                        houden van de website en boekingsomgeving.
                                    </li>
                                    <li>
                                        <strong>E-maildienstverlener</strong> — voor het verzenden van
                                        boekingsbevestigingen
                                        en nieuwsbrieven.
                                    </li>
                                    <li>
                                        <strong>Domeinnaamregistrar</strong> — voor het beheer van mijn domeinnaam en
                                        e-maildomein.
                                    </li>
                                </ul>

                                <p class="text-brand-text/80 leading-relaxed mb-4">
                                    Met al deze partijen heb ik een verwerkersovereenkomst (DPA) gesloten. Hieronder
                                    vind
                                    je een volledig overzicht.
                                </p>

                                <!-- Verwerkersoverzicht -->
                                <div class="overflow-x-auto rounded-lg border border-brand-primary/20">
                                    <table class="min-w-full text-sm text-left text-brand-text">
                                        <thead class="bg-brand-primary text-white text-xs uppercase tracking-wider">
                                            <tr>
                                                <th class="px-4 py-3">Partij</th>
                                                <th class="px-4 py-3">Vestiging</th>
                                                <th class="px-4 py-3">Doel</th>
                                                <th class="px-4 py-3">Privacybeleid</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-brand-primary/10">
                                            <tr class="bg-brand-secondary/50 hover:bg-brand-secondary">
                                                <td class="px-4 py-3 font-medium">Hetzner Online GmbH</td>
                                                <td class="px-4 py-3">Duitsland</td>
                                                <td class="px-4 py-3">Hosting website en boekingsomgeving</td>
                                                <td class="px-4 py-3">
                                                    <a href="https://hetzner.com/legal/privacy-policy" target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="text-brand-link underline hover:text-brand-accent">
                                                        Bekijken
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr class="hover:bg-brand-secondary">
                                                <td class="px-4 py-3 font-medium">Laravel Forge</td>
                                                <td class="px-4 py-3">Verenigde Staten</td>
                                                <td class="px-4 py-3">Serverbeheer en -configuratie</td>
                                                <td class="px-4 py-3">
                                                    <a href="https://laravel.com/legal/privacy" target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="text-brand-link underline hover:text-brand-accent">
                                                        Bekijken
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr class="bg-brand-secondary/50 hover:bg-brand-secondary">
                                                <td class="px-4 py-3 font-medium">Mailjet (Sinch)</td>
                                                <td class="px-4 py-3">Frankrijk (EU)</td>
                                                <td class="px-4 py-3">Verzenden van e-mails en nieuwsbrieven</td>
                                                <td class="px-4 py-3">
                                                    <a href="https://www.mailjet.com/legal/privacy-policy/"
                                                        target="_blank" rel="noopener noreferrer"
                                                        class="text-brand-link underline hover:text-brand-accent">
                                                        Bekijken
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr class="hover:bg-brand-secondary">
                                                <td class="px-4 py-3 font-medium">TransIP B.V.</td>
                                                <td class="px-4 py-3">Nederland</td>
                                                <td class="px-4 py-3">Domeinnaam- en e-maildomeinbeheer</td>
                                                <td class="px-4 py-3">
                                                    <a href="https://www.transip.nl/legal-and-security/privacy-policy/"
                                                        target="_blank" rel="noopener noreferrer"
                                                        class="text-brand-link underline hover:text-brand-accent">
                                                        Bekijken
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </section>

                            <!-- Section 4 -->
                            <section class="mb-8">
                                <h2
                                    class="text-2xl font-bold text-brand-text mb-4 border-b-2 border-brand-primary/20 pb-2">
                                    4. Hoe lang bewaar ik jouw gegevens?
                                </h2>
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div class="bg-brand-secondary/50 p-4 rounded-lg">
                                        <p class="font-medium text-brand-text">Contactformulier:</p>
                                        <p class="text-brand-text/80">max. 1 jaar na afhandeling</p>
                                    </div>
                                    <div class="bg-brand-secondary/50 p-4 rounded-lg">
                                        <p class="font-medium text-brand-text">Nieuwsbriefgegevens:</p>
                                        <p class="text-brand-text/80">tot uitschrijving; na uitschrijving worden
                                            gegevens
                                            binnen {{ retentionLabel }} verwijderd</p>
                                    </div>
                                    <div class="bg-brand-secondary/50 p-4 rounded-lg">
                                        <p class="font-medium text-brand-text">Boekingsgegevens:</p>
                                        <p class="text-brand-text/80">{{ bookingRetentionYears }} jaar op basis van de
                                            wettelijke bewaarplicht
                                            voor administratie. Na deze termijn worden persoonsgegevens geanonimiseerd.
                                        </p>
                                    </div>
                                    <div class="bg-brand-secondary/50 p-4 rounded-lg">
                                        <p class="font-medium text-brand-text">Medische/dieetgegevens:</p>
                                        <p class="text-brand-text/80">binnen {{ specialRequestsRetentionLabel }} na
                                            afloop van de reis verwijderd</p>
                                    </div>
                                </div>
                            </section>

                            <!-- Section 5 -->
                            <section class="mb-8">
                                <h2
                                    class="text-2xl font-bold text-brand-text mb-4 border-b-2 border-brand-primary/20 pb-2">
                                    5. Jouw rechten
                                </h2>
                                <p class="text-brand-text/80 mb-4">Je hebt het recht om:</p>
                                <ul class="list-disc pl-6 text-brand-text/80 space-y-2 mb-4">
                                    <li>Inzage te krijgen in jouw gegevens</li>
                                    <li>Gegevens te laten corrigeren of verwijderen</li>
                                    <li>Bezwaar te maken tegen verwerking</li>
                                    <li>Verwerking te beperken</li>
                                    <li>Gegevens over te dragen (dataportabiliteit)</li>
                                    <li>Toestemming in te trekken (voor nieuwsbrief)</li>
                                </ul>
                                <div class="bg-brand-accent/10 border-l-4 border-brand-accent p-4 rounded-r-lg">
                                    <p class="text-brand-text/80">
                                        Je kunt deze rechten uitoefenen door contact op te nemen via
                                        <a class="text-brand-link underline hover:text-brand-accent email-field"
                                            href="#" v-html="contact.mail.display"></a>.
                                        Ik reageer binnen 30 dagen.
                                    </p>
                                </div>
                            </section>

                            <!-- Section 6 -->
                            <section class="mb-8">
                                <h2
                                    class="text-2xl font-bold text-brand-text mb-4 border-b-2 border-brand-primary/20 pb-2">
                                    6. Beveiliging van jouw gegevens
                                </h2>
                                <p class="text-brand-text/80 leading-relaxed mb-4">
                                    Ik neem passende technische en organisatorische maatregelen om je persoonsgegevens
                                    te beschermen tegen verlies of ongeoorloofde toegang. Voorbeelden:
                                </p>
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-2 h-2 bg-brand-subtle rounded-full mt-2 flex-shrink-0"></div>
                                        <div>
                                            <p class="font-medium text-brand-text">Versleutelde verbinding (SSL)</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3">
                                        <div class="w-2 h-2 bg-brand-subtle rounded-full mt-2 flex-shrink-0"></div>
                                        <div>
                                            <p class="font-medium text-brand-text">Twee-factor-authenticatie (2FA)</p>
                                            <p class="text-sm text-brand-text/60">voor extra beveiliging van accounts en
                                                systemen</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3">
                                        <div class="w-2 h-2 bg-brand-subtle rounded-full mt-2 flex-shrink-0"></div>
                                        <div>
                                            <p class="font-medium text-brand-text">Beveiligde opslag</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3">
                                        <div class="w-2 h-2 bg-brand-subtle rounded-full mt-2 flex-shrink-0"></div>
                                        <div>
                                            <p class="font-medium text-brand-text">Beperkte toegang tot gegevens</p>
                                            <p class="text-sm text-brand-text/60">alleen ik heb toegang tot
                                                persoonsgegevens</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3 md:col-span-2">
                                        <div class="w-2 h-2 bg-brand-subtle rounded-full mt-2 flex-shrink-0"></div>
                                        <div>
                                            <p class="font-medium text-brand-text">Betrouwbare samenwerkingen</p>
                                            <p class="text-sm text-brand-text/60">ik werk uitsluitend met externe
                                                partijen die aantoonbaar voldoen aan de eisen van de AVG</p>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <!-- Section 7 -->
                            <section class="mb-8">
                                <h2
                                    class="text-2xl font-bold text-brand-text mb-4 border-b-2 border-brand-primary/20 pb-2">
                                    7. Cookies
                                </h2>
                                <div class="bg-brand-secondary border-l-4 border-brand-subtle p-4 rounded-r-lg">
                                    <ul class="list-disc text-brand-text/80 pl-4 leading-relaxed mb-2">
                                        <li>
                                            Deze website gebruikt alleen functionele cookies, die nodig zijn om de site
                                            goed te laten werken:
                                            <ul class="list-disc pl-6 mt-1 space-y-1">
                                                <li><strong>Sessiecookie</strong> — onthoudt je sessie tijdens het
                                                    bezoek (bijv. boekingsstatus).</li>
                                                <li><strong>CSRF-cookie</strong> — beschermt formulieren tegen
                                                    kwaadaardige verzoeken van buitenaf.</li>
                                                <li><strong>Cookie-voorkeur</strong> — onthoudt je cookiekeuze.</li>
                                            </ul>
                                        </li>
                                        <li class="mt-2">
                                            Ik gebruik <strong>geen</strong> analytische cookies en/of tracking cookies
                                            van derden.
                                        </li>
                                    </ul>
                                </div>
                            </section>

                            <!-- Section 8 -->
                            <section class="mb-8">
                                <h2
                                    class="text-2xl font-bold text-brand-text mb-4 border-b-2 border-brand-primary/20 pb-2">
                                    8. Klachten?
                                </h2>
                                <p class="text-brand-text/80 leading-relaxed">
                                    Als je vindt dat ik niet correct omga met jouw gegevens, kun je een klacht
                                    indienen bij de Autoriteit Persoonsgegevens via
                                    <a href="https://autoriteitpersoonsgegevens.nl" target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-brand-link underline hover:text-brand-accent">autoriteitpersoonsgegevens.nl</a>.
                                </p>
                            </section>

                            <!-- Section 9 -->
                            <section class="mb-8">
                                <h2
                                    class="text-2xl font-bold text-brand-text mb-4 border-b-2 border-brand-primary/20 pb-2">
                                    9. Wijzigingen
                                </h2>
                                <p class="text-brand-text/80 leading-relaxed">
                                    Ik kan deze privacyverklaring van tijd tot tijd aanpassen en adviseer je om deze
                                    verklaring regelmatig te raadplegen. Bij ingrijpende wijzigingen zal ik dit
                                    duidelijk communiceren.
                                </p>
                            </section>

                            <!-- Footer -->
                            <footer class="border-t border-brand-primary/10 pt-6 mt-8">
                                <p class="text-sm text-brand-text/50 text-center">
                                    <strong>Laatste update:</strong> {{ privacy.updated }}
                                </p>
                            </footer>
                        </div>
                    </div>
                </div>
            </article>
        </section>
    </Layout>
</template>
