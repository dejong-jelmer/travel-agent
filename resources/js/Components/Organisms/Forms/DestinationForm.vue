<script setup>
import { useI18n } from 'vue-i18n';

const props = defineProps({
    form: Object,
    travelInfoSections: Object,
    countries: Array,
});

const emit = defineEmits(['submit']);
const { t } = useI18n();
</script>

<template>
    <form @submit.prevent="emit('submit')" class="max-w-wide mx-auto">
        <div class="grid grid-cols-1 laptop:grid-cols-2 gap-8">
            <!-- Header Section -->
            <div class="laptop:col-span-2 bg-white py-10">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-700">
                            {{ form.id ? t('forms.destination.edit_heading') : t('forms.destination.new_heading') }}
                        </h1>
                        <p class="mt-1 text-sm text-gray-700/50">
                            {{ t('forms.destination.subheading') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="space-y-8">
                <!-- Basic Information Section -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">{{ t('forms.destination.sections.basic.title')
                            }}</h2>
                        <p class="mt-1 text-sm text-gray-700/30">{{ t('forms.destination.sections.basic.subtitle') }}
                        </p>
                    </div>
                    <div class="p-6 space-y-6">
                        <CountrySelect v-model="form.country_code" :label="t('forms.destination.fields.country.label')"
                            :required="true" :feedback="form.errors.country_code"
                            :placeholder="t('forms.destination.fields.country.placeholder')" :options="countries" />

                        <Input type="text" name="region" :label="t('forms.destination.fields.region.label')"
                            :required="false" v-model="form.region" :feedback="form.errors.region"
                            :placeholder="t('forms.destination.fields.region.placeholder')" />
                        <p class="text-xs text-gray-700/30">
                            {{ t('forms.destination.fields.region.help') }}
                        </p>
                    </div>
                </section>
            </div>
            <div class="space-y-8">
                <!-- Travel Info Section -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">{{
                            t('forms.destination.sections.travel_info.title') }}</h2>
                        <p class="mt-1 text-sm text-gray-700/30">{{ t('forms.destination.sections.travel_info.subtitle')
                            }}</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <template v-if="!form.region">
                            <div v-for="(label, key) in travelInfoSections" :key="key">
                                <TextArea :name="`travel_info.${key}`" :label="label" v-model="form.travel_info[key]"
                                    :feedback="form.errors[`travel_info.${key}`]"
                                    :placeholder="t('forms.destination.fields.travel_info.placeholder', { section: label })"
                                    :rows="4" />
                            </div>
                        </template>
                        <template v-else>
                            {{ t('forms.destination.travel_info_reference') }}
                        </template>
                    </div>
                </section>
            </div>
        </div>

        <!-- Footer Actions -->
        <FormFooter :form="form"
            :label="form.id ? t('forms.destination.submit.update') : t('forms.destination.submit.create')"
            @submit="emit('submit')" />
    </form>
</template>
