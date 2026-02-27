<script setup>
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { computed } from 'vue';

const weekdaysTranslated = computed(() => {
    return t('common.weekdays').split('_')
})

const props = defineProps({
    trip: Object,
    tripItems: Object,
    practicalSections: Object,
    required: true,
});

const { t } = useI18n();

const blockedWeekdays = computed(() =>
    (props.trip.blocked_dates?.weekdays ?? []).map(Number)
)

const blockedDates = computed(() =>
    props.trip.blocked_dates?.dates ?? []
)

const hasAvailabilityRestrictions = computed(() =>
    blockedWeekdays.value.length > 0 || blockedDates.value.length > 0
)

function displayDate(entry) {
    if (typeof entry === 'string') {
        return new Intl.DateTimeFormat('nl-NL', { day: '2-digit', month: 'long', year: 'numeric' }).format(new Date(entry + 'T00:00:00'))
    }
    if (entry.start && entry.end) {
        const fmt = (d) => new Intl.DateTimeFormat('nl-NL', { day: '2-digit', month: 'short', year: 'numeric' }).format(new Date(d + 'T00:00:00'))
        return `${fmt(entry.start)} — ${fmt(entry.end)}`
    }
    return ''
}
</script>

<template>
    <Admin>
        <div class="max-w-wide mx-auto">
            <div class="grid grid-cols-1 laptop:grid-cols-3 gap-8">
                <!-- Header Section -->
                <div class="laptop:col-span-3 bg-white py-10">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-700">
                                {{ trip.name }}
                            </h1>
                            <p class="mt-1 text-sm text-gray-700/50">
                                {{ trip.slug }}
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <IconLink icon="Pencil" :href="route('admin.trips.edit', trip)"
                                v-tippy="t('admin.trips.show.edit_tooltip')" />
                            <IconLink icon="Route" :href="trip.itineraries?.length ?
                                route('admin.trips.itineraries.index', trip)
                                : route('admin.trips.itineraries.create', trip)"
                                v-tippy="t('admin.trips.show.itinerary_tooltip')" />
                        </div>
                    </div>
                </div>

                <div class="laptop:col-span-2 space-y-8">
                    <!-- Tabbed Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <TabGroup>
                            <div class="border-b border-gray-200 bg-white px-6">
                                <TabList class="flex space-x-8 -mb-px">
                                    <Tab v-slot="{ selected }" class="outline-none">
                                        <div class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer"
                                            :class="selected
                                                ? 'border-primary-default text-primary-default'
                                                : 'border-transparent text-gray-700/50 hover:text-gray-700 hover:border-gray-300'">
                                            {{ t('admin.trips.show.tabs.details') }}
                                        </div>
                                    </Tab>
                                    <Tab v-slot="{ selected }" class="outline-none">
                                        <div class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer"
                                            :class="selected
                                                ? 'border-primary-default text-primary-default'
                                                : 'border-transparent text-gray-700/50 hover:text-gray-700 hover:border-gray-300'">
                                            {{ t('admin.trips.show.tabs.items') }}
                                        </div>
                                    </Tab>
                                    <Tab v-slot="{ selected }" class="outline-none">
                                        <div class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer"
                                            :class="selected
                                                ? 'border-primary-default text-primary-default'
                                                : 'border-transparent text-gray-700/50 hover:text-gray-700 hover:border-gray-300'">
                                            {{ t('admin.trips.show.tabs.practical') }}
                                        </div>
                                    </Tab>
                                    <Tab v-slot="{ selected }" class="outline-none">
                                        <div class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer"
                                            :class="selected
                                                ? 'border-primary-default text-primary-default'
                                                : 'border-transparent text-gray-700/50 hover:text-gray-700 hover:border-gray-300'">
                                            {{ t('admin.trips.show.tabs.meta') }}
                                        </div>
                                    </Tab>
                                </TabList>
                            </div>

                            <TabPanels>
                                <TabPanel class="p-6 space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">{{
                                            t('admin.trips.show.details.name') }}</label>
                                        <p class="mt-1 text-gray-900">{{ trip.name }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">{{
                                            t('admin.trips.show.details.slug') }}</label>
                                        <p class="mt-1 text-gray-900">{{ trip.slug }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">{{
                                            t('admin.trips.show.details.description') }}</label>
                                        <p class="mt-1 text-gray-900">{{ trip.description }}</p>
                                    </div>
                                    <div v-if="trip.highlights?.length">
                                        <label class="text-sm font-medium text-gray-700">{{
                                            t('admin.trips.show.highlights.label') }}</label>
                                        <ul class="mt-1 text-gray-900 list-disc list-inside space-y-1">
                                            <li v-for="(highlight, index) in trip.highlights" :key="index">
                                                {{ highlight }}
                                            </li>
                                        </ul>
                                    </div>
                                </TabPanel>

                                <TabPanel class="p-6">
                                    <div v-if="tripItems && Object.keys(tripItems).length" class="space-y-8">
                                        <div v-for="(categories, typeLabel) in tripItems" :key="typeLabel"
                                            class="space-y-4">
                                            <h3
                                                class="text-lg font-bold text-gray-800 border-b-2 border-primary-default pb-2">
                                                {{ typeLabel }}
                                            </h3>
                                            <div v-for="(items, categoryLabel) in categories" :key="categoryLabel"
                                                class="ml-4 space-y-2">
                                                <h4 class="text-base font-semibold text-gray-700">{{ categoryLabel }}
                                                </h4>
                                                <ul v-if="items.length" class="ml-6 space-y-1 list-disc list-inside">
                                                    <li v-for="(item, index) in items" :key="index"
                                                        class="text-gray-900">
                                                        {{ item.item }}
                                                    </li>
                                                </ul>
                                                <p v-else class="ml-6 text-sm text-gray-500 italic">
                                                    {{ t('admin.trips.show.items.no_items') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="text-center py-8 text-gray-500">
                                        {{ t('admin.trips.show.items.empty') }}
                                    </div>
                                </TabPanel>
                                <TabPanel class="p-4 space-y-2">
                                    <template v-for="(label, key) in practicalSections" :key="key">
                                        <div v-if="trip.practical_info?.[key]" class="p-2">
                                            <h4 class="text-base tablet:text-lg font-semibold text-brand-primary mb-2">
                                                {{ label }}
                                            </h4>
                                            <div
                                                class="text-sm tablet:text-base text-brand-primary leading-relaxed whitespace-pre-line">
                                                {{ trip.practical_info[key] }}
                                            </div>
                                        </div>
                                    </template>
                                </TabPanel>

                                <TabPanel class="p-6 space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">{{
                                            t('admin.trips.show.meta.title') }}</label>
                                        <p class="mt-1 text-gray-900">{{ trip.meta_title || '-' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">{{
                                            t('admin.trips.show.meta.description') }}</label>
                                        <p class="mt-1 text-gray-900">{{ trip.meta_description || '-' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">{{
                                            t('admin.trips.show.meta.og_image') }}</label>
                                        <p class="mt-1 text-xs text-gray-500">
                                            {{ t('admin.trips.show.meta.og_url') }} <a target="_blank"
                                                :href="trip.og_image_url" class="underline text-blue-500">{{
                                                    trip.og_image_url }}</a>
                                        </p>
                                    </div>
                                </TabPanel>
                            </TabPanels>
                        </TabGroup>
                    </section>

                    <!-- Media Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">{{ t('admin.trips.show.media.title') }}</h2>
                            <p class="mt-1 text-sm text-gray-700/30">{{ t('admin.trips.show.media.subtitle') }}</p>
                        </div>
                        <div class="p-6 space-y-6">
                            <div v-if="trip.hero_image">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('admin.trips.show.media.hero') }}
                                </label>
                                <img :src="trip.hero_image.public_url" alt="Hero image"
                                    class="max-w-full h-auto rounded-lg shadow-md" />
                            </div>

                            <div v-if="trip.images?.length">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ t('admin.trips.show.media.gallery') }}
                                </label>
                                <div class="flex flex-wrap gap-4">
                                    <img v-for="(image, index) in trip.images" :key="index" :src="image.public_url"
                                        :alt="t('admin.trips.show.media.alt', { index })"
                                        class="w-24 h-24 object-cover rounded-lg shadow-md" />
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Rechterkolom: Prijs & Duur + Instellingen + Bestemmingen -->
                <div class="laptop:col-start-3 space-y-8">
                    <!-- Settings Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">{{ t('admin.trips.show.settings.title') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-700/30">{{ t('admin.trips.show.settings.subtitle') }}</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">{{
                                    t('admin.trips.show.settings.published_date') }}</span>
                                {{ trip.published_at_formatted }}
                            </div>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">{{
                                    t('admin.trips.show.settings.featured') }}</span>
                                <Pill class="px-3 py-1 rounded-full text-xs font-semibold"
                                    :type="trip.featured ? 'success' : 'warning'">
                                    {{ trip.featured ? t('admin.trips.show.settings.yes') :
                                        t('admin.trips.show.settings.no') }}
                                </Pill>
                            </div>
                        </div>
                    </section>

                    <!-- Pricing & Duration Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">{{ t('admin.trips.show.pricing.title') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-700/30">{{ t('admin.trips.show.pricing.subtitle') }}</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">{{
                                    t('admin.trips.show.pricing.price_per_person') }}</label>
                                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ trip.price_formatted }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">{{
                                    t('admin.trips.show.pricing.duration') }}</label>
                                <p class="mt-1 text-xl text-gray-900">{{ t('admin.trips.show.pricing.days', {
                                    duration:
                                        trip.duration
                                }) }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- Destination Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">{{ t('admin.trips.show.destinations.title')
                            }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-700/30">{{ t('admin.trips.show.destinations.subtitle') }}
                            </p>
                        </div>
                        <div class="p-6">
                            <div class="flex flex-wrap gap-2">
                                <Pill v-for="destination in trip.destinations" :key="destination.id" type="success"
                                    variant="transparent">
                                    <Link :href="route('admin.destinations.edit', destination.id)">
                                    {{ destination.region || destination.name }}
                                    </Link>
                                </Pill>
                            </div>
                        </div>
                    </section>

                    <!-- Transport Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">{{ t('admin.trips.show.transport.title') }}</h2>
                            <p class="mt-1 text-sm text-gray-700/30">{{ t('admin.trips.show.transport.subtitle') }}</p>
                        </div>
                        <div class="p-6">
                            <div v-if="trip.transport_formatted?.length" class="flex flex-wrap gap-2">
                                <Pill v-for="mode in trip.transport_formatted" :key="mode.value" type="primary" variant="transparent">
                                    <EnumIcon :enum="mode.value" class="w-4 h-4 mr-1.5 flex-none" />
                                    {{ mode.label }}
                                </Pill>
                            </div>
                            <p v-else class="text-sm text-gray-700/30">{{ t('admin.trips.show.transport.empty') }}</p>
                        </div>
                    </section>

                    <!-- Availability Section -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">{{ t('admin.trips.show.availability.title') }}</h2>
                            <p class="mt-1 text-sm text-gray-700/30">{{ t('admin.trips.show.availability.subtitle') }}</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <template v-if="hasAvailabilityRestrictions">
                                <div v-if="blockedWeekdays.length">
                                    <label class="text-sm font-medium text-gray-700">{{ t('admin.trips.show.availability.blocked_weekdays') }}</label>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <span
                                            v-for="day in 7"
                                            :key="day"
                                            class="px-3 py-1.5 rounded-md text-sm font-medium border"
                                            :class="blockedWeekdays.includes(day % 7)
                                                ? 'bg-status-error/10 border-status-error text-status-error'
                                                : 'bg-white border-gray-200 text-gray-700/30'"
                                        >
                                            {{ weekdaysTranslated[day % 7] }}
                                        </span>
                                    </div>
                                </div>

                                <div v-if="blockedDates.length">
                                    <label class="text-sm font-medium text-gray-700">{{ t('admin.trips.show.availability.blocked_dates') }}</label>
                                    <div class="mt-2 space-y-2">
                                        <div
                                            v-for="(entry, index) in blockedDates"
                                            :key="index"
                                            class="p-2 bg-gray-50 rounded-md border border-gray-200 text-sm text-gray-700"
                                        >
                                            {{ displayDate(entry) }}
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <p v-else class="text-sm text-gray-700/30">
                                {{ t('admin.trips.show.availability.no_restrictions') }}
                            </p>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </Admin>
</template>
