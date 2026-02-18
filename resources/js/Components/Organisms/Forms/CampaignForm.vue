<script setup>
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
import { Check, Clock } from 'lucide-vue-next';
import { useCharacterCounter } from '@/Composables/useCharacterCounter.js';
import { useI18n } from 'vue-i18n';

const emit = defineEmits(['submit']);

const props = defineProps({
    form: Object,
    trips: Object,
    statusOptions: Object
});

const { t } = useI18n();

const PREVIEW_TEXT_MAX_LENGTH = 255;

const {
    length: previewTextLength,
    charsLeft: previewTextCharsLeft,
    counterClass: previewTextClass
} = useCharacterCounter(
    computed(() => props.form?.preview_text),
    PREVIEW_TEXT_MAX_LENGTH
);

const sendingTest = ref(false);

function sendTestEmail() {
    if (!props.form?.id) {
        return;
    }

    sendingTest.value = true;

    router.post(
        route('admin.newsletter.campaigns.send-test', props.form.id),
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                sendingTest.value = false;
            }
        }
    );
}

// Initialize trip_ids if not exists
if (!props.form.trip_ids) {
    props.form.trip_ids = [];
}

function toggleTrip(tripId) {
    const index = props.form.trips.indexOf(tripId);
    if (index > -1) {
        props.form.trips.splice(index, 1);
    } else {
        props.form.trips.push(tripId);
    }
}

function isTripSelected(tripId) {
    return props.form.trips.includes(tripId);
}

</script>

