<script setup>
import { Euro, FileText, CreditCard, Wallet, PhoneCall, ShieldCheck } from 'lucide-vue-next';
</script>

<template>
    <div class="space-y-6">
        <!-- Prijs & Offerte -->
        <div class="bg-white rounded-lg border border-accent-primary/20 p-4 tablet:p-6">
            <div class="flex items-start gap-3 mb-3">
                <div class="flex-shrink-0 mt-1">
                    <Euro class="w-5 h-5 text-accent-primary" />
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-base tablet:text-lg font-semibold text-brand-primary mb-2">
                        Prijzen & Offerte
                    </h4>
                    <p class="text-sm tablet:text-base text-accent-text leading-relaxed">
                        De vermelde prijzen zijn indicatieve prijzen per persoon. Voor een nauwkeurige berekening
                        op basis van jouw reisdata en gezelschap, vraag je een vrijblijvend reisvoorstel aan.
                        Boek tijdig voor de beste prijzen, vooral tijdens vakantieperiodes.
                    </p>
                </div>
            </div>
        </div>

        <!-- Bestelproces -->
        <div class="bg-white rounded-lg border border-accent-primary/20 p-4 tablet:p-6">
            <div class="flex items-start gap-3 mb-3">
                <div class="flex-shrink-0 mt-1">
                    <FileText class="w-5 h-5 text-accent-primary" />
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-base tablet:text-lg font-semibold text-brand-primary mb-2">
                        Hoe verloopt de boeking?
                    </h4>
                    <div class="space-y-3 text-sm tablet:text-base text-accent-text leading-relaxed">
                        <div class="flex items-start gap-2">
                            <span class="w-6 h-6 bg-accent-earth text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">1</span>
                            <p>Dien een aanvraag in via het boekingsformulier op deze pagina</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-6 h-6 bg-accent-earth text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">2</span>
                            <p>Ontvang binnen enkele dagen een op maat gemaakt reisvoorstel</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-6 h-6 bg-accent-earth text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">3</span>
                            <p>Onderteken de bestelbon en betaal het voorschot bij akkoord</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-6 h-6 bg-accent-earth text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">4</span>
                            <p>Je reis is bevestigd en je ontvangt de reisdocumenten</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Betalingsvoorwaarden -->
        <div class="bg-white rounded-lg border border-accent-primary/20 p-4 tablet:p-6">
            <div class="flex items-start gap-3 mb-3">
                <div class="flex-shrink-0 mt-1">
                    <CreditCard class="w-5 h-5 text-accent-primary" />
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-base tablet:text-lg font-semibold text-brand-primary mb-2">
                        Betalingsvoorwaarden
                    </h4>
                    <p class="text-sm tablet:text-base text-accent-text leading-relaxed">
                        Bij bevestiging betaal je een voorschot. Het resterende bedrag is verschuldigd
                        twee maanden voor vertrek. De hoogte van het voorschot verschilt per reis en
                        staat vermeld in je persoonlijke reisvoorstel.
                    </p>
                </div>
            </div>
        </div>

        <!-- Betalingsmogelijkheden -->
        <div class="bg-white rounded-lg border border-accent-primary/20 p-4 tablet:p-6">
            <div class="flex items-start gap-3 mb-3">
                <div class="flex-shrink-0 mt-1">
                    <Wallet class="w-5 h-5 text-accent-primary" />
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-base tablet:text-lg font-semibold text-brand-primary mb-2">
                        Betaalmethoden
                    </h4>
                    <p class="text-sm tablet:text-base text-accent-text leading-relaxed">
                        Je kunt betalen via bankoverschrijving of met je creditcard.
                    </p>
                </div>
            </div>
        </div>

        <!-- Hulp onderweg -->
        <div class="bg-white rounded-lg border border-accent-primary/20 p-4 tablet:p-6">
            <div class="flex items-start gap-3 mb-3">
                <div class="flex-shrink-0 mt-1">
                    <PhoneCall class="w-5 h-5 text-accent-primary" />
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-base tablet:text-lg font-semibold text-brand-primary mb-2">
                        Hulp onderweg
                    </h4>
                    <p class="text-sm tablet:text-base text-accent-text leading-relaxed">
                        Bij je reisdocumenten ontvang je ons 24/7 bereikbare noodnummer. Hiermee kun je
                        ons altijd bereiken bij problemen of vragen tijdens je reis, ook buiten kantooruren
                        en in het weekend.
                    </p>
                </div>
            </div>
        </div>

        <!-- Betalingsgarantie -->
        <div class="bg-accent-sage/10 rounded-lg border border-accent-sage/30 p-4 tablet:p-6">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 mt-1">
                    <ShieldCheck class="w-5 h-5 text-accent-sage" />
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-base tablet:text-lg font-semibold text-brand-primary mb-2">
                        Betalingsgarantie via STO Garant
                    </h4>
                    <p class="text-sm tablet:text-base text-accent-text leading-relaxed mb-3">
                        Al onze reizen zijn verzekerd via STO Garant, een garantieregeling die voldoet
                        aan de Europese Richtlijn Pakketreizen. Dit beschermt jouw betalingen.
                    </p>
                    <p class="text-sm tablet:text-base text-accent-text leading-relaxed">
                        Via het derdengeldenprincipe wordt jouw betaling veilig beheerd op een aparte
                        rekening tot de reis is voltooid. Pas daarna wordt het bedrag vrijgegeven.
                        Zo ben je verzekerd van een zorgeloze boeking.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
