<script setup>
const props = defineProps({
    contact: Object
});

</script>
<template>
    <Layout>
        <section class="section">
            <article>
                <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                        <!-- Header -->
                        <header class="bg-ui-blue text-white px-6 py-8">
                            <h1 class="text-3xl font-bold mb-2">Algemene Voorwaarden</h1>
                            <p class="text-sm text-gray-600 mb-8">Versie: 1.1 | Laatste update: juni 2025</p>
                        </header>

                        <!-- Content -->
                        <div class="px-6 py-8">

                            <div id="inhoud" class="mb-12">
                                <h2 class="text-2xl font-bold mb-4 text-gray-900 border-b pb-2">Inhoud</h2>
                                <ul class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2 list-none p-0">
                                    <li><a href="#article-1" class="hover:text-blue-800 hover:underline">Artikel 1
                                            - Definities</a></li>
                                    <li><a href="#article-2" class="hover:text-blue-800 hover:underline">Artikel 2
                                            – Garantiefonds STO Garant</a></li>
                                    <li><a href="#article-3" class="hover:text-blue-800 hover:underline">Artikel 3
                                            – Toepasselijkheid Voorwaarden</a></li>
                                    <li><a href="#article-4" class="hover:text-blue-800 hover:underline">Artikel 4
                                            – Totstandkoming en Inhoud van de Overeenkomst</a></li>
                                    <li><a href="#article-5" class="hover:text-blue-800 hover:underline">Artikel 5
                                            – Informatie door de Organisator</a></li>
                                    <li><a href="#article-6" class="hover:text-blue-800 hover:underline">Artikel 6
                                            – Informatie door de Reiziger</a></li>
                                    <li><a href="#article-7" class="hover:text-blue-800 hover:underline">Artikel 7
                                            – Betaling</a></li>
                                    <li><a href="#article-8" class="hover:text-blue-800 hover:underline">Artikel 8
                                            – Prijswijziging</a></li>
                                    <li><a href="#article-9" class="hover:text-blue-800 hover:underline">Artikel 9
                                            – Reisdocumenten en reisbescheiden</a></li>
                                    <li><a href="#article-10" class="hover:text-blue-800 hover:underline">Artikel
                                            10 – Wijzigingen door de Reiziger</a></li>
                                    <li><a href="#article-11" class="hover:text-blue-800 hover:underline">Artikel
                                            11 – Indeplaatsstelling</a></li>
                                    <li><a href="#article-12" class="hover:text-blue-800 hover:underline">Artikel
                                            12 – Annulering door de Reiziger</a></li>
                                    <li><a href="#article-13" class="hover:text-blue-800 hover:underline">Artikel
                                            13 – Wijzigingen door de Organisator</a></li>
                                    <li><a href="#article-14" class="hover:text-blue-800 hover:underline">Artikel
                                            14 – Opzegging door de Organisator</a></li>
                                    <li><a href="#article-15" class="hover:text-blue-800 hover:underline">Artikel
                                            15 – Uitvoering van de reis en conformiteit</a></li>
                                    <li><a href="#article-16" class="hover:text-blue-800 hover:underline">Artikel
                                            16 – Hulp en bijstand</a></li>
                                    <li><a href="#article-17" class="hover:text-blue-800 hover:underline">Artikel
                                            17 – Aansprakelijkheid en Onvermijdbare Omstandigheden</a></li>
                                    <li><a href="#article-18" class="hover:text-blue-800 hover:underline">Artikel
                                            18 – Bagage en Waardevolle Spullen</a></li>
                                    <li><a href="#article-19" class="hover:text-blue-800 hover:underline">Artikel
                                            19 – Verplichtingen Reiziger</a></li>
                                    <li><a href="#article-20" class="hover:text-blue-800 hover:underline">Artikel
                                            20 – Klachten en Geschillenbeslechting</a></li>
                                    <li><a href="#article-21" class="hover:text-blue-800 hover:underline">Artikel
                                            21 – Overige bepalingen</a></li>
                                    <li><a href="#article-22" class="hover:text-blue-800 hover:underline">Artikel
                                            22 – Bijzondere wensen en vereisten</a></li>
                                </ul>
                            </div>

                            <div class="space-y-10 text-gray-800 leading-relaxed">
                                <section id="article-1" class="scroll-mt-[150px]">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">
                                        Artikel 1 -
                                        Definities
                                    </h2>
                                    <div class="space-y-4">
                                        <p><strong class="font-semibold">Organisator:</strong> Omdat we reizen. De
                                            handelaar die
                                            Reis samenstelt en deze – al dan niet via een doorverkoper – aanbiedt, en de
                                            handelaar
                                            die een Gekoppeld Reisarrangement faciliteert. Omdat we reizen is een
                                            geregistreerde
                                            handelsnaam en ingeschreven bij de Kamer van Koophandel onder nummer:
                                            <strong>97092363</strong>.
                                        </p>
                                        <p><strong class="font-semibold">Reiziger:</strong> Iedere persoon die met de
                                            Organisator
                                            een Overeenkomst betreffende een Reis wil sluiten en iedere persoon die op
                                            grond van
                                            de
                                            Overeenkomst het recht heeft om te reizen.</p>
                                        <p><strong class="font-semibold">Reisdienst:</strong> Personenvervoer, huur van
                                            een
                                            motorvoertuig of motorrijwiel, accommodatie of een andere toeristische
                                            dienst, voor
                                            zover deze diensten vallen binnen de definitie van artikel 7:500 sub a BW.
                                        </p>
                                        <p><strong class="font-semibold">Reisdienstverlener:</strong> De dienstverlener
                                            die een
                                            onderdeel van de Reis uitvoert, zoals hulppersonen
                                            (accommodatieverschaffers/vervoerders/externe gidsen/etc.) van de
                                            Organisator.</p>
                                        <p><strong class="font-semibold">Overeenkomst:</strong> De Overeenkomst
                                            inclusief deze
                                            Voorwaarden waarbij de Organisator zich jegens de Reiziger verbindt tot het
                                            verschaffen
                                            van de Reis.</p>
                                        <p><strong class="font-semibold">Schriftelijk:</strong> Op schrift of langs
                                            elektronische
                                            weg waaronder per e-mail.</p>
                                        <p><strong class="font-semibold">Voorwaarden:</strong> Deze algemene
                                            Voorwaarden.</p>
                                        <p><strong class="font-semibold">Pakketreis:</strong> Een Pakketreis in de zin
                                            van artikel 7:500 sub b BW.</p>
                                        <p><strong class="font-semibold">Gekoppeld Reisarrangement:</strong> Een
                                            Gekoppeld
                                            Reisarrangement in de zin van artikel 7:500 sub e BW.</p>
                                        <p><strong class="font-semibold">Reis:</strong> Een Pakketreis of een Gekoppeld
                                            Reisarrangement, of een enkele Reisdienst indien de Voorwaarden hierop van
                                            toepassing
                                            zijn verklaard.</p>
                                        <p><strong class="font-semibold">Reisbescheiden:</strong> De documenten die
                                            nodig zijn
                                            voor
                                            de uitvoering van de Reis, zoals vervoertickets, vouchers en andere
                                            relevante
                                            informatie.</p>
                                        <p><strong class="font-semibold">Onvermijdbare en Buitengewone Omstandigheden
                                                (Overmacht):</strong> Een situatie die zich voordoet onafhankelijk van
                                            de wil
                                            van de
                                            partij die zich erop beroept en waarvan de gevolgen, ondanks alle redelijke
                                            voorzorgsmaatregelen, niet konden worden vermeden.</p>
                                        <p><strong class="font-semibold">Non-conformiteit:</strong> Een tekortkoming in
                                            de
                                            uitvoering van de Reis die niet overeenkomt met de redelijke verwachtingen
                                            die de
                                            Reiziger op grond van de Overeenkomst mocht hebben.</p>
                                        <p><strong class="font-semibold">Werkdagen:</strong> Maandag tot en met vrijdag,
                                            met
                                            uitzondering van in Nederland erkende feestdagen. Indien in deze Voorwaarden
                                            sprake
                                            is
                                            van een termijn in Werkdagen of handelingen binnen Werkdagen, wordt verwezen
                                            naar de
                                            kantooruren (9:00 uur tot 17:00 uur Nederlandse tijd), tenzij uitdrukkelijk
                                            anders
                                            vermeld.</p>
                                    </div>
                                </section>

                                <section id="article-2" class="scroll-mt-[150px]">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">
                                        Artikel 2 – Garantiefonds STO Garant</h2>
                                    <p class="mb-4">Om te voldoen aan de wettelijk verplichte garantiestelling maakt de
                                        Organisatie gebruik van STO Garant. U kunt dit controleren via de STO Garant
                                        deelnemerspagina op <a href="https://sto-garant.nl/reizigers/aangesloten-organisaties" class="text-blue-500 hover:text-blue-800 hover:underline">sto-garant.nl</a>. Bij elk (reis)aanbod van de Organisator wordt
                                        duidelijk vermeld of de garantie van STO Garant van toepassing is. Binnen de
                                        grenzen
                                        van
                                        de garantieregeling (te vinden op <a href="www.sto-garant.nl/downloads" class="text-blue-500 hover:text-blue-800 hover:underline">www.sto-garant.nl/downloads</a>) is de garantie
                                        van
                                        STO
                                        Garant van toepassing. In de garantieregeling leest u wat de garantie inhoudt en
                                        welke
                                        voorwaarden van toepassing zijn.</p>
                                    <p class="mb-4">Indien de garantie van STO Garant van toepassing is op uw boeking,
                                        dan
                                        betaalt u de reissom niet aan de Organisator, maar aan de derdengeldenrekening
                                        van
                                        Stichting Derdengelden Certo Escrow, een bij De Nederlandsche Bank (DNB) en de
                                        Autoriteit Financiële Markten (AFM) geregistreerde betaaldienstverlener. Deze
                                        stichting
                                        derdengelden waarborgt uw reissom tot na afloop van uw boeking. Wanneer diensten
                                        door
                                        financieel onvermogen van de Organisator niet (volledig en/of tijdig) worden
                                        verleend,
                                        dan voert STO Garant de garantie uit. In de garantieregeling leest u hoe u daar
                                        in
                                        dat
                                        geval aanspraak op maakt.</p>
                                </section>

                                <section id="article-3" class="scroll-mt-[150px]">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">
                                        Artikel 3 – Toepasselijkheid Voorwaarden</h2>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">3.1. Pakketreizen</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">Deze Voorwaarden zijn van toepassing op alle door de
                                        Organisator
                                        aangeboden of met de Organisator overeengekomen Pakketreizen en vormen een
                                        onlosmakelijk
                                        onderdeel daarvan.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">3.2. Gekoppelde
                                            reisarrangementen</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">Deze Voorwaarden zijn tevens van toepassing op Reisdiensten die
                                        onderdeel zijn van een gekoppeld reisarrangement. Op de Reisdiensten die niet
                                        met de
                                        Organisator zijn overeengekomen zijn de voorwaarden van de leverancier van die
                                        Reisdienst van toepassing.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">3.3. Reisdiensten</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">Deze Voorwaarden zijn tevens van toepassing op Reisdiensten die
                                        geen
                                        pakketreis of gekoppeld reisarrangement vormen. Op deze Reisdiensten is Titel 7a
                                        van
                                        Boek 7 Burgerlijk Wetboek niet van toepassing en geldt er, tenzij uitdrukkelijk
                                        anders
                                        vermeld, geen wettelijke bescherming bij insolventie.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">3.4. Afwijkende en
                                            aanvullende
                                            voorwaarden</strong></h3>
                                    <p class="mb-4 pl-4">Afwijkende en aanvullende voorwaarden dienen Schriftelijk te
                                        worden
                                        overeengekomen. Afwijkende bepalingen in de individuele overeenkomst hebben
                                        voorrang
                                        boven deze Voorwaarden.</p>
                                </section>

                                <section id="article-4" class="scroll-mt-[150px]">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">
                                        Artikel 4 – Totstandkoming en Inhoud van de Overeenkomst</h2>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">4.1. Het Aanbod en
                                            informatie</strong></h3>
                                    <p class="mb-4 pl-4">De aangeboden Reis omvat de diensten zoals omschreven in de
                                        uitingen
                                        van Omdat we reizen (offertes, publicaties, website). De inhoud van het aanbod
                                        wordt
                                        bepaald door de informatie verstrekt door of namens de Organisator vóór het
                                        sluiten
                                        van
                                        de Overeenkomst; deze informatie is een integraal deel van de Overeenkomst. De
                                        Organisator is verantwoordelijk voor de juistheid van in haar aanbod opgenomen
                                        informatie, ook indien deze afkomstig is van Reisdienstverleners. De reisduur is
                                        in
                                        hele
                                        dagen, waarbij vertrek- en aankomstdag als hele dagen worden gerekend, ongeacht
                                        de
                                        vertrek- en aankomsttijden. Kennelijke fouten of vergissingen in het aanbod
                                        binden
                                        de
                                        Organisator niet, indien de Reiziger redelijkerwijs had moeten begrijpen dat het
                                        een
                                        fout betrof. Bij twijfel aan de juistheid van de prijs of informatie dient de
                                        Reiziger
                                        navraag te doen.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">4.2. Totstandkoming van de
                                            Overeenkomst</strong></h3>
                                    <p class="mb-4 pl-4">Alle offertes van de Organisator zijn onder voorbehoud van
                                        beschikbaarheid. De Overeenkomst komt tot stand zodra de Reiziger het aanbod van
                                        de
                                        Organisator aanvaardt. Na totstandkoming ontvangt de Reiziger zo spoedig
                                        mogelijk
                                        een
                                        schriftelijke bevestiging, tevens factuur.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">4.3. Boekingen 'op
                                            aanvraag'</strong></h3>
                                    <p class="mb-4 pl-4">Bepaalde reizen geschieden 'op aanvraag'. Dit betekent dat na
                                        uw
                                        aanmelding om te boeken de beschikbaarheid bij betrokken leveranciers wordt
                                        gecontroleerd. Uw aanmelding om een reis 'op aanvraag' te boeken is een aanbod
                                        tot
                                        het
                                        sluiten van een Overeenkomst, welke pas definitief tot stand komt nadat de
                                        Organisator
                                        de beschikbaarheid bij de leveranciers heeft gecontroleerd en de boeking
                                        Schriftelijk
                                        aan de Reiziger heeft bevestigd.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">4.4. Bijzondere aspecten van
                                            de
                                            boeking</strong></h3>

                                    <div class="mb-4 pl-4 space-y-2">
                                        <p>
                                            <strong class="font-semibold">Herroepingsrecht:</strong> Een
                                            boeking
                                            van de Reis is definitief. De Reiziger heeft geen recht om de Overeenkomst
                                            te
                                            herroepen.
                                        </p>
                                        <p>
                                            <strong class="font-semibold">Meerderjarigheid:</strong> De Reiziger die de
                                            reis
                                            boekt
                                            dient meerderjarig te zijn (18 jaar of ouder).<br>
                                        </p>
                                        <p>
                                            <strong class="font-semibold">Boeken voor anderen & communicatie:</strong>
                                            De
                                            Reiziger
                                            die namens of ten behoeve van één of meer andere reizigers een Overeenkomst
                                            aangaat,
                                            is
                                            hoofdelijk aansprakelijk voor alle verplichtingen die daaruit voortvloeien.
                                            De
                                            medereizigers zijn ieder voor hun eigen deel jegens de Reiziger
                                            aansprakelijk.
                                            Alle
                                            communicatie met betrekking tot de boeking (zoals bevestiging, factuur,
                                            Reisbescheiden)
                                            wordt enkel verstuurd naar de Reiziger die de boeking verricht (de
                                            opdrachtgever).
                                            De
                                            opdrachtgever is verantwoordelijk voor het informeren van de medereizigers
                                            over
                                            de
                                            inhoud van deze Voorwaarden en alle relevante Reisbescheiden. De
                                            opdrachtgever
                                            is
                                            aansprakelijk jegens de Organisator voor schade die de Organisator lijdt als
                                            gevolg
                                            van
                                            het niet nakomen van deze informatieplichten, voor zover deze schade
                                            redelijkerwijs
                                            aan
                                            het nalaten van de opdrachtgever kan worden toegerekend.
                                        </p>
                                    </div>
                                </section>

                                <section id="article-5" class="scroll-mt-[150px]">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">
                                        Artikel 5 – Informatie door de Organisator</h2>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">5.1. Reissom</strong></h3>
                                    <p class="mb-4 pl-4">De vermelde prijzen gelden per persoon, tenzij uitdrukkelijk
                                        anders
                                        is
                                        vermeld. Indien afgeweken wordt van het aantal personen waarop de prijs per
                                        persoon
                                        is
                                        gebaseerd, kan de Organisator een nieuwe reissom per persoon vaststellen.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">5.2. Informatie vóór de
                                            boeking</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">Voordat de Overeenkomst tot stand komt, verstrekt de
                                        Organisator
                                        aan de
                                        Reiziger het ingevulde standaardinformatieformulier (Bijlage bij de Richtlijn
                                        (EU)
                                        2015/2302) en de overige wettelijk verplichte Informatie zoals vermeld in
                                        artikel
                                        7:502
                                        BW.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">5.3. Informatie bij de
                                            boeking</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">Bij het sluiten van de Overeenkomst of onverwijld daarna,
                                        verstrekt
                                        de
                                        Organisator aan de Reiziger de bevestiging van de Overeenkomst, met inbegrip van
                                        de
                                        geaccepteerde bijzondere wensen (indien van toepassing), op uw nationaliteit
                                        afgestemde
                                        informatie over de benodigde reisdocumenten (paspoorten, visa, etc.) en
                                        eventuele
                                        formaliteiten op gezondheidsgebied, en de overige wettelijk verplichte
                                        informatie.
                                    </p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">5.4. Informatie vóór de
                                            reis</strong></h3>
                                    <p class="mb-4 pl-4">Tijdig voor aanvang van de Reis en uiterlijk bij het
                                        verstrekken
                                        van de
                                        Reisbescheiden, ontvangt de Reiziger de nodige informatie over de geboekte Reis,
                                        waaronder informatie over de geplande vertrektijden, uiterste inchecktijden,
                                        tussenstops, aansluitingen, aankomsttijden en eventueel de naam van de
                                        uitvoerende
                                        vervoerder(s).</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">5.5. Contactgegevens tijdens
                                            de
                                            reis</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">Voor aanvang van de reis verstrekt de Organisator aan de
                                        Reiziger
                                        de
                                        contactgegevens in geval van nood van de Organisator en, in voorkomend geval,
                                        diens
                                        lokale vertegenwoordiger of de accommodatie.</p>
                                </section>

                                <section id="article-6" class="scroll-mt-[150px]">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">
                                        Artikel 6 – Informatie door de Reiziger</h2>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">6.1. Algemene
                                            informatieplicht</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">Vóór of bij het sluiten van de Overeenkomst verstrekt de
                                        Reiziger
                                        alle
                                        relevante informatie van zichzelf en aangemelde medereizigers. Deze informatie
                                        is
                                        essentieel voor de uitvoering, veiligheid en het welzijn gedurende de Reis,
                                        waaronder
                                        benodigde specifieke gegevens die nodig zijn voor tickets op naam.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">6.2. Melding van specifieke
                                            omstandigheden</strong></h3>
                                    <p class="mb-4 pl-4">Reizigers met verminderde mobiliteit en hun begeleiders,
                                        zwangere
                                        vrouwen, alleenreizende minderjarigen en Reizigers met een ziekte die mogelijk
                                        effect
                                        heeft op de Reis dienen dit bij het aangaan van de Overeenkomst of zo spoedig
                                        mogelijk
                                        nadat de Reiziger hiermee bekend is te melden bij de Organisator. Indien deze
                                        omstandigheden een essentieel vereiste vormen voor deelname aan de reis, dient
                                        dit
                                        expliciet als zodanig kenbaar te worden gemaakt conform <a href="#article-22.2"
                                            class="hover:text-blue-800 underline">Artikel 22.2 - Vereisten</a>.
                                    </p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">6.3. Dieetwensen</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">Specifieke dieetwensen bij inbegrepen maaltijden worden als
                                        voorkeur
                                        behandeld conform <a href="#article-22.1"
                                            class="hover:text-blue-800 underline">Artikel 22.1 - Voorkeuren (wensen)</a>. Hoewel de Organisator zijn
                                        best doet deze wensen door te geven aan het hotel of de betreffende dienstverlener,
                                        kunnen deze niet gegarandeerd worden. Eventuele meerkosten die ter plaatse door de
                                        dienstverlener in rekening worden gebracht voor het voldoen aan dieetwensen zijn
                                        voor rekening van de Reiziger.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">6.4. Medicijngebruik</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">De Reiziger is zelf verantwoordelijk voor het meenemen van
                                        voldoende
                                        medicijnen voor de gehele reis. Bij reizen naar het buitenland wordt geadviseerd
                                        om
                                        een
                                        recept in het Latijn mee te nemen.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">6.5. Gevolgen van onjuiste of
                                            onvolledige
                                            informatie</strong></h3>
                                    <p class="mb-4 pl-4">Onjuiste of onvolledige relevante informatie verstrekt door de
                                        Reiziger
                                        kan leiden tot uitsluiting van (verdere) deelname aan de Reis, waarbij de
                                        Reiziger
                                        aansprakelijk blijft voor annulerings- en andere kosten. Indien noodzakelijk
                                        voor de
                                        uitvoering, worden relevante gegevens van de Reiziger doorgegeven aan
                                        dienstverleners.
                                        Bij weigering van de Reiziger om noodzakelijke gegevens te verstrekken of door
                                        te
                                        laten
                                        geven, is de Organisator niet aansprakelijk voor niet- of foutieve uitvoering
                                        van de
                                        betreffende diensten.</p>
                                </section>

                                <section id="article-7" class="scroll-mt-[150px]">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">
                                        Artikel 7 – Betaling
                                    </h2>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">7.1. Aanbetaling</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">Na de totstandkoming van de Overeenkomst en tegelijk met de
                                        bevestiging/factuur en uiterlijk binnen 7 dagen na dagtekening daarvan, is de
                                        Reiziger
                                        een aanbetaling verschuldigd van 30% van de reissom. De boekingskosten (€27,50)
                                        en
                                        kosten voor de garantieregeling (€10,00) dienen ook binnen 7 dagen na ontvangst
                                        van
                                        de
                                        factuur te zijn voldaan.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">7.2. Restbetaling</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">Het restant van de reissom dient uiterlijk 6 weken voor de
                                        aanvangsdatum van de Reis te zijn voldaan.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">7.3. Boeking kort voor
                                            vertrek</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">Bij totstandkoming van de Overeenkomst binnen 6 weken voor de
                                        aanvangsdatum van de reis, dient de volledige reissom per ommegaande en in ieder
                                        geval
                                        voor aanvang van de reis te zijn voldaan.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">7.4. Niet-terugbetaalbare
                                            elementen</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">Sommige onderdelen van de Reis zijn 'niet-terugbetaalbaar'.
                                        Deze
                                        kosten
                                        worden door de Organisator na ontvangst van de aanbetaling definitief voldaan
                                        aan
                                        leveranciers en bij annulering niet (volledig) terugbetaald. Prijswijzigingen na
                                        het
                                        sluiten van de Overeenkomst zijn alleen mogelijk conform de wettelijke
                                        bepalingen en <a href="#article-8" class="hover:text-blue-800 underline">Artikel
                                            8 – Prijswijziging</a>, van deze Voorwaarden.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">7.5. Verzuim</strong></h3>
                                    <p class="mb-4 pl-4">Indien de Reiziger niet betaalt binnen de hierboven of op de
                                        factuur
                                        genoemde termijn, is de Reiziger zonder dat een nadere ingebrekestelling is
                                        vereist
                                        in
                                        verzuim.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">7.6. Uitblijven van
                                            betaling</strong></h3>
                                    <p class="mb-4 pl-4">Indien de Reiziger in verzuim is, kan de Organisator het
                                        toezenden
                                        van
                                        de Reisbescheiden zonder nadere aankondiging opschorten tot de volledige
                                        betaling is
                                        ontvangen. Indien betaling ook na aanmaning uitblijft of indien niet voor
                                        aanvang
                                        van de
                                        reis is betaald, heeft de Organisator het recht de Reiziger uit te sluiten van
                                        deelname
                                        aan de Reis. De verplichting tot betaling blijft in dat geval bestaan. In plaats
                                        van
                                        de
                                        Reiziger uit te sluiten van deelname kan de Organisator de Overeenkomst
                                        annuleren en
                                        de
                                        daarvoor verschuldigde annuleringskosten bij de Reiziger in rekening brengen
                                        conform <a href="#article-12" class="hover:text-blue-800 underline">Artikel 12 –
                                            Annulering door de Reiziger</a>.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">7.7. Geen annulering door
                                            niet-betalen</strong></h3>
                                    <p class="mb-4 pl-4">De Reiziger kan de boeking niet annuleren door simpelweg niet
                                        aan
                                        de
                                        financiële verplichtingen te voldoen. Een annulering dient Schriftelijk te
                                        geschieden conform <a href="#article-12"
                                            class="hover:text-blue-800 underline">Artikel 12 – Annulering door de
                                            Reiziger</a>.</p>
                                </section>

                                <section id="article-8" class="scroll-mt-[150px]">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">
                                        Artikel 8 –
                                        Prijswijziging</h2>
                                    <h3 id="article-8.1" class="mb-2 scroll-mt-[150px]"><strong
                                            class="text-lg font-semibold">8.1. Recht op
                                            Prijswijziging</strong></h3>
                                    <p class="mb-4 pl-4">De Organisator behoudt zich het recht voor om met betrekking
                                        tot
                                        reeds
                                        aangegane Overeenkomsten tot 20 dagen vóór de dag van vertrek de reissom te
                                        verhogen
                                        als
                                        gevolg van prijswijzigingen in:</p>
                                    <ul class="list-disc pl-8 mb-4">
                                        <li>de kosten van brandstof of andere energiebronnen; en/of</li>
                                        <li>de belastingen of vergoedingen voor de in de Pakketreis begrepen
                                            Reisdiensten,
                                            geheven door niet rechtstreeks bij de uitvoering van de Reis betrokken
                                            derden;
                                            en/of
                                        </li>
                                        <li>de toepasselijke wisselkoersen die voor de Reis van belang zijn.</li>
                                    </ul>
                                    <p class="mb-4 pl-4">Indien de kosten op deze gronden dalen, heeft de Reiziger recht
                                        op
                                        een
                                        Overeenkomstige prijsverlaging.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">8.2. Prijsherziening</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">De methode voor de berekening van prijsverhogingen en
                                        -verlagingen
                                        op basis van de in <a href="#article-8.1"
                                            class="hover:text-blue-800 underline">8.1 - Recht op Prijswijziging</a> –
                                        genoemde gronden dient voor de
                                        boeking
                                        kenbaar te zijn gemaakt aan de Reiziger en is onderdeel van de Overeenkomst. De
                                        gepubliceerde reissommen zijn gebaseerd op de prijzen, geldende koersen,
                                        brandstofprijzen, heffingen en belastingen, zoals deze bij de Organisator bekend
                                        waren
                                        op het moment van publicatie van het aanbod.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">8.3. Significante
                                            verhoging</strong></h3>
                                    <p class="mb-4 pl-4">Indien de prijsverhoging meer dan 8% van de totale reissom
                                        bedraagt,
                                        heeft de Reiziger het recht de Overeenkomst zonder betaling van een
                                        beëindigingsvergoeding te beëindigen. In dat geval heeft de Reiziger recht op
                                        onverwijlde terugbetaling van alle reeds betaalde bedragen. De Organisator stelt
                                        de
                                        Reiziger in kennis van de prijsverhoging en de mogelijkheid tot beëindiging,
                                        waarbij
                                        een
                                        redelijke termijn wordt gesteld waarbinnen de Reiziger Schriftelijk kenbaar
                                        dient te
                                        maken of deze de Overeenkomst beëindigt. Wordt de Overeenkomst niet binnen de
                                        gestelde
                                        termijn beëindigd, dan geldt de prijsverhoging als aanvaard en vervalt het recht
                                        op
                                        beëindiging.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">8.4. Recht op
                                            prijsverlaging</strong></h3>
                                    <p class="mb-4 pl-4">Een prijsverhoging wordt de Reiziger duidelijk en begrijpelijk
                                        meegedeeld, vergezeld van een motivering van die prijsverhoging en een
                                        berekening.
                                        Zoals
                                        vermeld in 8.1. Recht op Prijswijziging, heeft de Reiziger recht op een
                                        prijsverlaging
                                        indien de relevante kosten dalen na het sluiten van de Overeenkomst. De
                                        Organisator
                                        zal
                                        de Reiziger informeren over een dergelijke prijsverlaging.</p>
                                </section>

                                <section id="article-9" class="scroll-mt-[150px]">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">
                                        Artikel 9 –
                                        Reisdocumenten en reisbescheiden</h2>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">9.1. Reisdocumenten</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">De Reiziger dient gedurende de gehele Reis in bezit te zijn van
                                        reisnoodzakelijke reisdocumenten, zoals een paspoort, eventuele visa,
                                        vaccinatiebewijzen
                                        enzovoort. Het is verantwoordelijkheid van de Reiziger om de benodigde
                                        informatie
                                        over
                                        vereiste documenten en gezondheidsvoorschriften in te winnen bij de betreffende
                                        instanties en instellingen (zoals ambassades, consulaten, huisarts of GGD). De
                                        Reiziger
                                        dient ervoor zorg te dragen dat hij tijdig over de juiste documenten beschikt en
                                        aan
                                        alle vereisten voldoet. De Reiziger dient vóór de boeking te verifiëren of er
                                        voldoende
                                        tijd is om de benodigde documenten te verkrijgen. Van de Reiziger wordt verwacht
                                        dat
                                        hij
                                        zich goed voorbereidt op eventuele inreisbeperkingen of specifieke vereisten van
                                        het
                                        land van bestemming.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">9.2. Ontbrekende
                                            documenten</strong></h3>
                                    <p class="mb-4 pl-4">Indien de Reiziger niet aan de (voortzetting van de) reis kan
                                        deelnemen
                                        wegens het ontbreken van enig geldig document of wegens het niet voldoen aan
                                        enig
                                        voorschrift, dan wel indien ten gevolge hiervan de Reis als geheel geen
                                        uitvoering
                                        of
                                        voortgang kan vinden, komt zulks met alle daaraan verbonden gevolgen en kosten
                                        geheel
                                        voor de rekening van de Reiziger.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">9.3. Reisbescheiden</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">De benodigde Reisbescheiden zullen, tenzij anders
                                        overeengekomen,
                                        tijdig en uiterlijk 14 dagen voor vertrek in bezit van de Reiziger worden
                                        gesteld,
                                        op
                                        voorwaarde dat de factuur voor de Reis volledig is voldaan. Definitieve vertrek-
                                        en
                                        aankomsttijden worden vermeld in de Reisbescheiden. De Reiziger dient de
                                        Reisbescheiden
                                        onverwijld na ontvangst te controleren op volledigheid en juistheid en te
                                        verifiëren
                                        of
                                        deze overeenkomen met het overeengekomen reisschema. Eventuele onjuistheden of
                                        onvolledigheden dient de Reiziger direct na ontdekking te melden aan de
                                        Organisator.
                                        De
                                        bevestigde tijden van vervoer zijn indicatief en kunnen kort voor of tijdens de
                                        reis
                                        wijzigen. De Reiziger dient de actuele reistijden kort voor vertrek en ter
                                        plaatse
                                        te
                                        verifiëren.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">9.4. Specifieke
                                            Documenten</strong></h3>
                                    <div class="mb-4 pl-4 space-y-2">
                                        <p>
                                            <strong class="font-semibold">Fysieke treintickets:</strong>
                                            Indien
                                            fysieke treintickets zijn verstrekt en de Reiziger de reis annuleert, is het
                                            voor
                                            het
                                            verkrijgen van eventuele restitutie noodzakelijk dat deze fysieke tickets
                                            tijdig
                                            en
                                            in
                                            de vereiste staat worden teruggestuurd naar de Organisator. De Reiziger is
                                            verantwoordelijk voor de correcte en tijdige aankomst van de waardepapieren
                                            bij
                                            de
                                            Organisator.<br>
                                        </p>
                                        <p>
                                            <strong class="font-semibold">Interrail Passes:</strong> Indien een
                                            Interrail
                                            ticket
                                            of
                                            Pass is geleverd, is de Reiziger zelf verantwoordelijk voor het juist
                                            invullen
                                            van
                                            de
                                            reispas en het op de juiste wijze activeren van de reisdagen conform de
                                            instructies
                                            en
                                            voorwaarden van de Interrail Pass.
                                        </p>
                                    </div>
                                </section>

                                <section id="article-10" class="scroll-mt-[150px]">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">
                                        Artikel 10 –
                                        Wijzigingen
                                        door de Reiziger</h2>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">10.1. Wijzigen</strong></h3>
                                    <p class="mb-4 pl-4">De Reiziger kan de Organisator Schriftelijk verzoeken de
                                        Overeenkomst
                                        te wijzigen. De Organisator spant zich in om een redelijk verzoek tot wijziging
                                        in
                                        te
                                        willigen, mits voor de betrokken Reisdienstverleners het verzoek uitvoerbaar is
                                        en
                                        ze
                                        deze accepteren.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">10.2.
                                            Wijzigingskosten</strong></h3>
                                    <p class="mb-4 pl-4">Indien op verzoek van de Reiziger een wijziging van de
                                        Overeenkomst
                                        wordt doorgevoerd, kan het zijn dat de Reisdienstverleners kosten in rekening
                                        brengen
                                        voor de wijziging. Indien de nieuwe reissom lager uitvalt dan de oorspronkelijke
                                        reissom, wordt het verschil verrekend met de verschuldigde kosten van wijziging.
                                        Betaling van de doorgevoerde wijzigingen en de bijbehorende kosten dient direct
                                        na
                                        bevestiging en facturering te worden voldaan.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">10.3. Afwijzing</strong></h3>
                                    <p class="mb-4 pl-4">
                                        Wanneer de gevraagde wijziging niet door de betrokken Reisdienstverleners kan
                                        worden gehonoreerd of om andere redenen niet uitvoerbaar is, blijft de
                                        oorspronkelijke Overeenkomst ongewijzigd van kracht. Indien de Reiziger ondanks
                                        de afwijzing van uw wijzigingsverzoek vasthoudt aan de gewenste wijziging en de
                                        oorspronkelijke Overeenkomst niet wilt nakomen, wordt dit beschouwd als een
                                        annulering van de Overeenkomst, waarop de annuleringsvoorwaarden van
                                        <a href="#article-12" class="hover:text-blue-800 underline">Artikel 12 –
                                            Annulering door de Reiziger</a>
                                        van toepassing zijn.
                                    </p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">10.4. Uitzonderingen</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">Een verzoek tot wijziging van de vertrekdatum of een
                                        vermindering
                                        van
                                        het aantal betalende passagiers in een boeking wordt niet beschouwd als een
                                        wijziging in
                                        de zin van dit artikel, maar als een (gedeeltelijke) annulering van de
                                        Overeenkomst.
                                        Op
                                        een dergelijke (deel)annulering zijn de annuleringsbepalingen en kosten zoals
                                        vermeld in
                                        Artikel <a href="#article-12" class="hover:text-blue-800 underline">12 –
                                            Annulering door de Reiziger</a> van toepassing.</p>
                                </section>

                                <section id="article-11" class="scroll-mt-[150px]">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">
                                        Artikel 11 –
                                        Indeplaatsstelling</h2>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">11.1. Voorwaarden en
                                            kennisgeving</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">De Reiziger, heeft het recht om de Reis over te dragen aan een
                                        andere
                                        persoon die voldoet aan alle aan de Reis verbonden voorwaarden. De Reiziger
                                        verzoekt
                                        de
                                        Organisator uiterlijk 21 dagen voor aanvang van de Reis, althans met
                                        inachtneming
                                        van
                                        een redelijke termijn waarbinnen de nodige handelingen kunnen worden verricht,
                                        om de
                                        persoon in de plaats te stellen. Overdracht is slechts mogelijk voor zover de
                                        voorwaarden van de betrokken Reisdienstverlener dit toelaten.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">11.2. Hoofdelijke
                                            aansprakelijkheid en
                                            bijkomende kosten</strong></h3>
                                    <p class="mb-4 pl-4">De oorspronkelijke Reiziger, en degene die de Reis overneemt
                                        zijn
                                        hoofdelijk aansprakelijk jegens de Organisator voor de betaling van het nog
                                        verschuldigde bedrag van de reissom en voor eventuele bijkomende vergoedingen,
                                        toeslagen
                                        en andere kosten die voortvloeien uit de indeplaatsstelling, waaronder
                                        wijzigingskosten
                                        zoals vermeld in 10.2. Wijzigingskosten.</p>
                                </section>

                                <section id="article-12" class="scroll-mt-[150px]">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">
                                        Artikel 12 –
                                        Annulering
                                        door de Reiziger</h2>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">12.1. Recht op
                                            annulering</strong></h3>
                                    <p class="mb-4 pl-4">De Reiziger kan de Overeenkomst te allen tijde vóór aanvang van
                                        de
                                        Reis
                                        opzeggen.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">12.2. Wijze van
                                            annulering</strong></h3>
                                    <p class="mb-4 pl-4">Annulering dient Schriftelijk te geschieden. De datum waarop de
                                        Schriftelijke kennisgeving van annulering door de Organisator wordt ontvangen op
                                        een
                                        Werkdag, geldt als het moment van opzegging. Bij ontvangst na 17:00 uur of
                                        buiten de
                                        Werkdagen om, wordt de eerst volgende Werkdag gezien als de datum van ontvangst.
                                    </p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">12.3.
                                            Annuleringskosten</strong></h3>
                                    <p class="mb-4 pl-4">Bij opzegging van de Overeenkomst is de Reiziger de volgende
                                        gestandaardiseerde bedragen verschuldigd:</p>
                                    <ul class="list-disc pl-8 mb-4">
                                        <li>tot en met 56 dagen vóór de dag van vertrek: 30% van de reissom;</li>
                                        <li>vanaf 55 dagen tot en met 22 dagen vóór de dag van vertrek: 60% van de
                                            reissom;
                                        </li>
                                        <li>vanaf 21 dagen vóór de dag van vertrek: 100% van de reissom.</li>
                                    </ul>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">12.4. Deelannulering</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">Bij vermindering van het aantal deelnemers van de Reis waarbij
                                        niet
                                        alle diensten evenredig kunnen worden verminderd (bijvoorbeeld bij accommodatie
                                        op
                                        basis
                                        van een kamerprijs), bedragen de verschuldigde annuleringskosten de reissom
                                        minus de
                                        werkelijke kostenbesparingen die de Organisator als gevolg van de deelannulering
                                        heeft.
                                        Indien van toepassing worden inkomsten uit werkelijk alternatief gebruik van de
                                        vrijgekomen capaciteit op de annuleringskosten in mindering gebracht. Indien een
                                        reis
                                        niet-terugbetaalbaar elementen bevat, kan het mogelijk zijn dat deze niet per
                                        Reiziger
                                        geannuleerd kunnen worden, maar alleen voor het volledige gezelschap, met de
                                        bijbehorende kosten.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">12.5. Annulering ná
                                            omboeking</strong></h3>
                                    <p class="mb-4 pl-4">Indien de Reiziger en de Organisator de Reis hebben omgeboekt
                                        naar
                                        een
                                        later moment en de Reiziger vervolgens de omgeboekte reis annuleert, bedragen de
                                        annuleringskosten ten minste het bedrag dat verschuldigd zou zijn geweest als
                                        deze
                                        op de
                                        datum van de oorspronkelijke omboeking zou zijn geannuleerd.</p>
                                </section>

                                <section id="article-13" class="scroll-mt-[150px]">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">
                                        Artikel 13 –
                                        Wijzigingen
                                        door de Organisator</h2>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">13.1. Onbeduidende
                                            wijzigingen vóór
                                            vertrek</strong></h3>
                                    <p class="mb-4 pl-4">De Organisator heeft het recht vóór aanvang van de Reis de
                                        Overeenkomst
                                        eenzijdig te wijzigen voor zover het onbeduidende wijzigingen betreft. De
                                        Reiziger
                                        wordt
                                        hier Schriftelijk en op duidelijke wijze van op de hoogte gesteld. Een wijziging
                                        in
                                        het
                                        reisschema voor treinreizen wordt in de regel als onbeduidend beschouwd, tenzij
                                        deze
                                        wijziging aantoonbaar essentieel is voor de Reiziger en dit vooraf kenbaar is
                                        gemaakt.
                                    </p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">13.2. Ingrijpende wijzigingen
                                            vóór
                                            vertrek</strong></h3>
                                    <p class="mb-4 pl-4">Indien noodzakelijk kan de Organisator de voornaamste kenmerken
                                        van
                                        de
                                        Overeenkomst vóór aanvang van de Reis ingrijpend wijzigen. Hieronder valt
                                        eveneens
                                        het
                                        aanbieden van een alternatieve Reis die, indien redelijkerwijs mogelijk, van
                                        gelijkwaardige of hogere kwaliteit is.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">13.3. Procedure en rechten
                                            bij ingrijpende
                                            wijzigingen vóór vertrek</strong></h3>
                                    <p class="mb-4 pl-4">Bij ingrijpende wijzigingen vóór vertrek stelt de Organisator
                                        de
                                        Reiziger onverwijld Schriftelijk op de hoogte van: De voorgestelde wijzigingen
                                        en,
                                        indien van toepassing, de inhoud van een vervangende Reis of de hoogte van de
                                        passende
                                        prijsverlaging. De redelijke termijn waarbinnen de Reiziger de Organisator
                                        Schriftelijk
                                        in kennis dient te stellen van zijn besluit om de wijziging te aanvaarden, dan
                                        wel
                                        de
                                        Overeenkomst zonder betaling van de annuleringskosten te beëindigen. Dat indien
                                        de
                                        Reiziger niet tijdig reageert, de wijziging geldt als aanvaard en het recht op
                                        beëindiging vervalt zonder betaling van annuleringskosten. De reiziger dient de
                                        organisator binnen deze termijn schriftelijk in kennis te stellen van zijn
                                        besluit.
                                        Indien de Reiziger op grond van dit artikel de Overeenkomst beëindigt en de
                                        Reiziger
                                        geen vervangende Pakketreis aanvaardt, betaalt de Organisator alle door of
                                        namens de
                                        Reiziger betaalde bedragen onverwijld en uiterlijk binnen 14 dagen aan de
                                        Reiziger
                                        terug.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">13.4. Wijziging
                                            overeengekomen bijzondere
                                            wens</strong></h3>
                                    <p class="mb-4 pl-4">Indien de Organisator niet of niet met een redelijke inspanning
                                        kan
                                        voldoen aan een Schriftelijk overeengekomen bijzondere wens van de Reiziger
                                        (conform
                                        <a href="#article-22.2" class="hover:text-blue-800 underline">Artikel 22.2 - Vereisten</a> van deze Voorwaarden), dan kan de Organisator de Reis op dit
                                        onderdeel wijzigen. De Reiziger kan in dat geval de wijziging aanvaarden of de
                                        Overeenkomst beëindigen zonder betaling van annuleringskosten.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">13.5. Wijzigingen na vertrek</strong></h3>
                                    <p class="mb-4 pl-4">Indien wijzigingen zich voordoen na vertrek en een significant
                                        deel van de overeengekomen Reisdiensten niet kan worden verricht, zal de Organisator
                                        onmiddellijk alles in het werk stellen om de continuering van de Reis te waarborgen. Indien
                                        het onmogelijk is dergelijke maatregelen te treffen, of wanneer de Reiziger de
                                        aangeboden alternatieve om deugdelijke redenen niet accepteert, zal de Organisator, zonder
                                        extra kosten, zorg dragen voor de terugreis naar de plaats van vertrek, dan wel naar
                                        een andere overeengekomen plaats van terugkeer.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">13.6. Wijzigingen door schuld
                                            Reiziger</strong></h3>
                                    <p class="mb-4 pl-4">Indien de oorzaak van een wijziging (vóór of na vertrek) aan de
                                        Reiziger kan worden toegerekend, komt de hieruit voortvloeiende schade voor
                                        rekening van de Reiziger.</p>
                                </section>

                                <section id="article-14" class="scroll-mt-[150px]">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">
                                        Artikel 14 – Opzegging door de Organisator</h2>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">14.1. Opzegging</strong></h3>
                                    <p class="mb-4 pl-4">In de volgende gevallen kan de Organisator de Overeenkomst vóór
                                        aanvang
                                        van de reis opzeggen en de Reiziger alle voor de Reis betaalde bedragen,
                                        terugbetalen
                                        zonder dat de Organisator de Reiziger een schadevergoeding verschuldigd is:</p>
                                    <ol class="list-[lower-alpha] pl-8 mb-4">
                                        <li>ingeval het aantal aanmeldingen kleiner is dan het in de Overeenkomst
                                            vermelde
                                            minimumaantal en de Reiziger van de opzegging in kennis wordt gesteld binnen
                                            de
                                            in
                                            de overeenkomst bepaalde termijn, maar uiterlijk:
                                            <ul class="list-disc pl-6 my-2">
                                                <li>20 dagen voor aanvang van de Reis bij een Reis van 6 dagen of meer.
                                                </li>
                                                <li>7 dagen voor aanvang van de Reis bij een Reis van 2 tot 6 dagen.
                                                </li>
                                                <li>48 uur voor aanvang van de Reis bij een Reis van minder dan 2 dagen.
                                                </li>
                                            </ul>
                                        </li>
                                        <li>Als gevolg van Onvermijdbare en buitengewone omstandigheden (overmacht) In de bovenstaande gevallen betaalt de Organisator reeds
                                        ontvangen
                                        bedragen onverwijld en uiterlijk binnen 14 dagen terug. Niet vergoed worden
                                        kosten
                                        die
                                        door de Reiziger zijn gemaakt voor diensten die buiten de Overeenkomst vallen
                                        zoals
                                        inentingen, visa, aanschaf materiaal, verzekeringen en indien niet bij de Reis
                                        inbegrepen de vliegreis, tickets, accommodatie, e.d..</li>
                                    </ol>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">14.2. Opzegging door Schuld
                                            Reiziger</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">Ingeval de Reiziger niet voldoet aan vooraf gestelde
                                        deelname-eisen
                                        die
                                        essentieel zijn voor de uitvoering van de Reis, of indien door of namens de
                                        Reiziger
                                        onjuiste of onvolledige informatie wordt opgegeven over ervaring, vaardigheden,
                                        lichamelijke of geestelijke gesteldheid of andere relevante onderwerpen die de
                                        uitvoering van de Reis of de veiligheid in gevaar brengen, heeft de Organisator
                                        het
                                        recht de Overeenkomst te beëindigen. De hieruit voortvloeiende schade en kosten
                                        komen
                                        voor rekening van de Reiziger. Dit laat andere rechten van de Organisator
                                        onverlet.
                                    </p>
                                </section>

                                <section id="article-15" class="scroll-mt-[150px]">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">
                                        Artikel 15 –
                                        Uitvoering
                                        van de reis en conformiteit</h2>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">15.1. Verantwoordelijkheid
                                            van de
                                            Organisator</strong></h3>
                                    <p class="mb-4 pl-4">De Organisator is verantwoordelijk voor de goede uitvoering van
                                        de
                                        uit
                                        de Overeenkomst voortvloeiende verplichtingen. Onder de uit de Overeenkomst
                                        voortvloeiende verplichtingen worden mede verstaan de verwachtingen die de
                                        Reiziger
                                        in
                                        redelijkheid kon hebben op grond van de publicaties van de Organisator en de
                                        Overeenkomst. Deze verantwoordelijkheid geldt ongeacht of de Reisdiensten door
                                        de
                                        Organisator zelf of door een andere Reisdienstverlener worden uitgevoerd.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">15.2. Meldplicht bij
                                            Non-conformiteit</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">Indien de Reiziger meent dat de Reis niet conform de
                                        Overeenkomst
                                        wordt
                                        uitgevoerd, dient hij deze Non-conformiteit onverwijld te melden aan de
                                        Organisator
                                        of
                                        de betreffende dienstverlener ter plaatse. Het niet tijdig melden kan van
                                        invloed
                                        zijn
                                        op uw rechten.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">15.3. Oplossen
                                            Non-conformiteit</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">De Organisator draagt er zorg voor dat de gemelde
                                        Non-conformiteit
                                        wordt verholpen, tenzij dit onmogelijk is of dit onevenredig hoge kosten met
                                        zich
                                        meebrengt rekening houdend met de mate van Non-conformiteit en de waarde van de
                                        betreffende Reisdiensten.</p>
                                </section>

                                <section id="article-16" class="scroll-mt-[150px]">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">
                                        Artikel 16 – Hulp en
                                        bijstand</h2>
                                    <h3 id="article-16.1" class="mb-2 scroll-mt-[150px]"><strong class="text-lg font-semibold">16.1. Verplichte
                                            bijstand</strong></h3>
                                    <p class="mb-4 pl-4">De Organisator verleent de Reiziger onverwijld hulp en bijstand
                                        indien
                                        de Reiziger tijdens de Reis in moeilijkheden verkeert. Dit omvat in het
                                        bijzonder
                                        goede
                                        informatie verstrekken over medische diensten, plaatselijke autoriteiten en
                                        consulaire
                                        bijstand en de Reiziger helpen bij het gebruik van communicatie op afstand en
                                        bij
                                        het
                                        vinden van alternatieve reisarrangementen.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">16.2. Kosten</strong></h3>
                                    <p class="mb-4 pl-4">De Organisator kan een redelijke vergoeding in rekening brengen
                                        voor de
                                        verleende hulp en bijstand indien de moeilijkheden zijn ontstaan door opzet of
                                        nalatigheid van de Reiziger. De hoogte van deze vergoeding bedraagt nooit meer
                                        dan
                                        de
                                        werkelijk gemaakte kosten.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">16.3. Contact in geval van
                                            nood</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">In geval van nood of indien u in moeilijkheden verkeert tijdens
                                        de
                                        Reis, dient u onverwijld contact op te nemen met de Organisator via het
                                        opgegeven
                                        noodnummer of de verstrekte contactgegevens. De Organisator zal u vervolgens de
                                        nodige
                                        hulp en bijstand verlenen conform <a href="#article-16.1" class="hover:text-blue-800 underline">16.1 - Verplichte bijstand</a>.</p>
                                </section>

                                <section id="article-17" class="scroll-mt-[150px]">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">
                                        Artikel 17 –
                                        Aansprakelijkheid en Onvermijdbare Omstandigheden</h2>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">17.1. Aansprakelijkheid
                                            Organisator</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">De Organisator is aansprakelijk voor de goede uitvoering van
                                        alle
                                        in de
                                        Overeenkomst opgenomen Reisdiensten, overeenkomstig de verwachtingen die de
                                        Reiziger
                                        op
                                        grond van de Overeenkomst redelijkerwijs mocht hebben. Dit geldt ongeacht of
                                        deze
                                        diensten door de Organisator zelf of door andere ingeschakelde
                                        Reisdienstverleners
                                        worden uitgevoerd.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">17.2. Uitsluitingen van
                                            aansprakelijkheid</strong></h3>
                                    <p class="mb-4 pl-4">De Organisator is niet aansprakelijk voor schade die het gevolg
                                        is
                                        van
                                        een tekortkoming in de uitvoering van de Overeenkomst indien deze tekortkoming
                                        te
                                        wijten
                                        is aan:</p>
                                    <ol class="list-[lower-alpha] pl-8 mb-4">
                                        <li>De Reiziger zelf;</li>
                                        <li>Een derde die niet direct betrokken is bij de uitvoering van de Reisdiensten
                                            opgenomen in de Overeenkomst, en wiens handelen onvoorzienbaar en
                                            onvermijdelijk
                                            was;</li>
                                        <li>Onvermijdbare en Buitengewone Omstandigheden.</li>
                                    </ol>
                                    <h3 id="article-17.3" class="mb-2 scroll-mt-[150px]"><strong class="text-lg font-semibold">17.3. Beperking van
                                            aansprakelijkheid</strong>
                                    </h3>
                                    <ol class="list-[lower-alpha] pl-8 mb-4">
                                        <li>De aansprakelijkheid van de Organisator voor schade die de Reiziger
                                            lijdt, kan beperkt of uitgesloten zijn overeenkomstig internationale
                                            verdragen
                                            en/of
                                            EU-verordeningen die van toepassing zijn op de door de Organisator
                                            ingeschakelde
                                            Reisdienstverleners. De Organisator kan zich jegens de Reiziger op deze
                                            beperkingen
                                            beroepen.</li>
                                        <li>Indien de Organisator aansprakelijk is voor schade anders dan
                                            veroorzaakt
                                            door
                                            overlijden of persoonlijk letsel van de Reiziger, of schade veroorzaakt door
                                            opzettelijk
                                            of nalatig handelen van de Organisator (inclusief diens personeel of
                                            ingeschakelde
                                            dienstverleners), en voor zover geen internationale verdragen of
                                            EU-verordeningen
                                            (zoals
                                            genoemd onder a) een lagere limiet bepalen, is de totale aansprakelijkheid
                                            van de Organisator voor alle schade per Reiziger beperkt tot maximaal driemaal (3x)
                                            de totale reissom.
                                        </li>
                                    </ol>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">17.4. Vergoeding gederfd
                                            reisgenot</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">Voor schade bestaande uit derving van reisgenot (immateriële
                                        schade)
                                        als gevolg van een tekortkoming in de uitvoering van de Overeenkomst waarvoor de
                                        Organisator aansprakelijkheid is, bedraagt de vergoeding maximaal éénmaal (1x)
                                        de
                                        reissom per Reiziger. Deze beperking geldt binnen de totale
                                        aansprakelijkheidslimiet
                                        zoals genoemd in <a href="#article-17.3" class="hover:text-blue-800 underline">17.3.b</a>. Schade die de Reiziger lijdt in de uitoefening van zijn
                                        beroep
                                        of bedrijf komt niet voor vergoeding in aanmerking.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">17.5. Werking ten behoeve van
                                            hulppersonen</strong></h3>
                                    <p class="mb-4 pl-4">De in dit artikel opgenomen uitsluitingen en beperkingen van de
                                        aansprakelijkheid van de Organisator gelden ook ten behoeve van werknemers van
                                        de
                                        Organisator, de door de Organisator ingeschakelde dienstverleners en hun
                                        personeel,
                                        tenzij de wet of een toepasselijk internationaal verdrag dwingend anders
                                        bepaalt.
                                    </p>
                                </section>

                                <section id="article-18" class="scroll-mt-[150px]">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">
                                        Artikel 18 – Bagage
                                        en
                                        Waardevolle Spullen</h2>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">18.1.
                                            Verantwoordelijkheid</strong></h3>
                                    <p class="mb-4 pl-4">De Reiziger is zelf verantwoordelijk voor zijn bagage en
                                        persoonlijke
                                        bezittingen gedurende de gehele Reis. Dit omvat onder meer toezicht houden op
                                        bagage
                                        tijdens transport, overstappen en verblijfplaatsen.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">18.2.
                                            Aansprakelijkheid</strong></h3>
                                    <p class="mb-4 pl-4">De aansprakelijkheid van de Organisator voor verlies, diefstal
                                        van
                                        of
                                        schade aan bagage en persoonlijke bezittingen is beperkt. Deze beperking geldt
                                        Overeenkomstig de internationale verdragen en/of EU-verordeningen die van
                                        toepassing
                                        zijn op de door de Organisator ingeschakelde dienstverleners (zoals
                                        vervoerders).
                                        Voor
                                        zover geen dergelijke verdragen of verordeningen van toepassing zijn, is de
                                        aansprakelijkheid van de Organisator beperkt tot maximaal driemaal (3x) de
                                        reissom
                                        per
                                        Reiziger, tenzij de schade is veroorzaakt door opzet of grove schuld van de
                                        Organisator.
                                    </p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">18.3. Waardevolle
                                            spullen</strong></h3>
                                    <p class="mb-4 pl-4">De Organisator is niet aansprakelijk voor verlies, diefstal van
                                        of
                                        schade aan waardevolle spullen, zoals geld, sieraden, elektronica, documenten en
                                        andere
                                        kostbaarheden, die zich in de ingecheckte bagage bevinden of die niet onder uw
                                        directe
                                        toezicht staan. De Reiziger wordt dringend geadviseerd om waardevolle spullen
                                        altijd
                                        in
                                        de handbagage te bewaren en onder persoonlijk toezicht te houden.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">18.4.
                                            Reisverzekering</strong></h3>
                                    <p class="mb-4 pl-4">De Reiziger wordt dringend geadviseerd om een adequate
                                        reisverzekering
                                        af te sluiten die dekking biedt voor onder andere verlies, diefstal van of
                                        schade
                                        aan
                                        bagage en persoonlijke bezittingen. Kosten die gedekt zijn door een dergelijke
                                        verzekering komen niet voor rekening van de Organisator.</p>
                                </section>

                                <section id="article-19" class="scroll-mt-[150px]">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">
                                        Artikel 19 –
                                        Verplichtingen Reiziger</h2>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">19.1. Gedrag en opvolging
                                            aanwijzingen</strong></h3>
                                    <p class="mb-4 pl-4">De Reiziger dient zich als een redelijk handelend Reiziger te
                                        gedragen.
                                        Hij is verplicht alle aanwijzingen ter bevordering van een goede uitvoering van
                                        de
                                        Reis
                                        door de Organisatie en de Reisdienstverleners op te volgen.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">19.2. Aansprakelijkheid
                                            Reiziger voor
                                            schade</strong></h3>
                                    <p class="mb-4 pl-4">De Reiziger is aansprakelijk voor schade veroorzaakt door zijn
                                        gedrag,
                                        het niet nakomen van deze verplichtingen of schade die anderszins aan hem moet
                                        worden
                                        toegerekend. De Reiziger vrijwaart de Organisator van aanspraken van bij de Reis
                                        betrokken Reisdienstverleners of derden voor schade die door de Reiziger is
                                        veroorzaakt
                                        of aan hem moet worden toegerekend.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">19.3. Uitsluiting van
                                            deelname</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">Indien de Reiziger zodanige hinder of last veroorzaakt
                                        (inclusief
                                        wangedrag) dat daardoor de uitvoering van de reis in sterke mate wordt
                                        bemoeilijkt
                                        of de
                                        veiligheid van anderen in gevaar komt, kan hij door de Organisator of haar
                                        Reisdienstverleners van verdere deelname aan de reis worden uitgesloten. De
                                        Reiziger
                                        heeft in een dergelijk geval geen recht op restitutie van gelden of (een
                                        gedeelte
                                        van)
                                        de reissom.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">19.4. Controleren tijden
                                            terugreis</strong>
                                    </h3>
                                    <p class="mb-4 pl-4">De Reiziger dient ten minste 24 uur voor aanvang van de
                                        geplande
                                        terugreis het exacte tijdstip van vertrek te verifiëren.</p>
                                </section>

                                <section id="article-20" class="scroll-mt-[150px]">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">
                                        Artikel 20 – Klachten
                                        en
                                        Geschillenbeslechting</h2>
                                    <h3 id="article-20.1" class="mb-2 scroll-mt-[150px]"><strong
                                            class="text-lg font-semibold">20.1 - Melden tijdens de Reis</strong></h3>
                                    <p class="mb-4 pl-4">Een geconstateerde tekortkoming in de uitvoering van de
                                        Overeenkomst
                                        (Non-conformiteit) dient zo spoedig mogelijk te worden gemeld bij de betrokken
                                        Reisdienstverlener zodat deze een passende oplossing kan treffen. Indien de
                                        Non-conformiteit niet binnen redelijke termijn wordt opgelost door de
                                        Reisdienstverlener
                                        en afbreuk doet aan de kwaliteit van de Reis, dient deze onverwijld en zo
                                        volledig
                                        mogelijk onderbouwd, gemeld te worden bij de Organisator.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">20.2. Melding na de
                                            Reis</strong></h3>
                                    <p class="mb-4 pl-4">Als een Non-conformiteit niet bevredigend wordt opgelost
                                        tijdens de
                                        Reis, dient deze zo spoedig mogelijk, maar uiterlijk binnen twee maanden na het
                                        einde
                                        van de Reis Schriftelijk en gemotiveerd te worden ingediend bij de Organisator.
                                        De
                                        Organisator is gehouden binnen één maand na ontvangst van de klacht na
                                        terugkomst
                                        gemotiveerd te reageren.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">20.3. Gevolgen niet of niet
                                            tijdig
                                            melden</strong></h3>
                                    <p class="mb-4 pl-4">Het niet of niet tijdig melden van de Non-conformiteit
                                        overeenkomstig <a href="#article-20.1"
                                            class="hover:text-blue-800 underline">20.1 - Melden tijdens de Reis</a> -
                                        kan van invloed zijn op de hoogte van een
                                        eventuele
                                        prijsverlaging of schadevergoeding, tenzij de belangen van de Organisator door
                                        het
                                        niet
                                        tijdig klagen niet geschaad zijn. Klachten die na terugkomst niet tijdig zijn
                                        ontvangen
                                        conform de wettelijke termijn van twee maanden na het einde van de Reis, worden
                                        niet
                                        in
                                        behandeling genomen, tenzij dit in de omstandigheden van het geval niet redelijk
                                        is.
                                    </p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">20.4. Compensatie bij
                                            vertraging/comfortverlies treinreizen</strong></h3>
                                    <p class="mb-4 pl-4">Bij vertragingen van treinen (meestal vanaf 60 minuten) en/of
                                        vermindering van comfort (bijvoorbeeld missende rijtuigen, of vervangend, minder
                                        comfortabel rijtuig) heeft de Reiziger conform Europese regelgeving
                                        passagiersrechten
                                        ten opzichte van de betreffende spoorwegmaatschappij. De Reiziger heeft het
                                        recht om
                                        compensatie voor deze specifieke tekortkomingen direct bij de uitvoerende
                                        spoorwegmaatschappij aan te vragen conform de geldende
                                        passagiersrechtenverordening.
                                        De
                                        spoorwegmaatschappij die het ticket heeft uitgegeven staat op het ticket
                                        vermeld.
                                        Bij
                                        reizen met een Interrail pas gelden specifieke compensatieregels. Onverminderd
                                        de
                                        mogelijkheid om compensatie te verkrijgen van de spoorwegmaatschappij, blijft de
                                        Organisator als aanbieder van de Pakketreis het centrale aanspreekpunt voor de
                                        Reiziger
                                        en verantwoordelijk voor de goede uitvoering van de gehele Overeenkomst. Indien
                                        de
                                        tekortkoming met betrekking tot het treinvervoer niet (volledig) wordt opgelost
                                        door
                                        de
                                        spoorwegmaatschappij en deze tekortkoming significant afbreuk doet aan de
                                        kwaliteit
                                        van
                                        de totale reis, kan de Reiziger hiervoor een beroep doen op de Organisator via
                                        de
                                        klachtenprocedure zoals beschreven in <a href="#article-20"
                                            class="hover:text-blue-800 underline">dit Artikel 20 – Klachten en
                                            Geschillenbeslechting</a>. De eventuele compensatie die de Reiziger reeds
                                        van de
                                        spoorwegmaatschappij heeft ontvangen, zal hierbij in mindering worden gebracht
                                        op
                                        een
                                        eventuele vergoeding door de Organisator.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">20.5. Gang naar de
                                            rechter</strong></h3>
                                    <p class="mb-4 pl-4">Indien een geschil niet in onderling overleg kan worden
                                        opgelost,
                                        staat
                                        voor zowel de Reiziger als de Organisator de gang naar de bevoegde rechter open.
                                        Op
                                        de
                                        Overeenkomst is Nederlands recht van toepassing, zoals vermeld in <a
                                            href="#article-21.4" class="hover:text-blue-800 underline">21.4 -
                                            Toepasselijk recht</a> van deze Voorwaarden.</p>
                                </section>

                                <section id="article-21" class="scroll-mt-[150px]">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">
                                        Artikel 21 – Overige
                                        bepalingen</h2>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">21.1. Rechten van
                                            derden</strong></h3>
                                    <p class="mb-4 pl-4">Ondergeschikten, hulppersonen en andere bij de uitvoering van
                                        de
                                        Overeenkomst betrokken derden (zoals Reisdienstverleners en hun personeel)
                                        kunnen
                                        zich
                                        jegens de Reiziger beroepen op de bepalingen uit de Overeenkomst en deze
                                        Voorwaarden
                                        (met inbegrip van de aansprakelijkheidsuitsluitingen en -beperkingen).</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">21.2. Vervangende
                                            bepalingen</strong></h3>
                                    <p class="mb-4 pl-4">Indien dwingend recht aan de geldigheid van een bepaling in
                                        deze
                                        Voorwaarden in de weg staat of indien een bepaling wordt vernietigd, wordt die
                                        bepaling
                                        geacht te zijn geconverteerd naar een geldige bepaling die qua inhoud en
                                        strekking
                                        zo
                                        dicht mogelijk de oorspronkelijke intentie benadert.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">21.3. Verjaring</strong></h3>
                                    <p class="mb-4 pl-4">Iedere aanspraak van de Reiziger op vergoeding van schade
                                        verjaart
                                        twee
                                        jaar nadat de Reis heeft plaats gevonden of indien de reis geen doorgang vond
                                        twee
                                        jaar
                                        na de geplande datum van aanvang.</p>
                                    <h3 id="article-21.4" class="mb-2 scroll-mt-[150px]"><strong
                                            class="text-lg font-semibold">21.4. Toepasselijk
                                            recht</strong></h3>
                                    <p class="mb-4 pl-4">Op het aanbod, de Overeenkomst en de uitvoering van de
                                        Overeenkomst
                                        is
                                        uitsluitend Nederlands recht van toepassing, tenzij dit in strijd is met
                                        dwingend
                                        recht.
                                        Onverminderd deze rechtskeuze komt een consument de bescherming toe die het
                                        dwingend
                                        recht van het land van zijn woonplaats hem biedt indien de Organisator de
                                        commerciële
                                        activiteiten (o.a. reclame) betreffende de overeengekomen Reis richt op het land
                                        waar de
                                        consument woonplaats heeft, tenzij de diensten niet geheel of gedeeltelijk in
                                        dat
                                        land
                                        worden verricht.</p>
                                    <h3 class="mb-2"><strong class="text-lg font-semibold">21.5. Foto's en informatie in
                                            publicaties</strong></h3>
                                    <p class="mb-4 pl-4">Foto's in publicaties (brochures, website, etc.) van de
                                        Organisator
                                        geven een algemene indruk van de reisbestemmingen en accommodaties. Er kan
                                        gebruik
                                        worden gemaakt van eigen foto's, foto's van derden (verkeersbureaus,
                                        spoorwegmaatschappijen, partners) en stockfoto's. De Organisator is
                                        verantwoordelijk
                                        voor de juistheid van de informatie over de reis die door of namens haar wordt
                                        opgenomen
                                        in eigen publicaties en die onderdeel uitmaakt van het aanbod. De Organisator
                                        kan
                                        niet
                                        gehouden worden aan de inhoud van voorlichtingsmateriaal dat uitsluitend is
                                        uitgegeven
                                        onder verantwoording van derden en niet door de Organisator als onderdeel van
                                        haar
                                        aanbod is gepresenteerd.</p>
                                </section>

                                <section id="article-22" class="scroll-mt-[150px]">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-blue-200 pb-2">
                                        Artikel 22 –
                                        Bijzondere
                                        wensen en vereisten</h2>
                                    <h3 id="article-22.1" class="mb-2 scroll-mt-[150px]"><strong
                                            class="text-lg font-semibold">22.1. Voorkeuren
                                            (wensen)</strong></h3>
                                    <p class="mb-4 pl-4">Indien de Reiziger bij de totstandkoming van de Overeenkomst
                                        bepaalde
                                        voorkeuren kenbaar maakt met betrekking tot de door de Organisator te leveren
                                        diensten
                                        (bv. ligging hotelkamer, zitplaats, dieetvoorkeur), zal met de opgegeven
                                        voorkeuren
                                        zoveel mogelijk rekening worden gehouden. Gezien de aard van bepaalde
                                        bestemmingen
                                        of
                                        reistypes, kunnen aan deze voorkeuren kunnen echter geen rechten worden
                                        ontleend,
                                        ondanks eventuele vermeldingen op Reisbescheiden of boekingsformulieren.</p>
                                    <h3 id="article-22.2" class="mb-2 scroll-mt-[150px]"><strong
                                            class="text-lg font-semibold">22.2. Vereisten</strong></h3>
                                    <p class="mb-4 pl-4">Indien de Reiziger uiterlijk bij het aangaan van de
                                        Overeenkomst
                                        specifieke vereisten kenbaar maakt die essentieel zijn voor deelname aan de
                                        Reis,
                                        bijvoorbeeld in verband met de medische gesteldheid of vanwege andere
                                        zwaarwegende
                                        belangen, dient de Reiziger dit expliciet als vereiste kenbaar te maken bij de
                                        boeking.
                                        Het voldoen aan dit vereiste geldt als een opschortende voorwaarde voor de
                                        definitieve
                                        totstandkoming van de Overeenkomst. De Organisator zal dit vereiste binnen een
                                        redelijke
                                        termijn (bijvoorbeeld 7 dagen, afhankelijk van de complexiteit van het vereiste)
                                        beoordelen en de Reiziger Schriftelijk berichten of aan het vereiste kan worden
                                        voldaan.
                                        Indien de Organisator het vereiste afwijst, komt de Overeenkomst niet tot stand
                                        en
                                        zijn
                                        er voor de Reiziger geen kosten verbonden aan de boeking. Bij bevestiging door
                                        de
                                        Organisator, komt de Overeenkomst definitief tot stand. Indien aan het vereiste
                                        meerkosten zijn verbonden en deze bekend zijn, zal de Organisator de Reiziger
                                        hiervan op
                                        de hoogte stellen en de Reiziger een nieuw, aangepast aanbod doen alvorens de
                                        Overeenkomst definitief tot stand komt.</p>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </section>
    </Layout>
</template>
