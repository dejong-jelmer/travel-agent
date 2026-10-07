<script setup>
import { ref, computed } from 'vue'
import { ChevronRight, ChevronDown, Phone, AtSign, CircleQuestionMark } from 'lucide-vue-next';
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
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
    breadcrumbs: Array
})

const { t } = useI18n()

// Modal
const requestModalOpen = ref(false)

// LightBox
const lightboxRef = ref(null)
const openLightbox = (index) => {
    lightboxRef.value?.open(index)
}

// Rendered slide width per breakpoint (screens.js), following the layout around the Slider: the page
// padding (px-4, tablet:px-6), from laptop on the two-thirds column (grid-cols-3, gap-12, max-w-screen-desktop),
// the card border and padding (p-6, laptop:p-8), the slider wrapper (-mx-[1%], see the template) and the slide
// width set by Slider (100% / visible slides - 2% margin). Update this when that layout changes.
const gallerySizes = '(min-width: 1350px) 252px, (min-width: 900px) calc(21.3vw - 36px), (min-width: 600px) calc(49vw - 48px), calc(100vw - 82px)'

// Inquiry card tabs
const activeCardTab = ref('about')
const cardTabs = computed(() => [
    { id: 'about', label: t('trip_show.card_tabs.about') },
    { id: 'practical', label: t('trip_show.card_tabs.practical') }
])

