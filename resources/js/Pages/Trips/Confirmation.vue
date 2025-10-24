<script setup>
import { Link } from '@inertiajs/vue3'
const props = defineProps({
    booking: {
        type: Object,
        required: true
    }
})
</script>

<template>
    <Layout>
        <section class="min-h-screen bg-gradient-to-br from-neutral-50 to-secondary-sage/10 py-8 laptop:py-16">
            <div class="max-w-screen-laptop mx-auto px-4">
                <!-- Success Header -->
                <div class="text-center mb-8 laptop:mb-12">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 laptop:w-20 laptop:h-20 bg-status-success rounded-full mb-4 laptop:mb-6">
                        <CheckCircle class="w-8 h-8 laptop:w-10 laptop:h-10 text-white" />
                    </div>
                    <h1 class="text-2xl laptop:text-4xl desktop:text-5xl font-bold text-primary-dark mb-3 laptop:mb-4">
                        Boeking bevestigd!
                    </h1>
                    <p class="text-base laptop:text-lg text-primary-default max-w-2xl mx-auto leading-relaxed">
                        Bedankt voor uw boeking. We hebben uw reservering ontvangen en verwerkt.
                        U ontvangt binnen enkele ogenblikken een bevestigingsmail op <strong>{{ booking.contact.email
                            }}</strong>
                    </p>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 desktop:grid-cols-3 gap-6 laptop:gap-8">
                    <!-- Left Column: Booking Details (2 cols on desktop) -->
                    <div class="desktop:col-span-2 space-y-6">
                        <!-- Trip Details Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 overflow-hidden">
                            <!-- Trip Image Header -->
                            <div v-if="booking.product.featured_image"
                                class="h-48 laptop:h-64 bg-cover bg-center relative"
                                :style="`background-image: url(${booking.product.featured_image.path})`">
                                <div class="absolute inset-0 bg-gradient-to-t from-primary-dark/60 to-transparent">
                                </div>
                                <div class="absolute bottom-4 left-4 right-4">
                                    <h2 class="text-xl laptop:text-2xl font-bold text-white mb-1">
                                        {{ booking.product.name }}
                                    </h2>
                                    <div class="flex items-center gap-2 text-white/90">
                                        <MapPin class="w-4 h-4" />
                                        <span class="text-sm">{{ booking.product.countries_list }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Trip Info -->
                            <div class="p-6 laptop:p-8 space-y-6">
                                <div class="grid grid-cols-1 tablet:grid-cols-2 gap-4">
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-10 h-10 bg-accent-earth/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <Calendar class="w-5 h-5 text-accent-gold" />
                                        </div>
                                        <div>
                                            <p
                                                class="text-xs text-secondary-stone font-medium uppercase tracking-wide mb-1">
                                                Vertrekdatum</p>
                                            <p class="text-base laptop:text-lg font-semibold text-primary-dark">
                                                {{ booking.departure_date_formatted }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-10 h-10 bg-accent-earth/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <Clock class="w-5 h-5 text-accent-gold" />
                                        </div>
                                        <div>
                                            <p
                                                class="text-xs text-secondary-stone font-medium uppercase tracking-wide mb-1">
                                                Duur</p>
                                            <p class="text-base laptop:text-lg font-semibold text-primary-dark">
                                                {{ booking.product.duration }} dagen
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-10 h-10 bg-accent-earth/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <Train class="w-5 h-5 text-accent-gold" />
                                        </div>
                                        <div>
                                            <p
                                                class="text-xs text-secondary-stone font-medium uppercase tracking-wide mb-1">
                                                Vervoer</p>
                                            <p class="text-base laptop:text-lg font-semibold text-primary-dark">
                                                Treinreis
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-10 h-10 bg-accent-earth/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <UserAdd class="w-5 h-5 text-accent-gold" />
                                        </div>
                                        <div>
                                            <p
                                                class="text-xs text-secondary-stone font-medium uppercase tracking-wide mb-1">
                                                Reizigers</p>
                                            <p class="text-base laptop:text-lg font-semibold text-primary-dark">
                                                {{ booking.travelers.length }} {{ booking.travelers.length === 1 ?
                                                'persoon' : 'personen' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Travelers Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6 laptop:p-8">
                            <h3 class="text-lg laptop:text-xl font-bold text-primary-dark mb-4 flex items-center gap-2">
                                <UserAdd class="w-5 h-5 text-accent-gold" />
                                Reizigers
                            </h3>
                            <div class="space-y-3">
                                <div v-for="(traveler, index) in booking.travelers" :key="traveler.id"
                                    class="flex items-center justify-between p-4 bg-neutral-50 rounded-lg border border-neutral-200">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-secondary-sage/30 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-bold text-primary-dark">{{ index + 1 }}</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-primary-dark">{{ traveler.full_name }}</p>
                                            <p class="text-sm text-secondary-stone">{{ traveler.birthdate_formatted }}
                                            </p>
                                        </div>
                                    </div>
                                    <div v-if="traveler.id === booking.main_booker_id"
                                        class="px-3 py-1 bg-accent-gold/20 rounded-full">
                                        <span class="text-xs font-medium text-accent-gold">Hoofdboeker</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Summary & Contact -->
                    <div class="space-y-6">
                        <!-- Booking Reference Card -->
                        <div class="bg-accent-earth/10 border-2 border-accent-earth rounded-xl p-6">
                            <p class="text-xs text-primary-default font-medium uppercase tracking-wide mb-2">Uw
                                boekingsnummer</p>
                            <p class="text-2xl laptop:text-3xl font-bold text-primary-dark mb-3">
                                {{ booking.reference }}
                            </p>
                            <p class="text-sm text-primary-default">
                                Bewaar dit nummer voor uw administratie en eventuele correspondentie.
                            </p>
                        </div>

                        <!-- Contact Details Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6">
                            <h3 class="text-lg font-bold text-primary-dark mb-4">Contactgegevens</h3>
                            <div class="space-y-3 text-sm">
                                <div>
                                    <p class="text-xs text-secondary-stone font-medium uppercase tracking-wide mb-1">
                                        Naam</p>
                                    <p class="text-primary-dark">{{ booking.contact.name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-secondary-stone font-medium uppercase tracking-wide mb-1">
                                        E-mailadres</p>
                                    <p class="text-primary-dark break-all">{{ booking.contact.email }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-secondary-stone font-medium uppercase tracking-wide mb-1">
                                        Telefoonnummer</p>
                                    <p class="text-primary-dark">{{ booking.contact.phone }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-secondary-stone font-medium uppercase tracking-wide mb-1">
                                        Adres</p>
                                    <p class="text-primary-dark whitespace-pre-line">{{ booking.contact.address }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Next Steps Card -->
                        <div class="bg-secondary-sage/10 border border-secondary-sage/30 rounded-xl p-6">
                            <h3 class="text-lg font-bold text-primary-dark mb-3 flex items-center gap-2">
                                <Leaf class="w-5 h-5 text-secondary-sage" />
                                Volgende stappen
                            </h3>
                            <ul class="space-y-3 text-sm text-primary-default">
                                <li class="flex items-start gap-2">
                                    <span class="w-1.5 h-1.5 bg-secondary-sage rounded-full mt-2 flex-shrink-0"></span>
                                    <span>U ontvangt een bevestigingsmail met alle details van uw reis</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="w-1.5 h-1.5 bg-secondary-sage rounded-full mt-2 flex-shrink-0"></span>
                                    <span>We nemen binnenkort contact met u op voor de definitieve bevestiging</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="w-1.5 h-1.5 bg-secondary-sage rounded-full mt-2 flex-shrink-0"></span>
                                    <span>Heeft u vragen? Neem gerust contact met ons op</span>
                                </li>
                            </ul>
                        </div>
                            <div class="flex gap-3 mt-4">
                                <CallButton />
                                <MailButton />
                            </div>
                    </div>
                </div>

                <!-- Bottom Action -->
                <div class="mt-8 laptop:mt-12 text-center">
                    <Link :href="route('home')" as="a"
                        class="inline-flex items-center gap-2 bg-primary-default hover:bg-primary-dark text-white px-6 laptop:px-8 py-3 laptop:py-4 rounded-lg font-medium text-base laptop:text-lg transition-colors duration-200">
                        <Back class="w-5 h-5" />
                        Terug naar homepagina
                    </Link>
                </div>
            </div>
        </section>
    </Layout>
</template>
