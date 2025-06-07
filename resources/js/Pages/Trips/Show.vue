<script setup>
import { Train, NightTrain, Euro, Clock, Directions, MapPin, Star, Calendar } from "@/Pages/Icons";
import { UsersIcon, ChevronRightIcon, PhoneIcon, EnvelopeIcon } from '@heroicons/vue/24/outline'
import Slider from "@/Pages/Layouts/Components/Slider.vue";
import Layout from '@/Pages/Layouts/Layout.vue';
import { ref } from 'vue'

const activeTab = ref('itinerary')
const props = defineProps({
    trip: Object,
    required: true

})
const tabs = [
    { id: 'itinerary', label: 'Dag tot dag' },
    { id: 'inclusive', label: 'Inclusief & Exclusief' },
    { id: 'practical', label: 'Praktische info' },
    { id: 'extra', label: 'Extra info' }
]
</script>

<template>
    <Layout>
        <div class="min-h-screen bg-gradient-to-br from-neutral-50 to-secondary-sage/10">
            <!-- Hero Section -->
            <section class="relative overflow-hidden">
                <!-- Background Image -->
                <div class="h-[80vh] lg:h-[100vh] relative">
                    <div class="absolute inset-0 bg-cover bg-center"
                        :style="`background-image: url(${trip.featured_image?.path})`"></div>
                    <!-- Overlay -->
                    <div class="absolute inset-0 ">
                    </div>

                    <!-- Hero Content -->
                    <div class="absolute bottom-2 left-0 right-0">
                        <div class="max-w-screen-desktop mx-auto">
                            <div
                                class="max-w-5xl p-4 lg:p-8 border border-neutral-25 rounded-3xl bg-primary-dark/30 backdrop -blur-[2px]">
                                <!-- Trip Title -->
                                <h1 class="text-3xl lg:text-6xl font-bold text-white mb-6 leading-tight">
                                    {{ trip.name }}
                                </h1>

                                <!-- Trip Meta Info -->
                                <div class="flex flex-wrap gap-6 text-neutral-25">
                                    <!-- Price -->
                                    <div
                                        class="flex items-center gap-2 bg-accent-gold px-4 py-2 rounded-full font-bold">
                                        <span class="text-lg">Vanaf €{{ trip.price }},-</span>
                                    </div>

                                    <!-- Duration -->
                                    <div
                                        class="flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full">
                                        <Clock class="w-5 h-5" />
                                        <span class="font-medium">{{ trip.duration }} dagen</span>
                                    </div>

                                    <!-- Transport -->
                                    <div
                                        class="flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full">
                                        <Train class="w-5 h-5" />
                                        <span class="font-medium">Duurzaam reizen</span>
                                    </div>

                                    <!-- Countries -->
                                    <div
                                        class="flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full">
                                        <MapPin class="w-5 h-5" />
                                        <span class="font-medium">{{ trip.countries_list }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main Content -->
            <div class="max-w-screen-desktop mx-auto px-6 py-12 lg:py-16">
                <div class="grid lg:grid-cols-3 gap-12">
                    <!-- Left Column - Main Content -->
                    <div class="lg:col-span-2 space-y-12">
                        <!-- Image Slider -->
                        <div class="bg-white rounded-2xl shadow-sm border border-secondary-sage/20 overflow-hidden">

                        </div>

                        <!-- Description & Highlights -->
                        <div class="bg-white rounded-2xl shadow-sm border border-secondary-sage/20 p-6 lg:p-8">
                            <div class="mb-8">
                                <div class="flex items-center justify-center gap-3 mb-6">
                                    <span class="w-12 h-0.5 bg-accent-gold"></span>
                                    <div class="w-3 h-3 bg-accent-gold rounded-full"></div>
                                    <span class="w-12 h-0.5 bg-accent-gold"></span>
                                </div>
                                <div class="p-6">
                                    <Slider :items="trip.images">
                                        <template #default="{ item, index }">
                                            <img class="w-full h-[150px] rounded-md object-cover" :src="item.path" :key="index">
                                        </template>
                                    </Slider>
                                </div>
                                <h2 class="text-2xl lg:text-3xl font-bold text-primary-dark mb-6">
                                    Over deze reis
                                </h2>
                                <p class="text-lg text-primary-default leading-relaxed">
                                    {{ trip.description }}
                                </p>
                            </div>

                            <!-- Highlights -->
                            <div class="border-t border-secondary-sage/20 pt-8">
                                <ul class="space-y-4">
                                    <li class="flex items-start gap-3">
                                        <span class="w-2 h-2 bg-secondary-sage rounded-full mt-2 flex-shrink-0"></span>
                                        <span class="text-primary-default">Romantische treinrit door de Alpen met
                                            adembenemende
                                            uitzichten</span>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span class="w-2 h-2 bg-secondary-sage rounded-full mt-2 flex-shrink-0"></span>
                                        <span class="text-primary-default">Bezoek aan historische kastelen en UNESCO
                                            werelderfgoed
                                            locaties</span>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span class="w-2 h-2 bg-secondary-sage rounded-full mt-2 flex-shrink-0"></span>
                                        <span class="text-primary-default">Lokale culinaire ervaringen en
                                            wijnproeverijen</span>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span class="w-2 h-2 bg-secondary-sage rounded-full mt-2 flex-shrink-0"></span>
                                        <span class="text-primary-default">Kleinschalige groepen voor een persoonlijke
                                            ervaring</span>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span class="w-2 h-2 bg-secondary-sage rounded-full mt-2 flex-shrink-0"></span>
                                        <span class="text-primary-default">Duurzaam reizen zonder vliegtuig</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Tabs Section -->
                        <div class="bg-white rounded-2xl shadow-sm border border-secondary-sage/20 overflow-hidden">
                            <!-- Tab Headers -->
                            <div class="border-b border-secondary-sage/20">
                                <nav class="flex overflow-x-auto">
                                    <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id" :class="[
                                        'flex-1 px-6 py-4 text-center font-medium whitespace-nowrap transition-colors',
                                        activeTab === tab.id
                                            ? 'text-primary-dark bg-accent-earth/10 border-b-2 border-accent-gold'
                                            : 'text-secondary-stone hover:text-primary-dark hover:bg-neutral-50'
                                    ]">
                                        {{ tab.label }}
                                    </button>
                                </nav>
                            </div>

                            <!-- Tab Content -->
                            <div class="p-6 lg:p-8">
                                <!-- Dag tot dag Tab -->
                                <div v-if="activeTab === 'itinerary'" class="space-y-6">
                                    <div class="text-center py-12">
                                        <Calendar class="w-16 h-16 text-secondary-sage mx-auto mb-4" />
                                        <h3 class="text-xl font-semibold text-primary-dark mb-2">
                                            Dag tot dag programma
                                        </h3>
                                        <p class="text-secondary-stone">
                                            Gedetailleerd reisprogramma wordt binnenkort toegevoegd
                                        </p>
                                    </div>
                                </div>
                                <div v-else-if="activeTab === 'inclusive'" class="space-y-6">
                                    <p>Inbegrepen en niet inbegrepen informatie komt hier.</p>
                                </div>

                                <div v-else-if="activeTab === 'practical'" class="space-y-6">
                                    <p>Praktische informatie over de reis komt hier.</p>
                                </div>

                                <div v-else-if="activeTab === 'extra'" class="space-y-6">
                                    <p>Extra informatie over deze reis komt hier.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Booking Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="sticky top-6 space-y-6">
                            <!-- Booking Card -->
                            <div class="bg-white rounded-2xl shadow-lg border border-secondary-sage/20 overflow-hidden">
                                <div class=" bg-accent-gold p-6">
                                    <h3 class="text-xl font-bold text-neutral-25 mb-2">
                                        Boek deze reis
                                    </h3>
                                    <p class="text-primary-dark/80">
                                        Vanaf €{{ trip.price }},- per persoon
                                    </p>
                                </div>

                                <div class="p-6 space-y-6">
                                    <!-- Quick Facts -->
                                    <div class="space-y-4">
                                        <div class="flex justify-between items-center">
                                            <span class="text-secondary-stone">Duur:</span>
                                            <span class="font-medium text-primary-dark">{{ trip.duration }} dagen</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-secondary-stone">Vervoer:</span>
                                            <span class="font-medium text-primary-dark flex items-center gap-1">
                                                <Train class="w-4 h-4" />
                                                Trein
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-secondary-stone">Groepsgrootte:</span>
                                            <span class="font-medium text-primary-dark flex items-center gap-1">
                                                <UsersIcon class="w-4 h-4" />
                                                Max. 16 personen
                                            </span>
                                        </div>
                                    </div>

                                    <div class="border-t border-secondary-sage/20 pt-6">
                                        <button
                                            class="w-full bg-accent-terracotta hover:bg-accent-terracotta/90 text-white font-semibold py-4 px-6 rounded-xl transition-colors duration-300 flex items-center justify-center gap-2 group">
                                            Boek nu
                                            <ChevronRightIcon
                                                class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
                                        </button>

                                        <p class="text-sm text-secondary-stone text-center mt-4">
                                            Of neem contact op voor meer informatie
                                        </p>

                                        <div class="flex gap-3 mt-4">
                                            <a href="tel:+3112345678"
                                                class="flex-1 bg-secondary-sage/10 hover:bg-secondary-sage/20 text-primary-dark font-medium py-2 px-4 rounded-lg transition-colors duration-300 flex items-center justify-center gap-2">
                                                <PhoneIcon class="w-4 h-4" />
                                                Bellen
                                            </a>
                                            <a href="mailto:info@example.com"
                                                class="flex-1 bg-secondary-sage/10 hover:bg-secondary-sage/20 text-primary-dark font-medium py-2 px-4 rounded-lg transition-colors duration-300 flex items-center justify-center gap-2">
                                                <EnvelopeIcon class="w-4 h-4" />
                                                E-mail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Trust Indicators -->
                            <div class="bg-white/50 backdrop-blur-sm rounded-xl p-6 border border-secondary-sage/20">
                                <h4 class="font-semibold text-primary-dark mb-4">Waarom kiezen voor ons?</h4>
                                <ul class="space-y-3 text-sm">
                                    <li class="flex items-center gap-2">
                                        <span class="w-2 h-2 bg-accent-gold rounded-full"></span>
                                        <span class="text-primary-default">25+ jaar reiservaring</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="w-2 h-2 bg-accent-gold rounded-full"></span>
                                        <span class="text-primary-default">100% duurzaam reizen</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="w-2 h-2 bg-accent-gold rounded-full"></span>
                                        <span class="text-primary-default">Kleinschalig & persoonlijk</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>
