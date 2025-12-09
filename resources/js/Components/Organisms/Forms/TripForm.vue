<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
import { useCharacterCounter } from '@/Composables/useCharacterCounter.js';

const emit = defineEmits(['submit']);
const props = defineProps({
    countries: Object,
    form: Object,
});

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
                            {{ form.id ? 'Bewerk Reis' : 'Nieuwe Reis' }}
                        </h1>
                        <p class="mt-1 text-sm text-gray-700/50">
                            Beheer de details van uw reis
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
                                        Reis Details
                                    </div>
                                </Tab>
                                <Tab v-slot="{ selected }" class="outline-none">
                                    <div class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer"
                                        :class="selected
                                            ? 'border-primary-default text-primary-default'
                                            : 'border-transparent text-gray-700/50 hover:text-gray-700 hover:border-gray-300'">
                                        Meta data
                                    </div>
                                </Tab>
                            </TabList>
                        </div>

                        <TabPanels>
                            <TabPanel class="p-6 space-y-6">
                                <div class="grid grid-cols-1 laptop:grid-cols-2 gap-6">
                                    <Input type="text" name="name" label="Naam van de reis" :required="true"
                                        v-model="form.name" :feedback="form.errors.name"
                                        placeholder="Bijv. Venetië Express Treinreis" />
                                    <Input type="text" name="slug" label="URL Slug" :required="true" v-model="form.slug"
                                        :feedback="form.errors.slug" placeholder="venetie-express-treinreis" />
                                </div>
                                <TextArea name="description" label="Omschrijving" :required="true"
                                    v-model="form.description" :feedback="form.errors.description"
                                    placeholder="Beschrijf de reis en wat reizigers kunnen verwachten..." :rows="6" />
                            </TabPanel>

                            <TabPanel class="p-6 space-y-6">
                                <div>
                                    <Input type="text" name="meta_title" label="Meta Title" v-model="form.meta_title"
                                        :feedback="form.errors.meta_title"
                                        placeholder="SEO-vriendelijke titel voor zoekmachines" />
                                    <div class="mt-2 flex items-center justify-between text-xs">
                                        <span :class="metaTitleClass">
                                            {{ metaTitleLength }} / {{ META_TITLE_MAX_LENGTH }} karakters
                                        </span>
                                        <span v-if="metaTitleCharsLeft < 0" class="text-status-error font-semibold">
                                            {{ Math.abs(metaTitleCharsLeft) }} te veel
                                        </span>
                                        <span v-else-if="metaTitleCharsLeft <= 10" :class="metaTitleClass">
                                            Nog {{ metaTitleCharsLeft }} over
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <TextArea name="meta_description" label="Meta Description"
                                        v-model="form.meta_description" :feedback="form.errors.meta_description"
                                        placeholder="Korte beschrijving voor zoekmachine resultaten (max 160 karakters)"
                                        :rows="4" />
                                    <div class="mt-2 flex items-center justify-between text-xs">
                                        <span :class="metaDescriptionClass">
                                            {{ metaDescriptionLength }} / {{ META_DESCRIPTION_MAX_LENGTH }} karakters
                                        </span>
                                        <span v-if="metaDescriptionCharsLeft < 0" class="text-status-error font-semibold">
                                            {{ Math.abs(metaDescriptionCharsLeft) }} te veel
                                        </span>
                                        <span v-else-if="metaDescriptionCharsLeft <= 20" :class="metaDescriptionClass">
                                            Nog {{ metaDescriptionCharsLeft }} over
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
                        <h2 class="text-lg font-semibold text-gray-700">Media</h2>
                        <p class="mt-1 text-sm text-gray-700/30">Upload afbeeldingen voor de reis</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Hero Afbeelding
                            </label>
                            <ImageUploader v-model="form.heroImage" preview-size="large"
                                :label="form.heroImage ? 'Wijzig hoofdafbeelding' : 'Selecteer een afbeelding'"
                                :feedback="form.errors.heroImage" />
                            <p class="mt-2 text-xs text-gray-700/30">
                                Deze afbeelding wordt gebruikt als hoofdafbeelding in overzichten
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Galerij Afbeeldingen
                            </label>
                            <ImageUploader v-model="form.images" :multiple="true"
                                label="Sleep meerdere afbeeldingen hierheen" :feedback="imageErrors" />

                            <p class="mt-2 text-xs text-gray-700/30">
                                Upload meerdere afbeeldingen voor de reisgalerij
                            </p>
                        </div>
                    </div>
                </section>
            </div>

            <div class="laptop:col-start-3 space-y-8">
                <!-- Settings & Configuration Section -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">Instellingen</h2>
                        <p class="mt-1 text-sm text-gray-700/30">Configureer zichtbaarheid</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <div class="grid grid-cols-1 gap-4">
                                <div class="p-4 bg-white rounded-lg border border-gray-200">
                                    <Label for="published_at" :required="true">Publiseer datum</Label>
                                    <DatePicker v-model="form.published_at" :feedback="form.errors.published_at" />
                                    <span class="block text-xs text-gray-700/30 mt-2">
                                        Vanaf wanneer de reis zichtbaar is op de website
                                    </span>
                                </div>
                                <div class="flex items-center p-4 bg-white rounded-lg border border-gray-200">
                                    <Checkbox v-model="form.featured" name="featured" class="flex-1">
                                        <span class="font-medium text-gray-700">Uitgelicht</span>
                                        <span class="block text-xs text-gray-700/30 mt-1">
                                            Toon op homepage in de uitgelichte reizen
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
                        <h2 class="text-lg font-semibold text-gray-700">Prijs & Duur</h2>
                        <p class="mt-1 text-sm text-gray-700/30">Definieer de kosten en reisduur</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6">
                            <Input type="text" name="price" label="Prijs per persoon" :required="true"
                                v-model="form.price" :feedback="form.errors.price" placeholder="€ 1.295">
                            <template #prefix>
                                <span class="text-gray-700/30">€</span>
                            </template>
                            </Input>
                            <Input type="number" name="duration" label="Duur" :required="true" v-model="form.duration"
                                :feedback="form.errors.duration" placeholder="7">
                            <template #suffix>
                                <span class="text-gray-700/30">dagen</span>
                            </template>
                            </Input>
                        </div>
                    </div>
                </section>

                <!-- Linked countries -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">Landen</h2>
                        <p class="mt-1 text-sm text-gray-700/30">Configureer gekoppelde landen</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <Select name="country" v-model="form.countries" :multiple="true" :required="true"
                                :options="countries" :feedback="form.errors.countries"
                                :placeholder="'Selecteer één of meerdere landen'" />
                            <p class="mt-2 text-xs text-gray-700/30">
                                Selecteer de landen die bezocht worden tijdens deze reis
                            </p>
                        </div>
                    </div>
                </section>
            </div>

        </div>
        <!-- Footer Actions -->
        <FormFooter :form="form" label="Reis opslaan" @submit="emit('submit')" />

    </form>
</template>
