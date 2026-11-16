<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { ShieldCheck } from '@lucide/vue';
import { formatCents } from '@/Support/money.js';

// The departures of a group trip a visitor can request a place on, each with its dates, price and how certain it is.
const props = defineProps({
    // Shape: [{ id, departs_on, returns_on, duration_in_days, price_per_person, single_supplement, availability,
    // seats_left, is_guaranteed, min_participants, decision_date }], see App\Services\DepartureListing. Amounts in
    // cents, availability 'available', 'few_seats' or 'full'.
    departures: { type: Array, default: () => [] },
    // Shape: { total, items: { [setting key]: cents } }, what a booking pays once on top of the price per person
    fees: { type: Object, default: () => ({ total: 0, items: {} }) },
});

// The departure a visitor asks a place on; for a full one that is interest in a place that comes free
defineEmits(['request']);

// The locale of the site, which the app keeps in step with the server
const { t, locale } = useI18n();

const day = (value, options) => new Intl.DateTimeFormat(locale.value, options).format(new Date(`${value}T00:00:00`));

// "za 20 juni – vr 26 juni 2027": the year once, at the end
const period = (departure) => `${day(departure.departs_on, { weekday: 'short', day: 'numeric', month: 'long' })} – ${day(departure.returns_on, { weekday: 'short', day: 'numeric', month: 'long', year: 'numeric' })}`;
const longDate = (value) => day(value, { day: 'numeric', month: 'long', year: 'numeric' });
const money = (cents) => formatCents(cents, locale.value).replace(/,00$/, '');

const feeItems = computed(() => Object.entries(props.fees.items ?? {})
    .map(([key, cents]) => `${t(`booking_steps.overview.${key}`).toLowerCase()} ${money(cents)}`)
    .join(', '));
</script>

<template>
    <BaseCard aria-labelledby="trip-departures-heading">
        <h2 id="trip-departures-heading" class="text-[26px] font-semibold text-brand-primary">
            {{ $t('trip_show.departures.title') }}
        </h2>

        <p v-if="departures.length === 0" class="mt-4 text-base text-brand-text leading-relaxed" data-testid="no-departures">
            {{ $t('trip_show.departures.none') }}
        </p>

        <template v-else>
            <p class="mt-2 text-base text-brand-text leading-relaxed">{{ $t('trip_show.departures.intro') }}</p>

            <ul class="mt-6 divide-y divide-brand-earth/40 border-y border-brand-earth/40">
                <li v-for="departure in departures" :key="departure.id" :data-testid="`departure-${departure.id}`"
                    class="py-5 grid grid-cols-1 tablet:grid-cols-[minmax(0,1fr)_auto] gap-x-8 gap-y-4 tablet:items-center">
                    <div class="min-w-0 space-y-1.5">
                        <h3 class="text-lg font-semibold text-brand-primary">{{ period(departure) }}</h3>

                        <p v-if="departure.price_per_person !== null" class="text-base text-brand-text">
                            <span class="font-semibold">{{ money(departure.price_per_person) }}</span>
                            {{ $t('trip_show.departures.per_person') }}
                            <span v-if="departure.single_supplement" class="text-brand-light">
                                · {{ $t('trip_show.departures.single_supplement', { price: money(departure.single_supplement) }) }}
                            </span>
                        </p>

                        <!-- How certain the departure is: guaranteed, or from how many participants and by when -->
                        <p v-if="departure.is_guaranteed" class="flex items-center gap-2 text-sm text-brand-primary"
                            data-testid="guaranteed">
                            <ShieldCheck class="w-4 h-4 shrink-0 text-brand-subtle" aria-hidden="true" />
                            {{ $t('trip_show.departures.guaranteed') }}
                        </p>
                        <p v-else class="text-sm leading-relaxed text-brand-light" data-testid="minimum">
                            {{ $t('trip_show.departures.minimum', { min: departure.min_participants }) }}
                            <template v-if="departure.decision_date">
                                {{ $t('trip_show.departures.decision', { date: longDate(departure.decision_date) }) }}
                            </template>
                        </p>

                        <p v-if="departure.availability === 'few_seats'" class="text-sm text-brand-text" data-testid="seats-left">
                            {{ $t('trip_show.departures.seats_left', { count: departure.seats_left }) }}
                        </p>
                    </div>

                    <div class="tablet:text-right">
                        <template v-if="departure.availability === 'full'">
                            <p class="text-base font-semibold text-brand-light" data-testid="full">
                                {{ $t('trip_show.departures.full') }}
                            </p>
                            <button type="button" data-testid="interest"
                                class="mt-1 text-sm text-brand-primary underline underline-offset-2 hover:text-brand-text focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-accent"
                                @click="$emit('request', departure)">
                                {{ $t('trip_show.departures.full_interest') }}
                            </button>
                        </template>
                        <Button v-else color="primary" data-testid="request" @click="$emit('request', departure)">
                            {{ $t('trip_show.departures.cta') }}
                        </Button>
                    </div>
                </li>
            </ul>

            <!-- What comes on top of the price per person, as it has to be clear before someone asks a place -->
            <p v-if="fees.total > 0" class="mt-5 text-sm leading-relaxed text-brand-light" data-testid="fees">
                {{ $t('trip_show.departures.fees', { amount: money(fees.total), items: feeItems }) }}
            </p>
        </template>
    </BaseCard>
</template>
