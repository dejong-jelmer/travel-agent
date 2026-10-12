<script setup>
import { computed, reactive } from 'vue';
import { useI18n } from 'vue-i18n';
import { useCharacterCounter } from '@/Composables/useCharacterCounter.js';

const props = defineProps({
    form: Object,
    // Shape: [{ id, name }], the trips by name, ids as strings
    tripOptions: Array,
    // Shape: [{ id, name }], the relationships (TestimonialRelationship::options())
    relationshipOptions: Array,
    // Shape: { quote, body, author_context }, the maximum lengths of these fields
    limits: Object,
});

const emit = defineEmits(['submit']);
const { t } = useI18n();

// An empty trip makes the testimonial a general one
const tripSelectOptions = computed(() => [
    { id: '', name: t('forms.testimonial.fields.trip.general') },
    ...props.tripOptions,
]);

// A personal story must say who wrote it, so it never passes for a customer review
const contextRequired = computed(() => props.form.relationship === 'personal');

const quoteCounter = reactive(useCharacterCounter(computed(() => props.form.quote), props.limits.quote));
const bodyCounter = reactive(useCharacterCounter(computed(() => props.form.body), props.limits.body));
</script>

<template>
    <form @submit.prevent="emit('submit')" class="max-w-wide mx-auto">
        <div class="grid grid-cols-1 laptop:grid-cols-2 gap-8">
            <!-- Header Section -->
            <div class="laptop:col-span-2 bg-white py-10">
                <h1 class="text-3xl font-bold text-gray-700">
                    {{ form.id ? t('forms.testimonial.edit_heading') : t('forms.testimonial.new_heading') }}
                </h1>
                <p class="mt-1 text-sm text-gray-700/50">
                    {{ t('forms.testimonial.subheading') }}
                </p>
            </div>

            <!-- Left column: quote, account and link -->
            <div class="space-y-8">
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">{{ t('forms.testimonial.sections.content.title') }}</h2>
                        <p class="mt-1 text-sm text-gray-700/30">{{ t('forms.testimonial.sections.content.subtitle') }}</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <Select name="trip_id" :label="t('forms.testimonial.fields.trip.label')" v-model="form.trip_id"
                            :options="tripSelectOptions" :feedback="form.errors.trip_id" />

                        <div>
                            <TextArea name="quote" :label="t('forms.testimonial.fields.quote.label')" :required="true"
                                v-model="form.quote" :feedback="form.errors.quote" :rows="4"
                                :placeholder="t('forms.testimonial.fields.quote.placeholder')" />
                            <div class="mt-2 flex items-center justify-between text-xs">
                                <span :class="quoteCounter.counterClass">
                                    {{ t('forms.testimonial.character_counter.current', { current: quoteCounter.length, max: limits.quote }) }}
                                </span>
                                <span v-if="quoteCounter.isOverLimit" class="text-status-error font-semibold">
                                    {{ t('forms.testimonial.character_counter.too_many', { count: -quoteCounter.charsLeft }) }}
                                </span>
                                <span v-else-if="quoteCounter.isCritical" :class="quoteCounter.counterClass">
                                    {{ t('forms.testimonial.character_counter.remaining', { count: quoteCounter.charsLeft }) }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <TextArea name="body" :label="t('forms.testimonial.fields.body.label')" v-model="form.body"
                                :feedback="form.errors.body" :rows="12"
                                :description="t('forms.testimonial.fields.body.help')" />
                            <div class="mt-2 flex items-center justify-between text-xs">
                                <span :class="bodyCounter.counterClass">
                                    {{ t('forms.testimonial.character_counter.current', { current: bodyCounter.length, max: limits.body }) }}
                                </span>
                                <span v-if="bodyCounter.isOverLimit" class="text-status-error font-semibold">
                                    {{ t('forms.testimonial.character_counter.too_many', { count: -bodyCounter.charsLeft }) }}
                                </span>
                                <span v-else-if="bodyCounter.isCritical" :class="bodyCounter.counterClass">
                                    {{ t('forms.testimonial.character_counter.remaining', { count: bodyCounter.charsLeft }) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">{{ t('forms.testimonial.sections.link.title') }}</h2>
                        <p class="mt-1 text-sm text-gray-700/30">{{ t('forms.testimonial.sections.link.subtitle') }}</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <Input type="url" name="link_url" :label="t('forms.testimonial.fields.link_url.label')"
                            v-model="form.link_url" :feedback="form.errors.link_url"
                            :placeholder="t('forms.testimonial.fields.link_url.placeholder')" />
                        <Input type="text" name="link_label" :label="t('forms.testimonial.fields.link_label.label')"
                            v-model="form.link_label" :feedback="form.errors.link_label"
                            :placeholder="t('forms.testimonial.fields.link_label.placeholder')" />
                    </div>
                </section>
            </div>

            <!-- Right column: author, photo and publication -->
            <div class="space-y-8">
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">{{ t('forms.testimonial.sections.author.title') }}</h2>
                        <p class="mt-1 text-sm text-gray-700/30">{{ t('forms.testimonial.sections.author.subtitle') }}</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <Input type="text" name="author_name" :label="t('forms.testimonial.fields.author_name.label')"
                            :required="true" v-model="form.author_name" :feedback="form.errors.author_name" />
                        <Select name="relationship" :label="t('forms.testimonial.fields.relationship.label')"
                            :required="true" v-model="form.relationship" :options="relationshipOptions"
                            :placeholder="t('forms.testimonial.fields.relationship.placeholder')"
                            :feedback="form.errors.relationship" />
                        <div>
                            <Input type="text" name="author_context" :label="t('forms.testimonial.fields.author_context.label')"
                                :required="contextRequired" :maxlength="limits.author_context" v-model="form.author_context"
                                :feedback="form.errors.author_context"
                                :placeholder="t('forms.testimonial.fields.author_context.placeholder')" />
                            <p class="mt-2 text-xs text-gray-700/30">
                                {{ t('forms.testimonial.fields.author_context.help') }}
                            </p>
                        </div>
                        <div>
                            <Label for-field="travelled_on">{{ t('forms.testimonial.fields.travelled_on.label') }}</Label>
                            <DatePicker v-model="form.travelled_on" month-picker :feedback="form.errors.travelled_on"
                                class="mt-1" />
                        </div>
                    </div>
                </section>

                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">{{ t('forms.testimonial.sections.photo.title') }}</h2>
                        <p class="mt-1 text-sm text-gray-700/30">{{ t('forms.testimonial.sections.photo.subtitle') }}</p>
                    </div>
                    <div class="p-6">
                        <ImageUploader v-model="form.photo" :feedback="form.errors.photo" />
                    </div>
                </section>

                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">{{ t('forms.testimonial.sections.publication.title') }}</h2>
                    </div>
                    <div class="p-6">
                        <Checkbox v-model="form.is_published" :feedback="form.errors.is_published">
                            {{ t('forms.testimonial.fields.is_published.label') }}
                        </Checkbox>
                        <p class="mt-2 text-xs text-gray-700/30">
                            {{ t('forms.testimonial.fields.is_published.help') }}
                        </p>
                    </div>
                </section>
            </div>
        </div>

        <!-- Footer Actions -->
        <FormFooter :form="form"
            :label="form.id ? t('forms.testimonial.submit.update') : t('forms.testimonial.submit.create')"
            @submit="emit('submit')" />
    </form>
</template>
