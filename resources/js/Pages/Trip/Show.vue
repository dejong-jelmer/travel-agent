<script setup>
import { ref, computed, nextTick, onMounted, onBeforeUnmount } from 'vue'
import { ChevronRight, ChevronDown, Phone, AtSign, CircleQuestionMark } from 'lucide-vue-next';
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

const props = defineProps({
    trip: {
        type: Object,
        required: true
    },
    // Shape: [{ key, title, html }], the description split on its H2 headings
    descriptionSections: {
        type: Array,
        default: () => []
    },
    tripItems: Object,
    practicalSections: Object,
    travelInfoSections: Object,
    breadcrumbs: Array
})

const { t } = useI18n()

// Modal
const requestModalOpen = ref(false)

// LightBox, opening from the gallery buttons
const lightboxRef = ref(null)
const thumbnails = []
const openLightbox = (index) => {
    lightboxRef.value?.open(index, thumbnails)
}

// Rendered slide width per breakpoint (screens.js), following the layout around the PhotoSlider: the page
// padding (px-4, tablet:px-6), from laptop on the two-thirds column (grid-cols-3, gap-12, max-w-screen-desktop),
// the card border and padding (p-6, laptop:p-8) and the slide width set by PhotoSlider (84% on phones, from
// tablet on a third of the width minus two 12px gaps). Update this when that layout changes.
const gallerySizes = '(min-width: 1350px) 254px, (min-width: 900px) calc(22.2vw - 46px), (min-width: 600px) calc(33.3vw - 41px), calc(84vw - 69px)'

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

// Sidebar: sticky from laptop up, but only while it fits in the window below its top offset; otherwise it
// scrolls along with the page. Re-checked whenever its height changes (tab switch, country info) or the window resizes.
const sidebar = ref(null)
const sidebarFits = ref(false)
const SIDEBAR_BOTTOM_GAP = 24

const updateSidebarFits = async () => {
    const el = sidebar.value
    // The top offset comes from the laptop:top-[...] class; below laptop it is 'auto' and sticky does not apply
    const top = parseFloat(getComputedStyle(el).top) || 0
    const fits = top + el.offsetHeight + SIDEBAR_BOTTOM_GAP <= window.innerHeight
    if (fits === sidebarFits.value) {
        return
    }

    // A stuck sidebar that stops fitting drops back to the top of its column, far above the window when the
    // page is scrolled down. Scroll the page along so it stays where it was and the tab just clicked stays in view.
    const topBefore = el.getBoundingClientRect().top
    sidebarFits.value = fits
    if (!fits) {
        await nextTick()
        window.scrollBy({ top: el.getBoundingClientRect().top - topBefore, behavior: 'instant' })
    }
}

let sidebarObserver = null
onMounted(() => {
    sidebarObserver = new ResizeObserver(updateSidebarFits)
    sidebarObserver.observe(sidebar.value)
    window.addEventListener('resize', updateSidebarFits)
})
onBeforeUnmount(() => {
    sidebarObserver?.disconnect()
    window.removeEventListener('resize', updateSidebarFits)
})

const hasCountryInfo = computed(() =>
    props.trip.destinations?.some(destination =>
        Object.keys(props.travelInfoSections ?? {}).some(key => destination.travel_info?.[key])
    ) ?? false
)

const hasKeyFacts = computed(() => props.trip.key_facts?.length > 0)
const hasHighlights = computed(() => props.trip.highlights?.length > 0)

// The first section of the description opens the page next to the key facts, the others get a card each
const leadSection = computed(() => props.descriptionSections[0] ?? null)
const storySections = computed(() => props.descriptionSections.slice(1))

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

                    <!-- First description section, key facts & gallery -->
                    <div class="bg-white rounded-2xl shadow-sm border border-brand-accent/20 p-6 laptop:p-8">
                        <SectionHeader>{{ t('trip_show.about_trip', { trip: trip.name }) }}</SectionHeader>

                        <!-- First description section and key facts: stacked on smaller screens, 60/40 from laptop up.
                             Whichever is missing leaves the other at full width. -->
                        <div v-if="leadSection || hasKeyFacts"
                            :class="['grid grid-cols-1 gap-6 mb-6 laptop:gap-8 laptop:items-start', leadSection && hasKeyFacts ? 'laptop:grid-cols-[minmax(0,3fr)_minmax(0,2fr)]' : '']">
                            <div v-if="leadSection">
                                <h2 v-if="leadSection.title" class="text-[26px] font-semibold text-brand-primary mb-4">
                                    {{ leadSection.title }}
                                </h2>
                                <div class="prose prose-brand max-w-[68ch]" v-html="leadSection.html"></div>
                            </div>
                            <TripKeyFacts :facts="trip.key_facts" />
                        </div>

                        <PhotoSlider :items="trip.images" :label="t('trip_show.gallery_label', { trip: trip.name })">
                            <template #default="{ item, index, loaded }">
                                <!-- The slider clips anything outside the photos, so the focus outline is drawn inside -->
                                <button type="button" :ref="(el) => thumbnails[index] = el" aria-haspopup="dialog"
                                    class="block w-full rounded-xl cursor-zoom-in focus-visible:outline focus-visible:outline-2 focus-visible:-outline-offset-4 focus-visible:outline-brand-accent"
                                    @click="openLightbox(index)">
                                    <ResponsiveImage :image="item" :sizes="gallerySizes" :deferred="!loaded"
                                        :loading="index === 0 ? 'eager' : undefined"
                                        :alt="t('trip_show.gallery_image_alt', { trip: trip.name, position: index + 1 })"
                                        class="w-full aspect-[4/3] tablet:aspect-[4/5] object-cover rounded-xl" />
                                </button>
                            </template>
                        </PhotoSlider>
                        <LightBox ref="lightboxRef" :images="trip.images" />
                    </div>

                    <!-- Other description sections, a card each -->
                    <div v-if="storySections.length" class="space-y-8">
                        <TripStorySection v-for="section in storySections" :key="section.key" :section="section" />
                    </div>

                    <!-- Highlights -->
                    <BaseCard v-if="hasHighlights" aria-labelledby="trip-highlights-heading">
                        <SectionHeader id="trip-highlights-heading">{{ t('trip_show.highlights_heading') }}</SectionHeader>
                        <Highlights :highlights="trip.highlights" />
                    </BaseCard>

                    <!-- Inclusions & Exclusions -->
                    <TripItems :trip-items="tripItems" />

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

                    <!-- Plan this trip -->
                    <TripPlanCard @request="requestModalOpen = !requestModalOpen" />
                </div>

                <!-- Right Column - Booking Sidebar -->
                <div class="laptop:col-span-1">
                    <!-- top: 24px below the sticky header (header-height in tailwind.config.js); sticky only while it fits, see updateSidebarFits -->
                    <div ref="sidebar" class="space-y-6 laptop:top-[calc(theme(header-height.laptop)+24px)]"
                        :class="{ 'laptop:sticky': sidebarFits }">
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