const inquiryCard = ref(null)
const openCardTab = (id) => {
    activeCardTab.value = id
    inquiryCard.value?.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

const hasCountryInfo = computed(() =>
    props.trip.destinations?.some(destination =>
        Object.keys(props.travelInfoSections ?? {}).some(key => destination.travel_info?.[key])
    ) ?? false
)

const hasKeyFacts = computed(() => props.trip.key_facts?.length > 0)
const hasIntro = computed(() => Boolean(props.trip.intro))

const contactUrl = computed(() => {
    const params = new URLSearchParams({ reis: props.trip.slug })

    return `?${params.toString()}#contact`
})

</script>

<template>
    <Layout>
        <template v-slot:hero>
            <!-- Hero Section -->
            <TripHero :trip="trip" />

        </template>
        <DecorativeLine />
        <!-- Main Content -->
        <div
            class="max-w-screen-wide laptop:max-w-screen-desktop mx-auto mb-1 tablet:mb-8 desktop:mb-10 px-4 tablet:px-6 pt-4 tablet:pt-6 laptop:pt-8 pb-32 tablet:pb-12 laptop:pb-0 bg-brand-background">
            <PageBreadcrumbs :items="breadcrumbs" class="mb-4 laptop:mb-6" />
            <div class="grid grid-cols-1 laptop:grid-cols-3 gap-12">
                <!-- Left Column - Main Content -->
                <div class="laptop:col-span-2 space-y-12">

                    <!-- Description & Highlights -->
                    <div class="bg-white rounded-2xl shadow-sm border border-brand-accent/20 p-6 laptop:p-8">
                        <div class="mb-8">
                            <SectionHeader>{{ t('trip_show.about_trip', { trip: trip.name }) }}</SectionHeader>

                            <!-- Intro and key facts: stacked on smaller screens, 60/40 from laptop up.
                                 Whichever is missing leaves the other at full width. -->
                            <div v-if="hasIntro || hasKeyFacts"
                                :class="['grid grid-cols-1 gap-6 mb-6 laptop:gap-8 laptop:items-start', hasIntro && hasKeyFacts ? 'laptop:grid-cols-[minmax(0,3fr)_minmax(0,2fr)]' : '']">
                                <div v-if="hasIntro" class="prose prose-brand max-w-[68ch]" v-html="trip.intro"></div>
                                <TripKeyFacts :facts="trip.key_facts" />
                            </div>

                            <!-- Slider adds a 1% margin around every slide; the negative margin cancels it on the
                                 outer edges so the photos span from the intro to the key facts edge to edge -->
                            <div class="-mx-[1%] mb-6">
                                <Slider :items="trip.images" :visible="3">
                                    <template #default="{ item, index, loaded }">
                                        <ResponsiveImage :image="item" :sizes="gallerySizes" :deferred="!loaded"
                                            :loading="index === 0 ? 'eager' : undefined"
                                            :alt="t('trip_show.gallery_image_alt', { trip: trip.name, position: index + 1 })"
                                            class="w-full h-36 tablet:h-full max-h-[500px] object-cover cursor-zoom-in rounded-xl shadow-sm"
                                            :key="index" @click="openLightbox(index)" />
                                    </template>
                                </Slider>
                                <LightBox ref="lightboxRef" :images="trip.images" />
                            </div>
                            <div class="prose prose-brand max-w-[68ch]" v-html="trip.description"></div>
                        </div>

                        <!-- Highlights -->
                        <div class="border-t border-brand-accent/20 pt-8">
                            <h3 class="text-lg font-semibold text-brand-primary mb-4">
                                {{ t('trip_show.highlights_heading') }}
                            </h3>
                            <Highlights :highlights="trip.highlights" />
                        </div>
                    </div>

                    <!-- Itinerary Section -->
                    <div
                        class="bg-white rounded-2xl shadow-sm border border-brand-accent/20 overflow-hidden p-6 laptop:p-8">
                        <SectionHeader>{{ t('trip_show.itinerary_heading') }}</SectionHeader>
                        <div v-if="trip.itineraries?.length" class="space-y-6">
                            <template v-for="(itinerary, index) in trip.itineraries" :key="index">
                                <TripItinerary :itinerary="itinerary" :index="index" />
                            </template>
                        </div>
                        <p v-else class="text-brand-light">
                            {{ t('trip_show.tab_content.itinerary_empty') }}
                        </p>
                    </div>
                </div>

                <!-- Right Column - Booking Sidebar -->
                <div class="laptop:col-span-1">
                    <div class="space-y-6">
                        <!-- Inquiry Card -->
                        <div ref="inquiryCard"
                            class="bg-white rounded-2xl shadow-lg border border-brand-accent/20 overflow-hidden scroll-mt-[125px]">
                            <!-- Tab Headers -->
                            <div class="border-b border-brand-accent/20">
                                <nav class="flex">
                                    <button v-for="tab in cardTabs" :key="tab.id" @click="activeCardTab = tab.id"
                                        :class="[
                                            'flex-1 px-4 py-3 text-center text-sm font-medium transition-colors',
                                            activeCardTab === tab.id
                                                ? 'text-brand-primary bg-brand-earth/10 border-b-2 border-brand-accent'
                                                : 'text-brand-light hover:text-brand-primary hover:bg-white'
                                        ]">
                                        {{ tab.label }}
                                    </button>
                                </nav>
                            </div>

                            <!-- Tab Content -->
                            <div class="p-6 laptop:p-8 space-y-2 laptop:space-y-4">
                                <template v-if="activeCardTab === 'about'">
                                    <p class="flex flex-col space-y-1 text-base text-brand-text mt-1">
                                        <span class="font-bold">
                                            {{ trip.is_expected
                                                ? t('trip_show.inquiry.expected')
                                                : t('trip_show.inquiry.price_from', { price: trip.price_formatted }) }}
                                        </span>
                                        <span>
                                            {{ trip.destinations_formatted }}
                                        </span>
                                        <span>
                                            {{ t('trip_show.inquiry.duration_label', { duration: trip.duration }) }}
                                        </span>
                                    </p>
                                    <div class="border-t border-brand-accent/20" role="presentation"></div>

                                    <TripItems :trip-items="tripItems" />
                                </template>

                                <template v-else-if="activeCardTab === 'practical'">
                                    <template v-for="(label, key) in practicalSections" :key="key">
                                        <div v-if="trip.practical_info?.[key]" >
                                            <h3 class="text-base tablet:text-lg font-semibold text-brand-primary">
                                                {{ label }}
                                            </h3>
                                            <div class="prose prose-brand max-w-none"
                                                v-html="trip.practical_info[key]"></div>
                                        </div>
                                    </template>

                                    <!-- Country info -->
                                    <Disclosure v-if="hasCountryInfo" v-slot="{ open }" as="div" class="pt-2">
                                        <h3 class="text-base tablet:text-lg font-semibold text-brand-primary">
                                            <DisclosureButton
                                                class="w-full flex items-center justify-between gap-2 text-left hover:text-brand-accent transition-colors">
                                                {{ t('trip_show.country_heading') }}
                                                <ChevronDown :class="open ? 'rotate-180' : ''"
                                                    class="w-5 h-5 flex-shrink-0 transition-transform" />
                                            </DisclosureButton>
                                        </h3>
                                        <DisclosurePanel class="mt-2">
                                            <TripTravelInfo :destinations="trip.destinations"
                                                :travel-info-sections="travelInfoSections" />
                                        </DisclosurePanel>
                                    </Disclosure>

                                    <!-- Empty state -->
                                    <p v-if="!hasCountryInfo && (!trip.practical_info || !Object.values(trip.practical_info).some(v => v))"
                                        class="text-brand-light text-center py-8">
                                        {{ t('trip_show.tab_content.practical_placeholder') }}
                                    </p>
                                </template>
                            </div>

                            <div class="px-6 laptop:px-8 space-y-2 laptop:space-y-4">
                                <div class="border-t border-brand-accent/20" role="presentation"></div>
                                <h3 class="text-base tablet:text-lg font-semibold text-brand-primary mb-2">
                                    {{ t('trip_show.inquiry.title') }}
                                </h3>
                                <!-- Explanation -->
                                <p class="text-base text-brand-text leading-relaxed">
                                    {{ t('trip_show.inquiry.explanation') }}
                                </p>
                            </div>
                            <div class="p-6 laptop:p-8 space-y-6 laptop:space-y-12">




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

                    </div>
                </div>
            </div>
        </div>

        <!-- Fixed bottom nav: mobile + tablet only -->
        <div
            class="fixed bottom-0 left-0 right-0 z-40 laptop:hidden bg-white border-t border-brand-primary/20 shadow-lg">
            <nav class="flex border-b border-brand-accent/20">
                <button v-for="tab in cardTabs" :key="tab.id" @click="openCardTab(tab.id)" :class="[
                    'flex-1 px-4 py-3 text-center text-sm font-medium transition-colors',
                    activeCardTab === tab.id
                        ? 'text-brand-primary bg-brand-earth/10 border-b-2 border-brand-accent'
                        : 'text-brand-light hover:text-brand-primary'
                ]">
                    {{ tab.label }}
                </button>
            </nav>
            <div class="flex items-center gap-1 px-2 py-2">
                <Button @click="requestModalOpen = !requestModalOpen"
                    class="w-full flex justify-center items-center gap-1 group">
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
