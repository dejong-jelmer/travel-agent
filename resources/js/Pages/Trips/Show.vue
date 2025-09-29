<script setup>
import { ref, toRef, watch } from 'vue'
import { UsersIcon, ChevronRightIcon, PhoneIcon, EnvelopeIcon } from '@heroicons/vue/24/outline'
import { useBooking } from '@/composables/useBooking'

const props = defineProps({
    trip: Object,
    required: true
})
// Tabs
const activeTab = ref('itinerary')
// Modal
const bookingModalOpen = ref(false)
// LigtBox
const lightboxRef = ref(null)
const openLightbox = (index) => {
    lightboxRef.value?.open(index)
}

// Booking data
const { booking, validator, markTouched, markDirty } = useBooking(props.trip)
const departure_date = toRef(booking, 'departure_date')
const participants = toRef(booking, 'participants')
watch(
    () => booking.hasErrors,
    (newValue) => {
        if (newValue) {
            bookingModalOpen.value = true
        }
    }
)

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
                <div class="h-[80vh] laptop:h-[100vh] relative">
                    <div class="absolute inset-0 bg-cover bg-center"
                        :style="`background-image: url(${trip.featured_image?.path})`"></div>
                    <!-- Overlay -->
                    <div class="absolute inset-0 ">
                    </div>

                    <!-- Hero Content -->
                    <div class="absolute bottom-2 left-0 right-0">
                        <div class="max-w-screen-desktop mx-auto">
                            <div
                                class="max-w-5xl p-4 mx-2 laptop:p-8 border border-neutral-25 rounded-3xl bg-primary-dark/30 backdrop -blur-[2px]">
                                <!-- Trip Title -->
                                <h1 class="text-3xl laptop:text-6xl font-bold text-white mb-6 leading-tight">
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
            <div class="max-w-screen-desktop mx-auto px-6 py-12 laptop:py-16">
                <div class="grid grid-cols-1 laptop:grid-cols-3 gap-12">
                    <!-- Left Column - Main Content -->
                    <div class="laptop:col-span-2 space-y-12">

                        <!-- Description & Highlights -->
                        <div class="bg-white rounded-2xl shadow-sm border border-secondary-sage/20 p-6 laptop:p-8">
                            <div class="mb-8">
                                <div class="w-full text-center">
                                    <SectionHeader>Over deze reis</SectionHeader>
                                </div>
                                <div class="p-6">
                                    <Slider :items="trip.images">
                                        <template #default="{ item, index }">
                                            <img :src="item.path"
                                                class="w-full h-[150px] rounded-md object-cover cursor-zoom-in"
                                                :key="index" @click="openLightbox(index)" />
                                        </template>
                                    </Slider>
                                    <LightBox ref="lightboxRef" :images="trip.images" />
                                </div>
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
                            <div class="p-6 laptop:p-8">
                                <div v-if="activeTab === 'itinerary'" class="space-y-6">
                                    <div class="text-left py-4">
                                        <div v-if="trip.itineraries?.length" class="text-left space-y-6">
                                            <template v-for="(itinerary, index) in trip.itineraries" :key="index">
                                                <TripItinerary :itinerary="itinerary" />
                                            </template>
                                        </div>
                                        <p v-else class="text-secondary-stone">
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
                    <div class="laptop:col-span-1">
                        <div class="sticky top-6 space-y-6">
                            <!-- Booking Card -->
                            <div class="bg-white rounded-2xl shadow-lg border border-secondary-sage/20 overflow-hidden">
                                <div class=" bg-accent-gold p-4">
                                    <h3 class="text-xl font-bold text-neutral-25">
                                        Reis overzicht
                                    </h3>

                                </div>

                                <div class="p-6 space-y-6">
                                    <!-- Quick Facts -->
                                    <div class="space-y-4">
                                        <div class="flex justify-between items-center">
                                            <span class="text-secondary-stone">Vanaf:</span>
                                            <span class="font-medium text-primary-dark"><strong> €{{ trip.price }},-
                                                </strong> p.p.</span>
                                        </div>
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
                                    </div>
                                    <div class="border-t border-secondary-sage/20 pt-6 space-y-6">
                                        <h3 class="text-xl font-bold text-primary-dark">
                                            Boek deze reis
                                        </h3>
                                        <DatePicker v-model="departure_date" :min-date="new Date()" />
                                        <PersonPicker v-model="participants" :min-adults="1" :min-children="0"
                                            :max-adults="6" :max-children="4" />
                                        <button @click="bookingModalOpen = true"
                                            class="w-full bg-accent-terracotta hover:bg-accent-terracotta/90 text-white font-semibold py-4 px-6 rounded-xl transition-colors duration-300 flex items-center justify-center gap-2 group">
                                            Boek nu
                                            <ChevronRightIcon
                                                class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
                                        </button>
                                        <p class="text-sm text-secondary-stone text-center mt-4">
                                            Of neem contact op voor meer informatie
                                        </p>

                                        <div class="flex gap-3 mt-4">
                                            <a href="#"
                                                class="tel-field has-icon flex-1 bg-secondary-sage/10 hover:bg-secondary-sage/20 text-primary-dark font-medium py-2 px-4 rounded-lg transition-colors duration-300 flex items-center justify-center gap-2">
                                                <PhoneIcon class="w-4 h-4" />
                                                Bellen
                                            </a>
                                            <a href="#"
                                                class="email-field has-icon flex-1 bg-secondary-sage/10 hover:bg-secondary-sage/20 text-primary-dark font-medium py-2 px-4 rounded-lg transition-colors duration-300 flex items-center justify-center gap-2">
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
    <Modal :open="bookingModalOpen" @close="bookingModalOpen = false">
        <BookingForm v-model:booking="booking" :validator="validator" @booking-completed="bookingModalOpen = false" />
    </Modal>
</template>
