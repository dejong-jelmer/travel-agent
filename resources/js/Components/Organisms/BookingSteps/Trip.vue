<script setup>
import { useI18n } from 'vue-i18n'
import { toRef, computed } from 'vue'
import { useDateFormatter } from '@/Composables/useDateFormatter.js'

const { formattedDate } = useDateFormatter();
const { t } = useI18n();

const props = defineProps({
    booking: { type: Object, required: true },
    constraints: Object,
    disabledDates: { type: [Array, Function], default: null },
})

const departure_date = toRef(props.booking, 'departure_date')
const participants = toRef(props.booking, 'participants')

const participantSummary = computed(() => {
    const adults = props.booking.participants?.adults || 0;
    const children = props.booking.participants?.children || 0;
    return {
        adults,
        children,
        adultLabel: children > 0
            ? (adults === 1 ? t('booking_steps.trip.adult_singular') : t('booking_steps.trip.adult_plural'))
            : (adults === 1 ? t('booking_steps.trip.person_singular') : t('booking_steps.trip.person_plural')),
        childLabel: children === 1 ? t('booking_steps.trip.child_singular') : t('booking_steps.trip.child_plural')
    };
});

</script>

<template>
    <div key="trip" class="space-y-4">
        <div class="space-y-2">
            <h2 class="text-xl font-bold text-brand-primary">
                {{ $t('booking_steps.trip.heading', { tripName: booking.trip?.name }) }}
            </h2>
            <p class="text-accent-text leading-relaxed" v-html="$t('booking_steps.trip.intro', { tripName: booking.trip?.name })">
            </p>
            <div class="bg-accent-sand/20 border border-accent-sand rounded-lg p-4">
                <p class="text-sm text-accent-text">
                    <strong>{{ $t('booking_steps.trip.notice_heading') }}</strong> <span v-html="$t('booking_steps.trip.notice_text')"></span>
                </p>
            </div>
        </div>

        <hr class="border-accent-sage/20">

        <div class="space-y-3">
            <!-- Reis -->
            <div class="bg-brand-secondary/40 border border-brand-primary/10 rounded-xl p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-brand-light mb-1">{{ $t('booking_steps.trip.trip_label') }}</p>
                <div class="flex justify-between items-baseline">
                    <p class="text-brand-primary font-bold">{{ booking.trip.name }}</p>
                    <p class="text-sm text-accent-text shrink-0 ml-4">{{ $t('booking_steps.trip.price_from') }} <strong>€ {{ booking.trip.price_formatted }},-</strong> {{ $t('booking_steps.trip.per_person') }}</p>
                </div>
            </div>

            <!-- Vertrekdatum -->
            <div class="bg-brand-secondary/40 border border-brand-primary/10 rounded-xl p-4 space-y-3">
                <div class="flex justify-between items-center">
                    <p class="text-xs font-semibold uppercase tracking-wide text-brand-light" v-html="$t('booking_steps.trip.choose_date')"></p>
                    <span class="text-sm text-accent-text">{{ formattedDate(booking.departure_date) || $t('booking_steps.trip.no_date_chosen') }}</span>
                </div>
                <DatePicker v-model="departure_date" :min-date="new Date()" :max-date="constraints?.maxDate ?? null"
                    :disabled-dates="disabledDates"
                    :feedback="booking.errors['departure_date']" @mouseup="booking.clearErrors('departure_date')" />
            </div>

            <!-- Deelnemers -->
            <div class="bg-brand-secondary/40 border border-brand-primary/10 rounded-xl p-4 space-y-3">
                <div class="flex justify-between items-center">
                    <p class="text-xs font-semibold uppercase tracking-wide text-brand-light" v-html="$t('booking_steps.trip.choose_number')"></p>
                    <span class="text-sm text-accent-text">
                        {{ participantSummary.adults }} {{ participantSummary.adultLabel }}<span v-if="participantSummary.children"> &middot; {{ participantSummary.children }} {{ participantSummary.childLabel }}</span>
                    </span>
                </div>
                <PersonPicker v-model="participants" />
            </div>
        </div>
    </div>
</template>
