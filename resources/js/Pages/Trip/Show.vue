<script setup>
import { ref, toRef, watch, computed } from 'vue'
import { Clock, TrainFront, MapPinned, ChevronRight } from 'lucide-vue-next';
import { useBooking } from '@/Composables/useBooking.js'
import { useI18n } from 'vue-i18n'

const props = defineProps({
    trip: {
        type: Object,
        required: true
    },
    tripItems: Object,
    practicalSections: Object,
    travelInfoSections: Object,
    inclusions: {
        type: Object,
        default: () => ({ type_label: '', categories: [] })
    },
    exclusions: {
        type: Object,
        default: () => ({ type_label: '', categories: [] })
    }
})

const { t } = useI18n()

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
const booking = useBooking(props.trip)

const departure_date = toRef(booking.booking, 'departure_date')
const participants = toRef(booking.booking, 'participants')
watch(
    () => booking.booking.hasErrors,
    (newValue) => {
        if (newValue) {
            bookingModalOpen.value = true
        }
    }
)

const tabs = computed(() => [
    { id: 'itinerary', label: t('trip_show.tabs.itinerary') },
    { id: 'inclusive', label: t('trip_show.tabs.inclusive') },
    { id: 'practical', label: t('trip_show.tabs.practical') },
    { id: 'general_info', label: t('trip_show.tabs.general_info') }
])

</script>

