<script setup>
import { computed, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
import { useCharacterCounter } from '@/Composables/useCharacterCounter.js';
import { useI18n } from 'vue-i18n';
import { debounce } from 'lodash';
import { fetchApi } from '@/fetchApi';
import TripItemsTab from './TripItemsTab.vue';

const emit = defineEmits(['submit']);
const props = defineProps({
    destinations: Object,
    form: Object,
    typeOptions: Object,
    transportOptions: Object,
    priceLabelOptions: Object,
    keyFactIconOptions: Array,
    // Shape: [{ id, name, icon }], the cases of the HighlightCategory enum with their default label and icon name
    highlightCategoryOptions: {
        type: Array,
        default: () => []
    },
    // Shape: [{ id, name }], the cases of the HeroFocus enum: the nine focus points of the hero image
    heroFocusOptions: {
        type: Array,
        default: () => []
    },
    // Shape: [{ id, name }], the sections of the saved description that can become the journey section
    journeySectionOptions: {
        type: Array,
        default: () => []
    },
    // Shape: [Image], the saved gallery images that can be shown next to a description section
    galleryImages: {
        type: Array,
        default: () => []
    },
    practicalSections: Object,
});

const destinationOptions = computed(() => (
    props.destinations?.map((d) => (
        { 'id': d.id, 'name': d.region || d.name }
    )).sort((a, b) => a.name.localeCompare(b.name))
))

const { t } = useI18n();

const page = usePage();
const seoConfig = page.props.config?.seo || {}

const META_TITLE_MAX_LENGTH = seoConfig.meta_title_max_length || 0
const META_DESCRIPTION_MAX_LENGTH = seoConfig.meta_description_max_length || 0

function daysToDisplay(days) {
    if (!days) return { value: null, unit: 'days' }
    if (days % 7 === 0) return { value: days / 7, unit: 'weeks' }
    return { value: days, unit: 'days' }
}

const { value: initMinAdvanceValue, unit: initMinAdvanceUnit } = daysToDisplay(props.form.min_advance_days)
const minAdvanceValue = ref(initMinAdvanceValue)
const minAdvanceUnit = ref(initMinAdvanceUnit ?? 'days')

const unitMultiplier = { days: 1, weeks: 7 }
const advanceUnitOptions = computed(() => [
    { id: 'days',  name: t('forms.trip.fields.min_advance.units.days') },
    { id: 'weeks', name: t('forms.trip.fields.min_advance.units.weeks') },
])

watch([minAdvanceValue, minAdvanceUnit], ([val, unit]) => {
    const num = Number(val)
    props.form.min_advance_days = num > 0 ? Math.round(num * unitMultiplier[unit]) : null
})

// Keep in sync with Trip::MAX_KEY_FACTS, Trip::MAX_KEY_FACT_LABEL_LENGTH and Trip::MAX_KEY_FACT_VALUE_LENGTH
const KEY_FACTS_MAX = 6
const KEY_FACT_LABEL_MAX_LENGTH = 20
const KEY_FACT_VALUE_MAX_LENGTH = 60

// Matches KeyFactIcon::default()
const KEY_FACT_DEFAULT_ICON = 'info'

const keyFactFields = computed(() => [
    {
        key: 'icon',
        type: 'icon',
        label: t('forms.trip.fields.key_facts.icon.label'),
        options: props.keyFactIconOptions ?? [],
        default: KEY_FACT_DEFAULT_ICON,
    },
    {
        key: 'label',
        label: t('forms.trip.fields.key_facts.label_field.label'),
        placeholder: t('forms.trip.fields.key_facts.label_field.placeholder'),
    },
    {
        key: 'value',
        label: t('forms.trip.fields.key_facts.value.label'),
        placeholder: t('forms.trip.fields.key_facts.value.placeholder'),
    },
])

// The sections that can become the journey section follow the H2 headings in the editor: the content parser on the
// server splits the description, so the keys match the ones the trip page and the validation use
const sectionOptions = ref(props.journeySectionOptions)
const journeySectionSelectOptions = computed(() => [
    { id: '', name: t('forms.trip.fields.journey_section.none') },
    ...sectionOptions.value,
])

let latestSectionsRequest = 0
const refreshSectionOptions = debounce((description) => {
    const request = ++latestSectionsRequest
    fetchApi(route('admin.trips.description-sections'), {
        method: 'POST',
        body: { description },
    })
        .then((options) => {
            // Only the answer for the latest description counts
            if (request !== latestSectionsRequest) return
            sectionOptions.value = options

            // A renamed or removed heading is no longer a section, so the choice falls back to none
            if (props.form.journey_section && !options.some(option => option.id === props.form.journey_section)) {
                props.form.journey_section = ''
            }
        })
        .catch((error) => console.error(error))
}, 500)

watch(() => props.form.description, refreshSectionOptions)

// Every section that can become the journey section can also get a photo. Only saved gallery images that are still
// in the gallery can be chosen, in gallery order: new uploads get an id once the trip is saved. The server ignores
// the keys of sections no longer in the description and cleans them up on save.
const sectionImageOptions = computed(() => (props.form.images ?? [])
    .map((source, index) => ({
        image: props.galleryImages.find(image => image.path === source),
        position: index + 1,
    }))
    .filter(option => option.image))

const isSectionImage = (sectionKey, imageId) => (props.form.section_images?.[sectionKey] ?? null) === imageId
const hasSectionImage = (sectionKey) => sectionImageOptions.value.some(({ image }) => isSectionImage(sectionKey, image.id))
const setSectionImage = (sectionKey, imageId) => {
    props.form.section_images[sectionKey] = imageId
}

// Keep in sync with Trip::MAX_HIGHLIGHT_LABEL_LENGTH
const HIGHLIGHT_LABEL_MAX_LENGTH = 24

// The own label shows the default label of the chosen category as its placeholder
const highlightCategoryLabel = (category) =>
    props.highlightCategoryOptions.find(option => option.id === category)?.name ?? null

const highlightFields = computed(() => [
    {
        key: 'category',
        type: 'icon',
        label: t('forms.trip.fields.highlights.category.label'),
        options: [
            { id: '', name: t('forms.trip.fields.highlights.category.none') },
            ...props.highlightCategoryOptions,
        ],
        default: '',
        iconComponent: 'HighlightIcon',
    },
    {
        key: 'label',
        label: t('forms.trip.fields.highlights.label_field.label'),
        placeholder: (item) => highlightCategoryLabel(item?.category) ?? t('forms.trip.fields.highlights.label_field.label'),
    },
    {
        key: 'title',
        label: t('forms.trip.fields.highlights.title.label'),
        placeholder: t('forms.trip.fields.highlights.title.placeholder'),
    },
    {
        key: 'description',
        label: t('forms.trip.fields.highlights.description.label'),
        placeholder: t('forms.trip.fields.highlights.description.placeholder'),
    }
])

const { length: metaTitleLength, charsLeft: metaTitleCharsLeft, counterClass: metaTitleClass } = useCharacterCounter(
    computed(() => props.form?.meta_title),
    META_TITLE_MAX_LENGTH
)

const { length: metaDescriptionLength, charsLeft: metaDescriptionCharsLeft, counterClass: metaDescriptionClass } = useCharacterCounter(
    computed(() => props.form?.meta_description),
    META_DESCRIPTION_MAX_LENGTH
)

</script>

<template>
    <form @submit.prevent="submit" class="max-w-wide mx-auto">
        <div class="grid grid-cols-1 laptop:grid-cols-3 gap-8">
            <!-- Header Section -->
            <div class="laptop:col-span-3 bg-white py-10">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-700">
                            {{ form.id ? t('forms.trip.edit_heading') : t('forms.trip.new_heading') }}
                        </h1>
                        <p class="mt-1 text-sm text-gray-700/50">
                            {{ t('forms.trip.subheading') }}
                        </p>
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
                                        {{ t('forms.trip.tabs.basic') }}
                                    </div>
                                </Tab>
                                <Tab v-slot="{ selected }" class="outline-none">
                                    <div class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer"
                                        :class="selected
                                            ? 'border-primary-default text-primary-default'
                                            : 'border-transparent text-gray-700/50 hover:text-gray-700 hover:border-gray-300'">
                                        {{ t('forms.trip.tabs.items') }}
                                    </div>
                                </Tab>
                                <Tab v-slot="{ selected }" class="outline-none">
                                    <div class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer"
                                        :class="selected
                                            ? 'border-primary-default text-primary-default'
                                            : 'border-transparent text-gray-700/50 hover:text-gray-700 hover:border-gray-300'">
                                        {{ t('forms.trip.tabs.practical') }}
                                    </div>
                                </Tab>
                                <Tab v-slot="{ selected }" class="outline-none">
                                    <div class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer"
                                        :class="selected
                                            ? 'border-primary-default text-primary-default'
                                            : 'border-transparent text-gray-700/50 hover:text-gray-700 hover:border-gray-300'">
                                        {{ t('forms.trip.tabs.meta') }}
                                    </div>
                                </Tab>
                            </TabList>
                        </div>

                        <TabPanels>
                            <TabPanel class="p-6 space-y-6">
                                <Input type="text" name="name" :label="t('forms.trip.fields.name.label')"
                                    :required="true" v-model="form.name" :feedback="form.errors.name"
                                    :placeholder="t('forms.trip.fields.name.placeholder')" />

                                 <Input type="text" name="subtitle" :label="t('forms.trip.fields.subtitle.label')"
                                    :required="true" v-model="form.subtitle" :feedback="form.errors.subtitle"
                                    :placeholder="t('forms.trip.fields.subtitle.placeholder')" />
                                <Label for-field="description" :required="true">{{ t('forms.trip.fields.description.label') }}</Label>
                                <TipTap name="description" :required="true" v-model="form.description" :feedback="form.errors.description" />
                                <FormFeedback :message="form.errors.description" />
                                <div>
                                    <Select name="journey_section" v-model="form.journey_section"
                                        :label="t('forms.trip.fields.journey_section.label')"
                                        :options="journeySectionSelectOptions"
                                        :feedback="form.errors.journey_section" />
                                    <p class="mt-2 text-xs text-gray-700/30">
                                        {{ t('forms.trip.fields.journey_section.help') }}
                                    </p>
                                </div>
                                <div>
                                    <DynamicInputList :items="form.highlights" name="highlights"
                                        :label="t('forms.trip.fields.highlights.label')"
                                        :fields="highlightFields" :sortable="true"
                                        :feedback="form.errors" />
                                    <p class="mt-2 text-xs text-gray-700/30">
                                        {{ t('forms.trip.fields.highlights.help', { label: HIGHLIGHT_LABEL_MAX_LENGTH }) }}
                                    </p>
                                </div>
                                <div>
                                    <DynamicInputList :items="form.key_facts" name="key_facts"
                                        :label="t('forms.trip.fields.key_facts.label')"
                                        :fields="keyFactFields" fields-class="tablet:grid-cols-[13rem_10rem_minmax(0,1fr)]"
                                        :sortable="true" :max="KEY_FACTS_MAX"
                                        :feedback="form.errors" />
                                    <FormFeedback :message="form.errors.key_facts" />
                                    <p class="mt-2 text-xs text-gray-700/30">
                                        {{ t('forms.trip.fields.key_facts.help', { max: KEY_FACTS_MAX, label: KEY_FACT_LABEL_MAX_LENGTH, value: KEY_FACT_VALUE_MAX_LENGTH }) }}
                                    </p>
                                </div>
                            </TabPanel>

                            <TabPanel class="p-6">
                                <TripItemsTab :form="form" :type-options="typeOptions" />
                            </TabPanel>

                            <TabPanel class="p-6 space-y-6">
                                <template v-for="(label, key) in practicalSections" :key="key">
                                    <TipTap :name="`practical_info.${key}`" :label="label"
                                        v-model="form.practical_info[key]"
                                        :feedback="form.errors[`practical_info.${key}`]" />
                                </template>
                            </TabPanel>

                            <TabPanel class="p-6 space-y-6">
                                <div>
                                    <Input type="text" name="meta_title"
                                        :label="t('forms.trip.fields.meta_title.label')" v-model="form.meta_title"
                                        :feedback="form.errors.meta_title"
                                        :placeholder="t('forms.trip.fields.meta_title.placeholder')" />
                                    <div class="mt-2 flex items-center justify-between text-xs">
                                        <span :class="metaTitleClass">
                                            {{ t('forms.trip.fields.meta_title.characters', {
                                                current: metaTitleLength,
                                            max: META_TITLE_MAX_LENGTH
                                            }) }}
                                        </span>
                                        <span v-if="metaTitleCharsLeft < 0" class="text-status-error font-semibold">
                                            {{ t('forms.trip.character_counter.too_many', {
                                                count:
                                            Math.abs(metaTitleCharsLeft) }) }}
                                        </span>
                                        <span v-else-if="metaTitleCharsLeft <= 10" :class="metaTitleClass">
                                            {{ t('forms.trip.character_counter.remaining', {
                                                count: metaTitleCharsLeft
                                            }) }}
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <TextArea name="meta_description"
                                        :label="t('forms.trip.fields.meta_description.label')"
                                        v-model="form.meta_description" :feedback="form.errors.meta_description"
                                        :placeholder="t('forms.trip.fields.meta_description.placeholder')" :rows="4" />
                                    <div class="mt-2 flex items-center justify-between text-xs">
                                        <span :class="metaDescriptionClass">
                                            {{ t('forms.trip.fields.meta_description.characters', {
                                                current:
                                            metaDescriptionLength, max:
                                            META_DESCRIPTION_MAX_LENGTH }) }}
                                        </span>
                                        <span v-if="metaDescriptionCharsLeft < 0"
                                            class="text-status-error font-semibold">
                                            {{ t('forms.trip.character_counter.too_many', {
                                                count:
                                            Math.abs(metaDescriptionCharsLeft) }) }}
                                        </span>
                                        <span v-else-if="metaDescriptionCharsLeft <= 20" :class="metaDescriptionClass">
                                            {{ t('forms.trip.character_counter.remaining', {
                                                count:
                                            metaDescriptionCharsLeft }) }}
                                        </span>
                                    </div>
                                </div>
                            </TabPanel>
                        </TabPanels>
                    </TabGroup>
                </section>

                <!-- Media Section -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">{{ t('forms.trip.tabs.media') }} *</h2>
                        <p class="mt-1 text-sm text-gray-700/30">{{ t('forms.trip.sections.media.subtitle') }}</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('forms.trip.fields.hero_image.label') }}
                            </label>
                            <ImageUploader v-model="form.heroImage" preview-size="large"
                                :label="form.heroImage ? t('forms.trip.fields.hero_image.change') : t('forms.trip.fields.hero_image.select')"
                                :feedback="form.errors.heroImage" />
                            <p class="mt-2 text-xs text-gray-700/30">
                                {{ t('forms.trip.fields.hero_image.help') }}
                            </p>
                        </div>

                        <div v-if="form.heroImage">
                            <p class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('forms.trip.fields.hero_focus.label') }}
                            </p>
                            <FocusPointPicker v-model="form.hero_focus" :image="form.heroImage" :options="heroFocusOptions"
                                :label="t('forms.trip.fields.hero_focus.label')" :feedback="form.errors.hero_focus" />
                            <p class="mt-2 text-xs text-gray-700/30">
                                {{ t('forms.trip.fields.hero_focus.help') }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('forms.trip.fields.gallery.label') }}
                            </label>
                            <ImageUploader v-model="form.images" :multiple="true"
                                :label="t('forms.trip.fields.gallery.drop_label')" :feedback="form.errors.images" />

                            <p class="mt-2 text-xs text-gray-700/30">
                                {{ t('forms.trip.fields.gallery.help') }}
                            </p>
                        </div>

                        <div v-if="sectionOptions.length" class="space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-brand-text">
                                    {{ t('forms.trip.fields.section_images.label') }}
                                </h3>
                                <p class="mt-1 text-xs text-brand-light">
                                    {{ t('forms.trip.fields.section_images.help') }}
                                </p>
                            </div>
                            <p v-if="!sectionImageOptions.length" class="text-sm text-brand-light">
                                {{ t('forms.trip.fields.section_images.empty') }}
                            </p>
                            <template v-else>
                                <fieldset v-for="section in sectionOptions" :key="section.id">
                                    <legend class="mb-2 text-sm text-brand-text">{{ section.name }}</legend>
                                    <div class="flex flex-wrap gap-3">
                                        <label class="cursor-pointer">
                                            <input type="radio" class="peer sr-only" :name="`section_image_${section.id}`"
                                                :checked="!hasSectionImage(section.id)"
                                                @change="setSectionImage(section.id, null)" />
                                            <span
                                                class="flex items-center justify-center w-24 h-16 px-2 rounded-lg border border-brand-primary/20 text-center text-xs text-brand-light peer-checked:border-brand-primary peer-checked:ring-2 peer-checked:ring-brand-primary peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-brand-accent">
                                                {{ t('forms.trip.fields.section_images.none') }}
                                            </span>
                                        </label>
                                        <label v-for="{ image, position } in sectionImageOptions" :key="image.id"
                                            class="cursor-pointer">
                                            <input type="radio" class="peer sr-only" :name="`section_image_${section.id}`"
                                                :checked="isSectionImage(section.id, image.id)"
                                                @change="setSectionImage(section.id, image.id)" />
                                            <ResponsiveImage :image="image" sizes="96px" loading="lazy"
                                                :alt="t('forms.trip.fields.section_images.image_alt', { position })"
                                                class="w-24 h-16 object-cover rounded-lg opacity-70 peer-checked:opacity-100 peer-checked:ring-2 peer-checked:ring-brand-primary peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-brand-accent" />
                                        </label>
                                    </div>
                                    <FormFeedback :message="form.errors[`section_images.${section.id}`]" />
                                </fieldset>
                            </template>
                        </div>
                    </div>
                </section>
            </div>

            <div class="laptop:col-start-3 space-y-8">
                <!-- Settings & Configuration Section -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">{{ t('forms.trip.sections.settings.title') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-700/30">{{ t('forms.trip.sections.settings.subtitle') }}</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <div class="grid grid-cols-1 gap-4">
                                <div class="p-4 bg-white rounded-lg border border-gray-200">
                                    <Label for="published_at" :required="true">{{
                                        t('forms.trip.fields.published_at.label')
                                        }}</Label>
                                    <DatePicker v-model="form.published_at" :feedback="form.errors.published_at" />
                                    <span class="block text-xs text-gray-700/30 mt-2">
                                        {{ t('forms.trip.fields.published_at.help') }}
                                    </span>
                                </div>
                                <div class="flex items-center p-4 bg-white rounded-lg border border-gray-200">
                                    <Checkbox v-model="form.featured" name="featured" class="flex-1">
                                        <span class="font-medium text-gray-700">{{ t('forms.trip.fields.featured.label')
                                            }}</span>
                                        <span class="block text-xs text-gray-700/30 mt-1">
                                            {{ t('forms.trip.fields.featured.help') }}
                                        </span>
                                    </Checkbox>
                                </div>
                                <div class="p-4 bg-white rounded-lg border border-gray-200">
                                    <Label for="min_advance_value">
                                        {{ t('forms.trip.fields.min_advance.label') }}
                                    </Label>
                                    <div class="flex gap-2 mt-1">
                                        <Input type="number" name="min_advance_value" v-model="minAdvanceValue"
                                            :min="0" class="w-24"
                                            :feedback="form.errors.min_advance_days" />
                                        <Select name="min_advance_unit" v-model="minAdvanceUnit" :show-label="false"
                                            :options="advanceUnitOptions" />
                                    </div>
                                    <span class="block text-xs text-gray-700/30 mt-2">
                                        {{ t('forms.trip.fields.min_advance.help') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Pricing & Duration Section -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">{{ t('forms.trip.sections.pricing.title') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-700/30">{{ t('forms.trip.sections.pricing.subtitle') }}</p>
                    </div>
                    <div class="p-6">
                        <TripPricesManager v-model="form.prices" :errors="form.errors"
                            :price-label-options="priceLabelOptions" />
                    </div>
                </section>

                <!-- Transport -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">{{ t('forms.trip.sections.transport.label') }} *
                        </h2>
                        <p class="mt-1 text-sm text-gray-700/30">{{ t('forms.trip.sections.transport.help') }}</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <Select name="transport" :show-label="false" v-model="form.transport" :multiple="true"
                                :required="false" :options="transportOptions" :feedback="form.errors.transport"
                                :placeholder="t('forms.trip.sections.transport.placeholder')" />
                        </div>
                    </div>
                </section>

                <!-- Linked destinations -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">{{ t('forms.trip.sections.destinations.title')
                            }} *</h2>
                        <p class="mt-1 text-sm text-gray-700/30">{{ t('forms.trip.sections.destinations.subtitle') }}
                        </p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <Select name="destination" v-model="form.destinations" :multiple="true" :required="true"
                                :options="destinationOptions" :feedback="form.errors.destinations"
                                :placeholder="t('forms.trip.fields.destinations.placeholder')" />
                            <p class="mt-2 text-xs text-gray-700/30">
                                {{ t('forms.trip.fields.destinations.help') }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Availability / Blocked Dates -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">{{ t('forms.trip.sections.availability.title')
                            }}</h2>
                        <p class="mt-1 text-sm text-gray-700/30">{{ t('forms.trip.sections.availability.subtitle') }}
                        </p>
                    </div>
                    <div class="p-6">
                        <BlockedDatesManager :modelValue="form.blocked_dates" :errors="form.errors"
                            @update:modelValue="val => { form.blocked_dates = { dates: val.dates ?? [], weekdays: val.weekdays ?? [] } }" />
                    </div>
                </section>

            </div>

        </div>
        <!-- Footer Actions -->
        <FormFooter :form="form" :label="form.id ? t('forms.trip.submit.update') : t('forms.trip.submit.create')"
            @submit="emit('submit')" />

    </form>
</template>
