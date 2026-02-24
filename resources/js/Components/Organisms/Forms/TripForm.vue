<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
import { useCharacterCounter } from '@/Composables/useCharacterCounter.js';
import { useI18n } from 'vue-i18n';
import TripItemsTab from './TripItemsTab.vue';

const emit = defineEmits(['submit']);
const props = defineProps({
    destinations: Object,
    form: Object,
    typeOptions: Object,
    categoryOptions: Object,
    practicalSections: Object,
});

const destinationOptions = computed(() => (
    props.destinations?.map((d) => (
        { 'id': d.id, 'name': d.region || d.name }
    )).sort((a, b) => a.name.localeCompare(b.name))
))

const { t } = useI18n();

const imageErrors = computed(() =>
    Object.keys(props.form.errors)
        .filter((key) => key.startsWith('images.'))
        .map((key) => props.form.errors[key])
);
const page = usePage();
const seoConfig = page.props.config?.seo || {}

const META_TITLE_MAX_LENGTH = seoConfig.meta_title_max_length || 0
const META_DESCRIPTION_MAX_LENGTH = seoConfig.meta_description_max_length || 0

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
                                <div class="grid grid-cols-1 laptop:grid-cols-2 gap-6">
                                    <Input type="text" name="name" :label="t('forms.trip.fields.name.label')" :required="true"
                                        v-model="form.name" :feedback="form.errors.name"
                                        :placeholder="t('forms.trip.fields.name.placeholder')" />
                                    <Input type="text" name="slug" :label="t('forms.trip.fields.slug.label')" :required="true" v-model="form.slug"
                                        :feedback="form.errors.slug" :placeholder="t('forms.trip.fields.slug.placeholder')" />
                                </div>
                                <TextArea name="description" :label="t('forms.trip.fields.description.label')" :required="true"
                                    v-model="form.description" :feedback="form.errors.description"
                                    :placeholder="t('forms.trip.fields.description.placeholder')" :rows="6" />
                                <DynamicInputList :items="form.highlights" name="highlights" :label="t('forms.trip.fields.highlights.label')" :placeholder="t('forms.trip.fields.highlights.placeholder')" :feedback="form.errors" />
                            </TabPanel>

                            <TabPanel class="p-6">
                                <TripItemsTab :form="form" :type-options="typeOptions" :category-options="categoryOptions" />
                            </TabPanel>

                            <TabPanel class="p-6 space-y-6">
                                <template v-for="(label, key) in practicalSections" :key="key">
                                    <TextArea
                                        :name="`practical_info.${key}`"
                                        :label="label"
                                        v-model="form.practical_info[key]"
                                        :feedback="form.errors[`practical_info.${key}`]"
                                        :rows="6"
                                    />
                                </template>
                            </TabPanel>

                            <TabPanel class="p-6 space-y-6">
                                <div>
                                    <Input type="text" name="meta_title" :label="t('forms.trip.fields.meta_title.label')" v-model="form.meta_title"
                                        :feedback="form.errors.meta_title"
                                        :placeholder="t('forms.trip.fields.meta_title.placeholder')" />
                                    <div class="mt-2 flex items-center justify-between text-xs">
                                        <span :class="metaTitleClass">
                                            {{ t('forms.trip.fields.meta_title.characters', { current: metaTitleLength, max: META_TITLE_MAX_LENGTH }) }}
                                        </span>
                                        <span v-if="metaTitleCharsLeft < 0" class="text-status-error font-semibold">
                                            {{ t('forms.trip.character_counter.too_many', { count: Math.abs(metaTitleCharsLeft) }) }}
                                        </span>
                                        <span v-else-if="metaTitleCharsLeft <= 10" :class="metaTitleClass">
                                            {{ t('forms.trip.character_counter.remaining', { count: metaTitleCharsLeft }) }}
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <TextArea name="meta_description" :label="t('forms.trip.fields.meta_description.label')"
                                        v-model="form.meta_description" :feedback="form.errors.meta_description"
                                        :placeholder="t('forms.trip.fields.meta_description.placeholder')"
                                        :rows="4" />
                                    <div class="mt-2 flex items-center justify-between text-xs">
                                        <span :class="metaDescriptionClass">
                                            {{ t('forms.trip.fields.meta_description.characters', { current: metaDescriptionLength, max: META_DESCRIPTION_MAX_LENGTH }) }}
                                        </span>
                                        <span v-if="metaDescriptionCharsLeft < 0" class="text-status-error font-semibold">
                                            {{ t('forms.trip.character_counter.too_many', { count: Math.abs(metaDescriptionCharsLeft) }) }}
                                        </span>
                                        <span v-else-if="metaDescriptionCharsLeft <= 20" :class="metaDescriptionClass">
                                            {{ t('forms.trip.character_counter.remaining', { count: metaDescriptionCharsLeft }) }}
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
                        <h2 class="text-lg font-semibold text-gray-700">{{ t('forms.trip.tabs.media') }}</h2>
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

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('forms.trip.fields.gallery.label') }}
                            </label>
                            <ImageUploader v-model="form.images" :multiple="true"
                                :label="t('forms.trip.fields.gallery.drop_label')" :feedback="imageErrors" />

                            <p class="mt-2 text-xs text-gray-700/30">
                                {{ t('forms.trip.fields.gallery.help') }}
                            </p>
                        </div>
                    </div>
                </section>
            </div>

            <div class="laptop:col-start-3 space-y-8">
                <!-- Settings & Configuration Section -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">{{ t('forms.trip.sections.settings.title') }}</h2>
                        <p class="mt-1 text-sm text-gray-700/30">{{ t('forms.trip.sections.settings.subtitle') }}</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <div class="grid grid-cols-1 gap-4">
                                <div class="p-4 bg-white rounded-lg border border-gray-200">
                                    <Label for="published_at" :required="true">{{ t('forms.trip.fields.published_at.label') }}</Label>
                                    <DatePicker v-model="form.published_at" :feedback="form.errors.published_at" />
                                    <span class="block text-xs text-gray-700/30 mt-2">
                                        {{ t('forms.trip.fields.published_at.help') }}
                                    </span>
                                </div>
                                <div class="flex items-center p-4 bg-white rounded-lg border border-gray-200">
                                    <Checkbox v-model="form.featured" name="featured" class="flex-1">
                                        <span class="font-medium text-gray-700">{{ t('forms.trip.fields.featured.label') }}</span>
                                        <span class="block text-xs text-gray-700/30 mt-1">
                                            {{ t('forms.trip.fields.featured.help') }}
                                        </span>
                                    </Checkbox>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Pricing & Duration Section -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">{{ t('forms.trip.sections.pricing.title') }}</h2>
                        <p class="mt-1 text-sm text-gray-700/30">{{ t('forms.trip.sections.pricing.subtitle') }}</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6">
                            <Input type="text" name="price" :label="t('forms.trip.fields.price.label')" :required="true"
                                v-model="form.price" :feedback="form.errors.price" :placeholder="t('forms.trip.fields.price.placeholder')">
                            <template #prefix>
                                <span class="text-gray-700/30">€</span>
                            </template>
                            </Input>
                            <Input type="number" name="duration" :label="t('forms.trip.fields.duration.label')" :required="true" v-model="form.duration"
                                :feedback="form.errors.duration" :placeholder="t('forms.trip.fields.duration.placeholder')">
                            <template #suffix>
                                <span class="text-gray-700/30">{{ t('forms.trip.fields.duration.unit') }}</span>
                            </template>
                            </Input>
                        </div>
                    </div>
                </section>

                <!-- Availability / Blocked Dates -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">{{ t('forms.trip.sections.availability.title') }}</h2>
                        <p class="mt-1 text-sm text-gray-700/30">{{ t('forms.trip.sections.availability.subtitle') }}</p>
                    </div>
                    <div class="p-6">
                        <BlockedDatesManager
                            :modelValue="form.blocked_dates"
                            :errors="form.errors"
                            @update:modelValue="val => { form.blocked_dates = { dates: val.dates ?? [], weekdays: val.weekdays ?? [] } }"
                        />
                    </div>
                </section>

                <!-- Linked destinations -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">{{ t('forms.trip.sections.destinations.title') }}</h2>
                        <p class="mt-1 text-sm text-gray-700/30">{{ t('forms.trip.sections.destinations.subtitle') }}</p>
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
            </div>

        </div>
        <!-- Footer Actions -->
        <FormFooter :form="form" :label="form.id ? t('forms.trip.submit.update') : t('forms.trip.submit.create')" @submit="emit('submit')" />

    </form>
</template>
