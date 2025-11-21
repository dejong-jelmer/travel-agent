<script setup>
import { Link } from '@inertiajs/vue3'
import { CircleCheckBig } from 'lucide-vue-next'

const props = defineProps({
    booking: {
        type: Object,
        required: true
    }
})
</script>

<template>
    <Layout>
        <section class="min-h-screen py-12 laptop:py-16">
            <div class="max-w-screen-laptop mx-auto px-4">
                <!-- Success Header -->
                <div class="text-left mb-8 laptop:mb-12">
                    <div class="inline-flex gap-4">
                        <div
                            class="inline-flex items-center justify-center w-8 h-8 laptop:w-12 laptop:h-12 bg-status-success rounded-full mb-4 laptop:mb-6">
                            <CircleCheckBig class="w-4 h-4 laptop:w-8 laptop:h-8 text-white" />
                        </div>
                        <h1
                            class="text-2xl laptop:text-4xl desktop:text-5xl font-bold text-brand-primary mb-3 laptop:mb-4">
                            Boeking ontvangen!
                        </h1>
                    </div>
                    <p
                        class="text-left text-base laptop:text-lg text-brand-primary max-w-xl laptop:max-w-2xl leading-relaxed">
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
                        <div class="bg-white rounded-xl shadow-sm border border-brand-primary/20 overflow-hidden">
                            <!-- Trip Image Header -->
                            <div v-if="booking.trip.featured_image"
                                class="h-48 laptop:h-64 bg-cover bg-center relative"
                                :style="`background-image: url(${booking.trip.featured_image.full_path})`">
                                <div class="absolute inset-0 bg-gradient-to-t from-brand-primary/50 to-transparent">
                                </div>
                                <div class="absolute bottom-4 left-4 right-4">
                                    <h2 class="text-xl laptop:text-2xl font-bold text-white mb-1">
                                        {{ booking.trip.name }}
                                    </h2>
                                    <div class="flex items-center gap-2 text-white/90">
                                        <MapPin class="w-4 h-4" />
                                        <span class="text-sm">{{ booking.trip.countries_list }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Trip Info -->
                            <div class="p-6 laptop:p-8 space-y-6">
                                <div class="grid grid-cols-1 tablet:grid-cols-2 gap-4">
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-10 h-10 bg-brand-secondary rounded-lg flex items-center justify-center flex-shrink-0">
                                            <Calendar class="w-5 h-5 text-accent-primary" />
                                        </div>
                                        <div>
                                            <p
                                                class="text-xs text-brand-light font-medium uppercase tracking-wide mb-1">
                                                Vertrekdatum</p>
                                            <p class="text-base laptop:text-lg font-semibold text-brand-primary">
                                                {{ booking.departure_date_formatted }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-10 h-10 bg-brand-secondary rounded-lg flex items-center justify-center flex-shrink-0">
                                            <Clock class="w-5 h-5 text-accent-primary" />
                                        </div>
                                        <div>
                                            <p
                                                class="text-xs text-brand-light font-medium uppercase tracking-wide mb-1">
                                                Duur</p>
                                            <p class="text-base laptop:text-lg font-semibold text-brand-primary">
                                                {{ booking.trip.duration }} dagen
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-10 h-10 bg-brand-secondary rounded-lg flex items-center justify-center flex-shrink-0">
                                            <Train class="w-5 h-5 text-accent-primary" />
                                        </div>
                                        <div>
                                            <p
                                                class="text-xs text-brand-light font-medium uppercase tracking-wide mb-1">
                                                Vervoer</p>
                                            <p class="text-base laptop:text-lg font-semibold text-brand-primary">
                                                Treinreis
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-10 h-10 bg-brand-secondary rounded-lg flex items-center justify-center flex-shrink-0">
                                            <UserAdd class="w-5 h-5 text-accent-primary" />
                                        </div>
                                        <div>
                                            <p
                                                class="text-xs text-brand-light font-medium uppercase tracking-wide mb-1">
                                                Reizigers</p>
                                            <p class="text-base laptop:text-lg font-semibold text-brand-primary">
                                                {{ booking.travelers.length }} {{ booking.travelers.length === 1 ?
                                                    'persoon' : 'personen' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Travelers Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-brand-primary/20 p-6 laptop:p-8">
                            <h3
                                class="text-lg laptop:text-xl font-bold text-brand-primary mb-4 flex items-center gap-2">
                                <UserAdd class="w-5 h-5 text-accent-primary" />
                                Reizigers
                            </h3>
                            <div class="space-y-3">
                                <div v-for="(traveler, index) in booking.travelers" :key="traveler.id"
                                    class="flex items-center justify-between p-4 bg-white rounded-lg border border-brand-light/20">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-accent-sage/30 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-bold text-brand-primary">{{ index + 1 }}</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-brand-primary">{{ traveler.full_name }}</p>
                                            <p class="text-sm text-brand-light">{{ traveler.birthdate_formatted }}
                                            </p>
                                        </div>
                                    </div>
                                    <Pill type="accent" v-if="traveler.id === booking.main_booker_id">Hoofdboeker</Pill>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Summary & Contact -->
                    <div class="space-y-6">
                        <!-- Booking Reference Card -->
                        <div class="bg-brand-secondary border-2 border-accent-primary rounded-xl p-6">
                            <p class="text-xs text-brand-primary font-medium uppercase tracking-wide mb-2">Uw
                                boekingsnummer</p>
                            <p class="text-2xl laptop:text-3xl font-bold text-accent-primary mb-3">
                                {{ booking.reference }}
                            </p>
                            <p class="text-sm text-brand-primary">
                                Bewaar dit nummer voor uw administratie en eventuele correspondentie.
                            </p>
                        </div>

                        <!-- Contact Details Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-brand-primary/20 p-6">
                            <h3 class="text-lg font-bold text-brand-primary mb-4">Contactgegevens</h3>
                            <div class="space-y-3 text-sm">
                                <div>
                                    <p class="text-xs text-brand-light font-medium uppercase tracking-wide mb-1">
                                        Naam</p>
                                    <p class="text-brand-primary">{{ booking.contact.name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-brand-light font-medium uppercase tracking-wide mb-1">
                                        E-mailadres</p>
                                    <p class="text-brand-primary break-all">{{ booking.contact.email }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-brand-light font-medium uppercase tracking-wide mb-1">
                                        Telefoonnummer</p>
                                    <p class="text-brand-primary">{{ booking.contact.phone }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-brand-light font-medium uppercase tracking-wide mb-1">
                                        Adres</p>
                                    <p class="text-brand-primary whitespace-pre-line">{{ booking.contact.address }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Next Steps Card -->
                        <div class="bg-accent-sage/10 border border-accent-sage/30 rounded-xl p-6">
                            <h3 class="text-lg font-bold text-brand-primary mb-3 flex items-center gap-2">
                                Volgende stappen
                            </h3>
                            <ul class="space-y-3 text-sm text-brand-primary">
                                <li class="flex items-start gap-2">
                                    <span class="w-1.5 h-1.5 bg-accent-sage rounded-full mt-2 flex-shrink-0"></span>
                                    <span>U ontvangt een bevestigingsmail met alle details van uw reis</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="w-1.5 h-1.5 bg-accent-sage rounded-full mt-2 flex-shrink-0"></span>
                                    <span>We nemen binnenkort contact met u op voor de definitieve bevestiging</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="w-1.5 h-1.5 bg-accent-sage rounded-full mt-2 flex-shrink-0"></span>
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
                    <Link :href="route('home')" as="a">
                    <Button>
                        Terug naar homepagina
                    </Button>
                    </Link>
                </div>
            </div>
        </section>
    </Layout>
</template>
