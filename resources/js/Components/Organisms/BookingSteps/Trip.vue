<script setup>
import { useI18n } from 'vue-i18n'
import { toRef, computed } from 'vue'
import { useDateFormatter } from '@/Composables/useDateFormatter.js'

const { formattedDate } = useDateFormatter();
const { t } = useI18n();

const props = defineProps({
    booking: { type: Object, required: true },
    constraints: Object
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
            <p class="text-brand-primary leading-relaxed" v-html="$t('booking_steps.trip.intro', { tripName: booking.trip?.name })">
            </p>
            <div class="bg-accent-sand/20 border border-accent-sand rounded-lg p-4">
                <p class="text-sm text-brand-primary">
                    <strong>{{ $t('booking_steps.trip.notice_heading') }}</strong> <span v-html="$t('booking_steps.trip.notice_text')"></span>
                </p>
            </div>
        </div>

        <hr class="border-accent-sage/20">

        <div class="grid grid-cols-3 gap-2 items-center">
            <p>{{ $t('booking_steps.trip.trip_label') }}</p>
            <p class="text-center"><strong>{{ booking.trip.name }}</strong></p>
            <p class="text-right">{{ $t('booking_steps.trip.price_from') }} <strong>€ {{ booking.trip.price_formatted }},-</strong> {{ $t('booking_steps.trip.per_person') }}</p>

            <p v-html="$t('booking_steps.trip.choose_date')"></p>
            <DatePicker v-model="departure_date" :min-date="new Date()" :feedback="booking.errors['departure_date']"
                @mouseup="booking.clearErrors('departure_date')" />
            <p class="text-right">{{ formattedDate(booking.departure_date) || $t('booking_steps.trip.no_date_chosen') }}</p>

            <p v-html="$t('booking_steps.trip.choose_number')"></p>
            <PersonPicker v-model="participants" />
            <div class="text-right">
                <p>{{ $t('booking_steps.trip.participants_label') }}</p>
                <div class="min-h-[3em]">
                    <div>
                        <p>{{ participantSummary.adults }} {{ participantSummary.adultLabel }}</p>
                        <p v-if="participantSummary.children">{{ participantSummary.children }} {{
                            participantSummary.childLabel }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>