<template>
    <form @submit.prevent="submit" class="max-w-wide mx-auto">
        <div class="grid grid-cols-1 laptop:grid-cols-3 gap-8">
            <!-- Header Section -->
            <div class="laptop:col-span-3 bg-white py-10">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-700">
                            {{ form.id ? t('forms.campaign.edit_heading') : t('forms.campaign.new_heading') }}
                        </h1>
                        <p class="mt-1 text-sm text-gray-700/50">
                            {{ t('forms.campaign.subheading') }}
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
                                        {{ t('forms.campaign.tabs.basic') }}
                                    </div>
                                </Tab>
                                <Tab v-slot="{ selected }" class="outline-none">
                                    <div class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer"
                                        :class="selected
                                            ? 'border-primary-default text-primary-default'
                                            : 'border-transparent text-gray-700/50 hover:text-gray-700 hover:border-gray-300'">
                                        {{ t('forms.campaign.tabs.content') }}
                                    </div>
                                </Tab>
                            </TabList>
                        </div>

                        <TabPanels>
                            <TabPanel class="p-6 space-y-6">
                                <ImageUploader v-model="form.hero_image" preview-size="large"
                                    :label="form.hero_image ? t('forms.campaign.fields.hero_image.change') : t('forms.campaign.fields.hero_image.select')"
                                    :feedback="form.errors.hero_image" />

                                <Input type="text" name="subject" :label="t('forms.campaign.fields.subject.label')" :required="true"
                                    v-model="form.subject" :feedback="form.errors.subject"
                                    :placeholder="t('forms.campaign.fields.subject.placeholder')" />

                                <div>
                                    <Input type="text" name="preview_text" :label="t('forms.campaign.fields.preview_text.label')"
                                        :required="false" v-model="form.preview_text"
                                        :feedback="form.errors.preview_text"
                                        :placeholder="t('forms.campaign.fields.preview_text.placeholder')" />
                                    <div class="mt-2 flex items-center justify-between text-xs">
                                        <span :class="previewTextClass">
                                            {{ t('forms.campaign.character_counter.current', { current: previewTextLength, max: PREVIEW_TEXT_MAX_LENGTH }) }}
                                        </span>
                                        <span v-if="previewTextCharsLeft < 0" class="text-status-error font-semibold">
                                            {{ t('forms.campaign.character_counter.too_many', { count: Math.abs(previewTextCharsLeft) }) }}
                                        </span>
                                        <span v-else-if="previewTextCharsLeft <= 20" :class="previewTextClass">
                                            {{ t('forms.campaign.character_counter.remaining', { count: previewTextCharsLeft }) }}
                                        </span>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-700/30">
                                        {{ t('forms.campaign.fields.preview_text.help') }}
                                    </p>
                                </div>

                                <TextArea name="content" :label="t('forms.campaign.fields.body.label')" :required="true" v-model="form.content"
                                    :feedback="form.errors.content"
                                    :placeholder="t('forms.campaign.fields.body.placeholder')"
                                    :rows="15" />
                                <p class="text-xs text-gray-700/30">
                                    {{ t('forms.campaign.fields.body.help') }}
                                </p>

                                <!-- Featured Trips Section -->
                                <div class="border-t border-gray-200 pt-6">
                                    <Label>{{ t('forms.campaign.fields.featured_trips.label') }}</Label>
                                    <p class="text-xs text-gray-700/30 mb-4">
                                        {{ t('forms.campaign.fields.featured_trips.help') }}
                                    </p>

                                    <div v-if="trips && trips.length > 0"
                                        class="space-y-2 max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-3 bg-gray-50/30">
                                        <div v-for="trip in trips" :key="trip.id" @click="toggleTrip(trip.id)"
                                            class="flex items-center gap-3 p-3 bg-white rounded-lg border-2 transition-all cursor-pointer"
                                            :class="isTripSelected(trip.id)
                                                ? 'border-primary-default shadow-sm'
                                                : 'border-gray-100 hover:border-gray-200 hover:shadow-sm'">

                                            <!-- Checkbox -->
                                            <div class="flex-shrink-0 mt-1">
                                                <div class="w-5 h-5 flex items-center justify-center border-2 rounded transition-all"
                                                    :class="isTripSelected(trip.id)
                                                        ? 'bg-primary-default border-primary-default'
                                                        : 'bg-white border-gray-300'">
                                                    <Check v-if="isTripSelected(trip.id)" />
                                                </div>
                                            </div>

                                            <Thumbnail :imageUrl="trip.hero_image?.public_url || ''" :alt="trip.name" />
                                            <!-- Content -->
                                            <div class="flex-1 min-w-0">
                                                <h3 class="font-medium text-gray-900 mb-1">{{ trip.name }}</h3>
                                                <p class="text-sm text-gray-600 line-clamp-2 mb-2">
                                                    {{ trip.description }}
                                                </p>
                                                <div class="flex gap-3 text-xs text-gray-500">
                                                    <span v-if="trip.duration" class="flex items-center gap-1">
                                                        <Clock class="w-3.5 h-3.5" />
                                                        {{ t('forms.campaign.fields.featured_trips.duration', { days: trip.duration }) }}
                                                    </span>
                                                    |
                                                    <span v-if="trip.price_formatted" class="flex items-center gap-1 font-medium">
                                                        €{{ trip.price_formatted }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-else
                                        class="text-sm text-gray-700/30 italic p-4 bg-gray-50 rounded-lg border border-gray-200">
                                        {{ t('forms.campaign.fields.featured_trips.no_trips') }}
                                    </div>

                                    <p v-if="form.trips && form.trips.length > 0"
                                        class="text-xs text-gray-700/50 mt-3">
                                        {{ t('forms.campaign.fields.featured_trips.selected', { count: form.trips.length }) }}
                                    </p>
                                </div>
                            </TabPanel>

                            <TabPanel class="p-6 space-y-6">
                                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                                    <h3 class="text-sm font-semibold text-gray-700 mb-4">{{ t('forms.campaign.preview.title') }}</h3>

                                    <div class="bg-white rounded border border-gray-200 p-4 mb-4">
                                        <div class="text-xs text-gray-700/50 mb-2">{{ t('forms.campaign.preview.subject') }}</div>
                                        <div class="font-semibold text-gray-700 mb-3">
                                            {{ form.subject || t('forms.campaign.preview.no_subject') }}
                                        </div>

                                        <div v-if="form.preview_text" class="text-xs text-gray-700/50 mb-2">{{ t('forms.campaign.preview.preview_text') }}</div>
                                        <div v-if="form.preview_text" class="text-sm text-gray-700/70 mb-3">
                                            {{ form.preview_text }}
                                        </div>
                                    </div>

                                    <div class="bg-white rounded border border-gray-200 p-4 max-h-96 overflow-y-auto">
                                        <div class="text-xs text-gray-700/50 mb-2">{{ t('forms.campaign.preview.content_preview') }}</div>
                                        <div v-if="form.content" class="prose prose-sm max-w-none"
                                            v-html="form.content"></div>
                                        <div v-else class="text-gray-700/30 text-sm italic">
                                            {{ t('forms.campaign.preview.no_content') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <h4 class="text-sm font-semibold text-blue-900 mb-2">{{ t('forms.campaign.test.title') }}</h4>
                                    <p class="text-xs text-blue-700 mb-3">
                                        {{ t('forms.campaign.test.help') }}
                                    </p>
                                    <Button type="button" @click="sendTestEmail" :disabled="!form?.id || sendingTest">
                                        {{ sendingTest ? t('forms.campaign.test.sending') : t('forms.campaign.test.button') }}
                                    </Button>
                                    <p v-if="!form?.id" class="text-xs text-blue-600 mt-2">
                                        {{ t('forms.campaign.test.save_first') }}
                                    </p>
                                </div>
                            </TabPanel>
                        </TabPanels>
                    </TabGroup>
                </section>
            </div>

            <div class="laptop:col-start-3 space-y-8">
                <!-- Status & Scheduling Section -->
                <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">{{ t('forms.campaign.status.title') }}</h2>
                        <p class="mt-1 text-sm text-gray-700/30">{{ t('forms.campaign.status.subtitle') }}</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <Select name="status" :label="t('forms.campaign.fields.status.label')" v-model="form.status" :required="true"
                                :options="statusOptions" :feedback="form.errors.status"
                                :placeholder="t('forms.campaign.fields.status.placeholder')" />
                            <p class="mt-2 text-xs text-gray-700/30">
                                {{ t('forms.campaign.status.help') }}
                            </p>
                        </div>

                        <div v-if="form.status === 'scheduled'"
                            class="p-4 bg-white rounded-lg border border-gray-200 grid gap-1">
                            <Label for="scheduled_at" :required="false">{{ t('forms.campaign.fields.scheduled_at.label') }}</Label>
                            <DatePicker v-model="form.scheduled_at" :minDate="new Date()"
                                :feedback="form.errors.scheduled_at" :enableTimePicker="true" />
                            <span class="block text-xs text-gray-700/30 mt-2">
                                {{ t('forms.campaign.status.scheduled_help') }}
                            </span>
                        </div>
                    </div>
                </section>

                <!-- Statistics Section (alleen bij bewerken) -->
                <section v-if="form.id" class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-700">{{ t('forms.campaign.statistics.title') }}</h2>
                        <p class="mt-1 text-sm text-gray-700/30">{{ t('forms.campaign.statistics.subtitle') }}</p>
                    </div>
                    <div class="p-6 space-y-4">
                        <div v-if="form.sent_at" class="flex justify-between text-sm">
                            <span class="text-gray-700/50">{{ t('forms.campaign.statistics.sent_at') }}</span>
                            <span class="font-medium text-gray-700">{{ form.sent_at }}</span>
                        </div>
                        <div v-if="form.sent_count !== null" class="flex justify-between text-sm">
                            <span class="text-gray-700/50">{{ t('forms.campaign.statistics.sent_count') }}</span>
                            <span class="font-medium text-gray-700">{{ form.sent_count }}</span>
                        </div>
                        <div v-if="!form.sent_at" class="text-sm text-gray-700/30 italic">
                            {{ t('forms.campaign.statistics.not_sent') }}
                        </div>
                    </div>
                </section>
            </div>

        </div>
        <!-- Footer Actions -->
        <FormFooter :form="form" :label="form.id ? t('forms.campaign.submit.update') : t('forms.campaign.submit.create')" @submit="emit('submit')" />

    </form>
</template>
