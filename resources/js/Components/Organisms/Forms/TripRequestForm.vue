<script setup>
import { computed, ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { LoaderCircle } from '@lucide/vue'

const props = defineProps({
    trip: {
        type: Object,
        required: true,
    },
})

const { t } = useI18n()

const form = useForm({
    name: '',
    email: '',
    phone: '',
    preferred_month: '',
    preferred_year: '',
    preferred_period_note: '',
    travelers_count: '',
    departure_station: '',
    notes: '',
    consent_privacy: false,
})

const honeypot = ref(null)

const currentYear = new Date().getFullYear()
const currentMonth = new Date().getMonth() + 1

const monthOptions = computed(() => {
    const minMonth = form.preferred_year === String(currentYear) ? currentMonth : 1
    return Array.from({ length: 12 }, (_, i) => ({
        id: String(i + 1),
        name: t(`trip_request.form.months.${i + 1}`),
    })).filter((_, i) => i + 1 >= minMonth)
})

watch(() => form.preferred_year, (year) => {
    if (year === String(currentYear) && Number(form.preferred_month) < currentMonth) {
        form.preferred_month = ''
    }
})

const yearOptions = computed(() =>
    Array.from({ length: 3 }, (_, i) => ({
        id: String(currentYear + i),
        name: String(currentYear + i),
    }))
)

const travelerOptions = computed(() => {
    const options = Array.from({ length: 6 }, (_, i) => ({
        id: String(i + 1),
        name: String(i + 1),
    }))
    options.push({
        id: '7',
        name: t('trip_request.form.travelers_count_more_than_6'),
    })
    return options
})

const stationOptions = computed(() => {
    const keys = ['amsterdam', 'schiphol', 'utrecht', 'arnhem', 'rotterdam', 'breda', 'other']
    return keys.map((key) => ({
        id: t(`trip_request.form.stations.${key}`),
        name: t(`trip_request.form.stations.${key}`),
    }))
})

function submit() {
    try {
        honeypot.value?.validate()
    } catch {
        return
    }

    form.post(route('trip-requests.store', props.trip.slug), {
        preserveScroll: true,
    })
}
</script>

<template>
    <form @submit.prevent="submit" class="space-y-8">
        <!-- Header -->
        <div class="text-center mb-2">
            <h2 class="text-xl tablet:text-2xl font-semibold text-brand-primary">
                {{ t('trip_request.form.title', { tripName: trip.name }) }}
            </h2>
            <p v-if="trip.subtitle" class="text-sm text-brand-text/70 mt-2 leading-relaxed">
                {{ trip.subtitle }}
            </p>
        </div>

        <!-- Section 1: You -->
        <div class="space-y-5">
            <h3 class="text-lg font-medium text-brand-primary">
                {{ t('trip_request.form.section_you') }}
            </h3>

            <div class="grid grid-cols-1 tablet:grid-cols-2 gap-5">

                <Input type="text" name="name" :label="t('trip_request.form.name_label')"
                    :required="true" v-model="form.name"
                    :feedback="form.errors.name" />

                <Input type="email" name="email" :label="t('trip_request.form.email_label')"
                    :required="true" v-model="form.email"
                    :feedback="form.errors.email" />

            </div>
            <div>
                <Input type="tel" name="phone" :label="t('trip_request.form.phone_label')"
                    v-model="form.phone"
                    :feedback="form.errors.phone" />
                <p class="text-xs text-brand-primary/60 mt-1.5 pl-0.5">
                    {{ t('trip_request.form.phone_helper') }}
                </p>
            </div>
        </div>

        <!-- Section 2: Your Trip -->
        <div class="space-y-5">
            <h3 class="text-lg font-medium text-brand-primary">
                {{ t('trip_request.form.section_trip') }}
            </h3>

            <!-- Period: month + year side by side -->
            <div>
                <Label forField="preferred_month">
                    {{ t('trip_request.form.preferred_month_label') }}
                </Label>
                <div class="grid grid-cols-2 gap-3 mt-1">
                    <Select name="preferred_month" :show-label="false" :options="monthOptions"
                        :placeholder="t('trip_request.form.preferred_month_placeholder')" v-model="form.preferred_month"
                        :feedback="form.errors.preferred_month" />
                    <Select name="preferred_year" :show-label="false" :options="yearOptions"
                        :placeholder="t('trip_request.form.preferred_year_placeholder')" v-model="form.preferred_year"
                        :feedback="form.errors.preferred_year" />
                </div>
            </div>

            <Input type="text" name="preferred_period_note" :label="t('trip_request.form.preferred_period_note_label')"
                :placeholder="t('trip_request.form.preferred_period_note_placeholder')"
                v-model="form.preferred_period_note" :feedback="form.errors.preferred_period_note" />


            <div class="grid grid-cols-1 tablet:grid-cols-3 gap-5">
                <div class="col-span-2">

                    <Select name="departure_station" :label="t('trip_request.form.departure_station_label')"
                        :options="stationOptions" :placeholder="t('trip_request.form.departure_station_placeholder')"
                        v-model="form.departure_station" :feedback="form.errors.departure_station" />
                    <p class="text-xs text-brand-primary/60 mt-1.5 pl-0.5">
                        {{ t('trip_request.form.departure_station_helper') }}
                    </p>
                </div>
                <div class="col-span-1">
                    <Select name="travelers_count" :label="t('trip_request.form.travelers_count_label')"
                        :options="travelerOptions" :placeholder="t('trip_request.form.travelers_count_placeholder')"
                        v-model="form.travelers_count" :feedback="form.errors.travelers_count" />
                    <p class="text-xs text-brand-primary/60 mt-1.5 pl-0.5">
                        {{ t('trip_request.form.travelers_count_helper') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Section 3: Details -->
        <div class="space-y-5">
            <h3 class="text-lg font-medium text-brand-primary">
                {{ t('trip_request.form.section_notes') }}
            </h3>

            <div>
                <TextArea name="notes" :label="t('trip_request.form.notes_label')"
                    :description="t('trip_request.form.notes_helper')" :rows="5" v-model="form.notes"
                    :feedback="form.errors.notes" />
            </div>
        </div>

        <!-- Privacy consent -->
        <div>
            <Checkbox name="consent_privacy" v-model="form.consent_privacy" :feedback="form.errors.consent_privacy">
                <i18n-t keypath="trip_request.form.consent_privacy" tag="span" class="text-sm">
                    <template #link>
                        <DefaultLink :href="route('privacy')" target="_blank"
                            class="underline hover:text-brand-primary">
                            {{ t('trip_request.form.consent_privacy_link') }}
                        </DefaultLink>
                    </template>
                </i18n-t>
            </Checkbox>
        </div>

        <VueHoneypot ref="honeypot" />

        <!-- Submit -->
        <div class="flex justify-center">
            <Button color="accent" type="submit" :disabled="form.processing"
                class="w-full tablet:w-auto flex justify-center items-center">
                <span v-if="!form.processing">{{ t('trip_request.form.submit_button') }}</span>
                <span v-else class="flex items-center gap-2">
                    <LoaderCircle class="animate-spin h-5 w-5" />
                    {{ t('trip_request.form.submit_processing') }}
                </span>
            </Button>
        </div>
    </form>
</template>
