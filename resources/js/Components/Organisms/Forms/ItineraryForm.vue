<script setup>
import { useI18n } from 'vue-i18n';

const props = defineProps({
    form: Object,
    meals: Object,
    transport: Object,
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
                            {{ form.id ? t('forms.itinerary.edit_heading') : t('forms.itinerary.new_heading') }}
                        </h1>
                        <p class="mt-1 text-sm text-gray-700/50">
                            {{ t('forms.itinerary.subheading') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Linkerkolom: Basis Informatie -->
            <div class="space-y-8">
                <!-- Basic Information Section -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">{{ t('forms.itinerary.tabs.basic') }}</h2>
                        <p class="mt-1 text-sm text-gray-700/30">{{ t('forms.itinerary.sections.basic.subtitle') }}</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <Input type="text" name="title" :label="t('forms.itinerary.fields.title.label')" :required="true" v-model="form.title"
                            :feedback="form.errors.title" :placeholder="t('forms.itinerary.fields.title.placeholder')" />
                        <Input type="text" name="location" :label="t('forms.itinerary.fields.location.label')" :required="true" v-model="form.location"
                            :feedback="form.errors.location" :placeholder="t('forms.itinerary.fields.location.placeholder')" />
                        <TextArea name="description" :label="t('forms.itinerary.fields.description.label')" :required="true" v-model="form.description"
                            :feedback="form.errors.description" :placeholder="t('forms.itinerary.fields.description.placeholder')"
                            :rows="6" />
                        <Input type="text" name="remark" :label="t('forms.itinerary.fields.remark.label')" :required="false" v-model="form.remark"
                            :feedback="form.errors.remark" :placeholder="t('forms.itinerary.fields.remark.placeholder')" />
                        <p class="text-xs text-gray-700/30">
                            {{ t('forms.itinerary.fields.remark.help') }}
                        </p>
                    </div>
                </section>
                <!-- Media Section -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">{{ t('forms.itinerary.tabs.media') }}</h2>
                        <p class="mt-1 text-sm text-gray-700/30">{{ t('forms.itinerary.sections.media.subtitle') }}</p>
                    </div>
                    <div class="p-6">
                        <ImageUploader v-model="form.image" preview-size="large"
                            :label="form.image ? t('forms.itinerary.fields.image.change') : t('forms.itinerary.fields.image.select')"
                            :feedback="form.errors.image" />
                        <p class="mt-2 text-xs text-gray-700/30">
                            {{ t('forms.itinerary.fields.image.help') }}
                        </p>
                    </div>
                </section>
            </div>

            <!-- Rechterkolom: Media -->
            <div class="space-y-8">
                <!-- Details Section -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">{{ t('forms.itinerary.tabs.details') }}</h2>
                        <p class="mt-1 text-sm text-gray-700/30">{{ t('forms.itinerary.sections.details.subtitle') }}</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <Input type="text" name="accommodation" :label="t('forms.itinerary.fields.accommodation.label')" :required="true"
                            v-model="form.accommodation" :feedback="form.errors.accommodation"
                            :placeholder="t('forms.itinerary.fields.accommodation.placeholder')" />
                        <Input type="text" name="activities" :label="t('forms.itinerary.fields.activities.label')" :required="true"
                            v-model="form.activities" :feedback="form.errors.activities"
                            :placeholder="t('forms.itinerary.fields.activities.placeholder')" />
                        <Select name="transport" :label="t('forms.itinerary.fields.transport.label')" v-model="form.transport" :multiple="true"
                            :required="false" :options="transport" :feedback="form.errors.transport"
                            :placeholder="t('forms.itinerary.fields.transport.placeholder')" />
                        <Select name="meals" :label="t('forms.itinerary.fields.meals.label')" v-model="form.meals" :multiple="true" :required="false"
                            :options="meals" :feedback="form.errors.meals"
                            :placeholder="t('forms.itinerary.fields.meals.placeholder')" />
                    </div>
                </section>
            </div>
        </div>

        <!-- Footer Actions -->
        <FormFooter :form="form" :label="form.id ? t('forms.itinerary.submit.update') : t('forms.itinerary.submit.create')" @submit="emit('submit')" />
    </form>
</template>