<template>
    <Layout>
        <template v-slot:hero>
            <!-- Hero Section -->
            <section class="relative overflow-hidden">
                <!-- Background Image -->
                <div class="h-[calc(100vh-140px)] relative">
                    <div class="absolute inset-0 bg-cover bg-center"
                        :style="`background-image: url(${trip.hero_image?.public_url})`"></div>
                    <!-- Overlay -->
                    <div class="absolute inset-0 ">
                    </div>

                    <!-- Hero Content -->
                    <div class="absolute bottom-2 left-0 right-0">
                        <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto">
                            <div
                                class="max-w-5xl p-4 mx-2 laptop:p-8 border border-white rounded-3xl bg-brand-primary/30 backdrop -blur-[2px]">
                                <!-- Trip Title -->
                                <h1 class="text-3xl laptop:text-6xl font-bold text-white mb-6 leading-tight">
                                    {{ trip.name }}
                                </h1>

                                <!-- Trip Meta Info -->
                                <div class="flex flex-wrap gap-6 text-white">
                                    <!-- Price -->
                                    <div
                                        class="flex items-center gap-2 bg-accent-primary px-4 py-2 rounded-full font-bold">
                                        <span class="text-lg">{{ t('trip_show.hero.from_price', { price: trip.price })
                                        }}</span>
                                    </div>

                                    <!-- Duration -->
                                    <div
                                        class="flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full">
                                        <Clock class="w-5 h-5" />
                                        <span class="font-medium">{{ t('trip_show.hero.days', {
                                            duration: trip.duration
                                        }) }}</span>
                                    </div>

                                    <!-- Transport -->
                                    <div
                                        class="flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full">
                                        <TrainFront class="w-5 h-5" />
                                        <span class="font-medium">{{ t('trip_show.hero.sustainable_travel') }}</span>
                                    </div>

                                    <!-- Destination -->
                                    <div
                                        class="flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full">
                                        <MapPinned class="w-5 h-5" />
                                        <span class="font-medium">{{ trip.destinations_formatted }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </template>
        <DecorativeLine />
        <!-- Main Content -->
        <div class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto px-6 py-12 laptop:py-16">
            <div class="grid grid-cols-1 laptop:grid-cols-3 gap-12">
                <!-- Left Column - Main Content -->
                <div class="laptop:col-span-2 space-y-12">

                    <!-- Description & Highlights -->
                    <div class="bg-white rounded-2xl shadow-sm border border-brand-primary/20 p-6 laptop:p-8">
                        <div class="mb-8">
                            <div class="w-full text-center">
                                <SectionHeader>{{ t('trip_show.about_trip') }}</SectionHeader>
                            </div>
                            <div class="p-6">
                                <Slider :items="trip.images">
                                    <template #default="{ item, index }">
                                        <img :src="item.public_url" alt="Trip image"
                                            class="w-full h-[150px] rounded-md object-cover cursor-zoom-in" :key="index"
                                            loading="lazy" @click="openLightbox(index)" />
                                    </template>
                                </Slider>
                                <LightBox ref="lightboxRef" :images="trip.images" />
                            </div>
                            <p class="text-lg text-brand-primary leading-relaxed">
                                {{ trip.description }}
                            </p>
                        </div>

                        <!-- Highlights -->
                        <div class="border-t border-accent-primary/20 pt-8">
                            <ul class="space-y-4">
                                <template v-for="(highlight, index) in trip.highlights" :key="index">
                                    <li class="flex items-start gap-3">
                                        <span class="w-2 h-2 bg-accent-sage rounded-full mt-2 flex-shrink-0"></span>
                                        <span class="text-brand-primary">{{ highlight }}</span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>

                    <!-- Tabs Section -->
                    <div class="bg-white rounded-2xl shadow-sm border border-accent-primary/20 overflow-hidden">
                        <!-- Tab Headers -->
                        <div class="border-b border-accent-primary/20">
                            <nav class="flex overflow-x-auto">
                                <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id" :class="[
                                    'flex-1 px-6 py-4 text-center font-medium whitespace-nowrap transition-colors',
                                    activeTab === tab.id
                                        ? 'text-brand-primary bg-accent-earth/10 border-b-2 border-accent-primary'
                                        : 'text-brand-light hover:text-brand-primary hover:bg-white'
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
                                    <p v-else class="text-brand-light">
                                        {{ t('trip_show.tab_content.itinerary_empty') }}
                                    </p>
                                </div>
                            </div>
                            <div v-else-if="activeTab === 'inclusive'" class="space-y-8">
                                <TripInclusionsExclusions :trip-items="tripItems" />
                            </div>

                            <div v-else-if="activeTab === 'practical'" class="space-y-2">
                                <template v-for="(label, key) in practicalSections" :key="key">
                                    <div v-if="trip.practical_info?.[key]" class="p-2 tablet:p-4">
                                        <h4 class="text-base tablet:text-lg font-semibold text-brand-primary mb-2">
                                            {{ label }}
                                        </h4>
                                        <div
                                            class="text-sm tablet:text-base text-brand-primary leading-relaxed whitespace-pre-line">
                                            {{ trip.practical_info[key] }}
                                        </div>
                                    </div>
                                </template>

                                <!-- Empty state -->
                                <p v-if="!trip.practical_info || !Object.values(trip.practical_info).some(v => v)"
                                    class="text-brand-light text-center py-8">
                                    {{ t('trip_show.tab_content.practical_placeholder') }}
                                </p>
                            </div>

                            <div v-else-if="activeTab === 'general_info'" class="space-y-2">
                                <TripTravelInfo
                                    :destinations="trip.destinations"
                                    :travel-info-sections="travelInfoSections"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Booking Sidebar -->
                <div class="laptop:col-span-1">
                    <div class="sticky top-6 space-y-6">
                        <!-- Booking Card -->
                        <div class="bg-white rounded-2xl shadow-lg border border-accent-primary/20 overflow-hidden">
                            <div class=" bg-accent-primary p-4">
                                <h3 class="text-xl font-bold text-white">
                                    {{ t('trip_show.sidebar.trip_overview') }}
                                </h3>

                            </div>

                            <div class="p-6 space-y-6">
                                <!-- Quick Facts -->
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-brand-light">{{ t('trip_show.sidebar.from') }}</span>
                                        <span class="font-medium text-brand-primary"><strong> €{{ trip.price }},-
                                            </strong> {{ t('trip_show.sidebar.per_person') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-brand-light">{{ t('trip_show.sidebar.duration') }}</span>
                                        <span class="font-medium text-brand-primary">{{ trip.duration }}
                                            {{ t('trip_show.sidebar.days_label') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-brand-light">{{ t('trip_show.sidebar.transport') }}</span>
                                        <span class="font-medium text-brand-primary flex items-center gap-1">
                                            <TrainFront class="w-5 h-5" />
                                            {{ t('trip_show.sidebar.train') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="border-t border-accent-primary/20 pt-6 space-y-6">
                                    <h3 class="text-xl font-bold text-brand-primary">
                                        {{ t('trip_show.sidebar.book_this_trip') }}
                                    </h3>
                                    <DatePicker v-model="departure_date" :min-date="new Date()" />
                                    <PersonPicker v-model="participants" :min-adults="1" :min-children="0"
                                        :max-adults="6" :max-children="4" />
                                    <Button @click="bookingModalOpen = true"
                                        class="w-full flex justify-center items-center group">
                                        {{ t('trip_show.sidebar.book_now') }}
                                        <ChevronRight
                                            class=" group-hover:translate-x-2 transition-transform duration-300" />
                                    </Button>
                                    <p class="text-sm text-brand-light text-center mt-4">
                                        {{ t('trip_show.sidebar.contact_info') }}
                                    </p>
                                    <div class="flex gap-3 mt-4">
                                        <CallButton />
                                        <MailButton />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Extra Info -->
                        <TripExtraInfo />

                        <!-- Trust Indicators -->
                        <div class="bg-white/50 backdrop-blur-sm rounded-xl p-6 border border-accent-primary/20">
                            <h4 class="font-semibold text-brand-primary mb-4">{{ t('trip_show.sidebar.why_choose_us') }}
                            </h4>
                            <ul class="space-y-3 text-sm">
                                <li class="flex items-center gap-2">
                                    <span class="w-2 h-2 bg-accent-primary rounded-full"></span>
                                    <span class="text-brand-primary">{{ t('trip_show.sidebar.sustainable_travel')
                                    }}</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="w-2 h-2 bg-accent-primary rounded-full"></span>
                                    <span class="text-brand-primary">{{ t('trip_show.sidebar.small_personal') }}</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="w-2 h-2 bg-accent-primary rounded-full"></span>
                                    <span class="text-brand-primary">{{ t('trip_show.sidebar.carefree_travel') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
    <Modal :open="bookingModalOpen" @close="bookingModalOpen = false">
        <BookingForm :booking="booking.booking" :constraints="booking.constraints" />
    </Modal>
</template>
