<script setup>
import { ref, computed, toRef, watch } from 'vue'
import { ChevronRight, Map, ListChecks, Info, Globe, Phone, AtSign, CircleQuestionMark } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3'
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
const tabsSection = ref(null)
const selectTab = (id) => {
    activeTab.value = id
    tabsSection.value?.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

// Modal
const requestModalOpen = ref(false)

// LightBox
const lightboxRef = ref(null)
const openLightbox = (index) => {
    lightboxRef.value?.open(index)
}

// Inquiry
const contactUrl = computed(() => {
    const params = new URLSearchParams({ reis: props.trip.slug })

    return `${route('contact')}?${params.toString()}#contact-form`
})

const tabs = computed(() => [
    { id: 'itinerary', label: t('trip_show.tabs.itinerary') },
    { id: 'inclusive', label: t('trip_show.tabs.inclusive') },
    { id: 'practical', label: t('trip_show.tabs.practical') },
    { id: 'general_info', label: t('trip_show.tabs.general_info') }
])

const tripMeta = computed(() => ({
    price: props.trip.is_expected
        ? t('trip_show.hero.expected')
        : `${t('trip_show.hero.from_price', { price: props.trip.price_formatted })} ${t('trip_show.hero.per_person')}`,
    data: [
        props.trip.destinations_formatted,
        t('trip_show.hero.days', {
            duration: props.trip.duration
        })
    ]
}))

const tabIcons = {
    itinerary: Map,
    inclusive: ListChecks,
    practical: Info,
    general_info: Globe,
}

</script>

<template>
    <Layout>
        <template v-slot:hero>
            <!-- Hero Section -->
            <PageHero overlay-class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/10 to-transparent"
                :image="trip.hero_image?.public_url" :title="trip.name" :subtitle="trip.intro" :trip-meta="tripMeta" />

        </template>
        <DecorativeLine />
        <!-- Main Content -->
        <div
            class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto mb-1 tablet:mb-8 desktop:mb-10 px-4 tablet:px-6 py-8 tablet:py-12 laptop:py-16 pb-24 laptop:pb-0">
            <div class="grid grid-cols-1 laptop:grid-cols-3 gap-12">
                <!-- Left Column - Main Content -->
                <div class="laptop:col-span-2 space-y-12">

                    <!-- Description & Highlights -->
                    <div class="bg-white rounded-2xl shadow-sm border border-brand-primary/20 p-6 laptop:p-8">
                        <div class="mb-8">
                            <div class="w-full text-center">
                                <SectionHeader>{{ t('trip_show.about_trip', { trip: trip.name }) }}</SectionHeader>
                            </div>
                            <div class="p-0 laptop:p-6">
                                <Slider :items="trip.images" :visible="1">
                                    <template #default="{ item, index }">
                                        <img :src="item.public_url" alt="Trip image"
                                            class="w-full h-36 tablet:h-full max-h-[500px] object-cover cursor-zoom-in"
                                            :key="index" loading="lazy" @click="openLightbox(index)" />
                                    </template>
                                </Slider>
                                <LightBox ref="lightboxRef" :images="trip.images" />
                            </div>
                            <p class="text-lg text-brand-text leading-relaxed" v-html="trip.description">
                            </p>
                        </div>

                        <!-- Highlights -->
                        <div v-if="trip.highlights?.length" class="border-t border-brand-accent/20 pt-8">
                            <h3 class="text-lg font-semibold text-brand-primary mb-4">
                                {{ t('trip_show.highlights_heading') }}
                            </h3>
                            <ul class="space-y-4">
                                <template v-for="(highlight, index) in trip.highlights" :key="index">
                                    <li class="flex items-start gap-3">
                                        <span class="w-2 h-2 bg-brand-subtle rounded-full mt-2 flex-shrink-0"></span>
                                        <span class="text-brand-text">{{ highlight }}</span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>

                    <!-- Tabs Section -->
                    <div ref="tabsSection"
                        class="bg-white rounded-2xl shadow-sm border border-brand-accent/20 overflow-hidden scroll-mt-[125px]">
                        <!-- Tab Headers -->
                        <div class="hidden laptop:block border-b border-brand-accent/20">
                            <nav class="flex overflow-x-auto">
                                <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id" :class="[
                                    'flex-1 px-6 py-4 text-center font-medium whitespace-nowrap transition-colors',
                                    activeTab === tab.id
                                        ? 'text-brand-primary bg-brand-earth/10 border-b-2 border-brand-accent'
                                        : 'text-brand-light hover:text-brand-primary hover:bg-white'
                                ]">
                                    {{ tab.label }}
                                </button>
                            </nav>
                        </div>

                        <!-- Tab Content -->
                        <div class="p-6 laptop:p-8">
                            <div v-if="activeTab === 'itinerary'" class="space-y-6">
                                <div v-if="trip.itineraries?.length" class="space-y-6">
                                    <template v-for="(itinerary, index) in trip.itineraries" :key="index">
                                        <TripItinerary :itinerary="itinerary" :index="index" />
                                    </template>
                                </div>
                                <p v-else class="text-brand-light">
                                    {{ t('trip_show.tab_content.itinerary_empty') }}
                                </p>
                            </div>
                            <div v-else-if="activeTab === 'inclusive'" class="space-y-6">
                                <TripItems :trip-items="tripItems" />
                            </div>

                            <div v-else-if="activeTab === 'practical'" class="space-y-2">
                                <template v-for="(label, key) in practicalSections" :key="key">
                                    <div v-if="trip.practical_info?.[key]" class="p-2 tablet:p-4">
                                        <h4 class="text-base tablet:text-lg font-semibold text-brand-primary mb-2">
                                            {{ label }}
                                        </h4>
                                        <div
                                            class="text-sm tablet:text-base text-brand-text leading-relaxed whitespace-pre-line">
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
                                <TripTravelInfo :destinations="trip.destinations"
                                    :travel-info-sections="travelInfoSections" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Booking Sidebar -->
                <div class="laptop:col-span-1">
                    <div class="sticky top-6 space-y-6">
                        <!-- Inquiry Card -->
                        <div class="bg-white rounded-2xl shadow-lg border border-brand-accent/20 overflow-hidden">
                            <div class="p-6 laptop:p-8 space-y-6 laptop:space-y-12">
                                <h3 class="text-base tablet:text-lg font-semibold text-brand-primary mb-2">
                                    {{ t('trip_show.inquiry.title') }}
                                </h3>

                                <!-- Price indication -->
                                <div>
                                    <span class="text-base tablet:text-base font-semibold text-brand-primary">
                                        {{ trip.is_expected
                                            ? t('trip_show.inquiry.expected')
                                            : t('trip_show.inquiry.price_from', { price: trip.price_formatted }) }}
                                    </span>
                                    <span class="block text-sm text-brand-light mt-1">
                                        {{ t('trip_show.inquiry.duration_label', { duration: trip.duration }) }}
                                    </span>
                                </div>

                                <!-- Explanation -->
                                <p class="text-base text-brand-text leading-relaxed">
                                    {{ t('trip_show.inquiry.explanation') }}
                                </p>

                                <!-- CTA buttons -->
                                <div class="space-y-3">
                                    <div class="block">
                                        <Button @click="requestModalOpen = !requestModalOpen"
                                            class="w-full flex justify-center items-center">
                                            {{ t('trip_show.inquiry.cta_make_request') }}
                                        </Button>
                                    </div>
                                </div>

                                <!-- Direct contact -->
                                <div class="border-t border-brand-accent/20 pt-4">
                                    <p class="text-sm text-brand-light mb-3">
                                        {{ t('trip_show.inquiry.direct_contact_label') }}
                                    </p>
                                    <div class="space-y-2 items-center flex-shrink-0">
                                        <span class="flex items-center gap-2">
                                            <Phone class="w-4 h-4 text-brand-primary " />
                                            <a href="#"
                                                class="tel-field text-sm text-brand-text hover:text-brand-primary transition-colors">
                                            </a>
                                        </span>
                                        <a href="#"
                                            class="email-field has-icon flex items-center gap-2 text-sm text-brand-text hover:text-brand-primary hover:underline transition-colors">
                                            <AtSign class="w-4 h-4 text-brand-primary flex-shrink-0" />
                                            {{ t('trip_show.inquiry.send_email') }}
                                        </a>
                                        <span class="flex items-center gap-2">
                                            <CircleQuestionMark class="w-4 h-4 text-brand-primary flex-shrink-0" />

                                            <Link :href="contactUrl"
                                                class="flex flex-wrap items-center text-sm text-brand-text hover:text-brand-primary hover:underline transition-colors">
                                                {{ t('trip_show.inquiry.direct_contact_form') }}
                                            </Link>

                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Extra Info -->
                        <TripExtraInfo />

                        <!-- Trust Indicators -->

                    </div>
                </div>
            </div>
        </div>

        <!-- Fixed bottom nav: mobile + tablet only -->
        <div
            class="fixed bottom-0 left-0 right-0 z-40 laptop:hidden bg-white border-t border-brand-primary/20 shadow-lg">
            <div class="flex items-center gap-1 px-2 py-2">
                <button v-for="tab in tabs" :key="tab.id" @click="selectTab(tab.id)"
                    class="flex-1 flex flex-col items-center gap-0.5 py-2 px-1 rounded-lg transition-colors" :class="activeTab === tab.id
                        ? 'text-brand-primary bg-brand-earth/10'
                        : 'text-brand-light hover:text-brand-primary'">
                    <component :is="tabIcons[tab.id]" class="w-5 h-5" />
                    <span class="text-xs hidden tablet:inline">{{ tab.label }}</span>
                </button>


                <Button @click="requestModalOpen = !requestModalOpen" class="flex items-center gap-1 group">
                    {{ t('trip_show.inquiry.cta_make_request') }}
                    <ChevronRight class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-300" />
                </Button>

            </div>
        </div>
    </Layout>
    <Modal :open="requestModalOpen" @close="requestModalOpen = false">
        <TripRequestForm :trip="trip" />
    </Modal>
</template>
