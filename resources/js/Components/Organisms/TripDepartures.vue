<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { ArrowRight, CalendarDays, ShieldCheck } from '@lucide/vue';
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

const toDate = (value) => new Date(`${value}T00:00:00`);
const day = (value, options) => new Intl.DateTimeFormat(locale.value, options).format(toDate(value));

// "za 20 juni – vr 26 juni 2027": the year once, at the end. Read out instead of the date tile.
const period = (departure) => `${day(departure.departs_on, { weekday: 'short', day: 'numeric', month: 'long' })} – ${day(departure.returns_on, { weekday: 'short', day: 'numeric', month: 'long', year: 'numeric' })}`;
const longDate = (value) => day(value, { day: 'numeric', month: 'long', year: 'numeric' });
const money = (cents) => formatCents(cents, locale.value).replace(/,00$/, '');

// The date tile: "vr – zo", "5–7", and the month with the year in full ("februari 2027") and short ("feb 2027").
// A departure across months names both months short; across years both years too.
function tile({ departs_on: start, returns_on: end }) {
    const [from, to] = [toDate(start), toDate(end)];
    const sameDay = start === end;
    const sameYear = from.getFullYear() === to.getFullYear();
    const sameMonth = sameYear && from.getMonth() === to.getMonth();
    const range = (format) => sameDay ? format(start) : `${format(start)} – ${format(end)}`;

    let months;
    if (sameMonth) {
        months = {
            long: day(start, { month: 'long', year: 'numeric' }),
            short: day(start, { month: 'short', year: 'numeric' }),
        };
    } else {
        const short = sameYear
            ? `${day(start, { month: 'short' })} – ${day(end, { month: 'short', year: 'numeric' })}`
            : `${day(start, { month: 'short', year: 'numeric' })} – ${day(end, { month: 'short', year: 'numeric' })}`;
        months = { long: short, short };
    }

    return {
        weekdays: range((value) => day(value, { weekday: 'short' })),
        days: sameDay ? `${from.getDate()}` : `${from.getDate()}–${to.getDate()}`,
        months,
    };
}

const tiles = computed(() => Object.fromEntries(props.departures.map((departure) => [departure.id, tile(departure)])));

const feeItems = computed(() => Object.entries(props.fees.items ?? {})
    .map(([key, cents]) => `${t(`booking_steps.overview.${key}`).toLowerCase()} ${money(cents)}`)
    .join(', '));
</script>

<template>
    <section aria-labelledby="trip-departures-heading" class="rounded-[28px] bg-white p-5 tablet:p-12">
        <p class="text-[13px] uppercase tracking-[0.12em] text-brand-light">{{ $t('trip_show.departures.eyebrow') }}</p>
        <h2 id="trip-departures-heading" class="mt-1 text-[36px] leading-tight font-semibold text-brand-primary">
            {{ $t('trip_show.departures.title') }}
        </h2>

        <p v-if="departures.length === 0" class="mt-4 text-base text-brand-text leading-relaxed" data-testid="no-departures">
            {{ $t('trip_show.departures.none') }}
        </p>

        <template v-else>
            <!-- The rows follow the width of the list, not of the screen: the card is narrow beside the sidebar on
                 laptops. From 40rem a row lays out as tile | price and status | action, below that it stacks. -->
            <ul class="mt-8 space-y-4 [container-type:inline-size]">
                <li v-for="departure in departures" :key="departure.id" :data-testid="`departure-${departure.id}`"
                    class="grid grid-cols-[auto_minmax(0,1fr)] gap-4 rounded-[20px] p-4 [@container(min-width:40rem)]:grid-cols-[auto_minmax(0,1fr)_auto] [@container(min-width:40rem)]:gap-x-7 [@container(min-width:40rem)]:gap-y-2 [@container(min-width:40rem)]:rounded-[22px] [@container(min-width:40rem)]:p-5"
                    :class="departure.availability === 'full' ? 'bg-white border-[1.5px] border-dashed border-brand-earth' : 'bg-brand-secondary'">
                    <h3 class="sr-only">{{ period(departure) }}</h3>

                    <!-- Date tile: a visual summary of the period, which the heading above reads out in full -->
                    <div aria-hidden="true"
                        class="col-start-1 row-start-1 self-center flex w-24 flex-col items-center justify-center rounded-2xl px-2 py-3 text-center [@container(min-width:40rem)]:row-span-2 [@container(min-width:40rem)]:w-32"
                        :class="departure.availability === 'full' ? 'bg-brand-secondary text-brand-light' : 'bg-brand-primary text-white'">
                        <span class="text-xs uppercase tracking-[0.08em]">{{ tiles[departure.id].weekdays }}</span>
                        <span class="mt-1 whitespace-nowrap text-[28px] leading-none font-semibold [@container(min-width:40rem)]:text-[38px]">
                            {{ tiles[departure.id].days }}
                        </span>
                        <span class="mt-1.5 text-[13px] leading-snug [@container(min-width:40rem)]:hidden">{{ tiles[departure.id].months.short }}</span>
                        <span class="mt-1.5 hidden text-[13px] leading-snug [@container(min-width:40rem)]:inline">{{ tiles[departure.id].months.long }}</span>
                    </div>

                    <p v-if="departure.price_per_person !== null"
                        class="col-start-2 row-start-1 self-center flex flex-col [@container(min-width:40rem)]:self-end [@container(min-width:40rem)]:flex-row [@container(min-width:40rem)]:flex-wrap [@container(min-width:40rem)]:items-baseline [@container(min-width:40rem)]:gap-x-2">
                        <span class="text-[28px] leading-tight font-semibold"
                            :class="departure.availability === 'full' ? 'text-brand-light' : 'text-brand-text'">
                            {{ money(departure.price_per_person) }}
                        </span>
                        {{ ' ' }}
                        <span class="text-sm text-brand-light">
                            {{ $t('trip_show.departures.per_person') }}<template v-if="departure.single_supplement">
                                · {{ $t('trip_show.departures.single_supplement', { price: money(departure.single_supplement) }) }}</template>
                        </span>
                    </p>

                    <!-- How certain the departure is: guaranteed, or from how many participants and by when -->
                    <div class="col-span-2 row-start-2 space-y-1.5 [@container(min-width:40rem)]:col-span-1 [@container(min-width:40rem)]:col-start-2 [@container(min-width:40rem)]:self-start">
                        <p v-if="departure.is_guaranteed" data-testid="guaranteed"
                            class="inline-flex items-center gap-1.5 rounded-full bg-[#e6f0dc] px-3 py-1 text-sm font-medium text-[#36581f]">
                            <ShieldCheck class="h-4 w-4 shrink-0" aria-hidden="true" />
                            {{ $t('trip_show.departures.guaranteed') }}
                        </p>
                        <div v-else class="flex items-start gap-2 text-sm leading-relaxed text-brand-light">
                            <CalendarDays class="mt-[3px] h-4 w-4 shrink-0" aria-hidden="true" />
                            <p data-testid="minimum">
                                {{ $t('trip_show.departures.minimum', { min: departure.min_participants }) }}
                                <template v-if="departure.decision_date">
                                    {{ $t('trip_show.departures.decision', { date: longDate(departure.decision_date) }) }}
                                </template>
                            </p>
                        </div>

                        <p v-if="departure.availability === 'few_seats'" class="text-sm text-brand-text" data-testid="seats-left">
                            {{ $t('trip_show.departures.seats_left', { count: departure.seats_left }) }}
                        </p>
                    </div>

                    <div class="col-span-2 row-start-3 [@container(min-width:40rem)]:col-span-1 [@container(min-width:40rem)]:col-start-3 [@container(min-width:40rem)]:row-start-1 [@container(min-width:40rem)]:row-span-2 [@container(min-width:40rem)]:self-center">
                        <div v-if="departure.availability === 'full'"
                            class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1 [@container(min-width:40rem)]:flex-col [@container(min-width:40rem)]:items-end [@container(min-width:40rem)]:text-right">
                            <p class="text-[15px] font-semibold text-brand-text" data-testid="full">
                                {{ $t('trip_show.departures.full') }}
                            </p>
                            <button type="button" data-testid="interest"
                                class="rounded text-sm text-brand-primary underline underline-offset-2 hover:text-brand-text focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-primary"
                                @click="$emit('request', departure)">
                                {{ $t('trip_show.departures.full_interest') }}
                            </button>
                        </div>
                        <button v-else type="button" data-testid="request"
                            class="inline-flex min-h-12 w-full items-center justify-center gap-2 whitespace-nowrap rounded-full bg-brand-primary px-6 text-[15px] font-medium text-white transition-colors duration-200 hover:bg-brand-text focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-primary [@container(min-width:40rem)]:w-auto"
                            @click="$emit('request', departure)">
                            {{ $t('trip_show.departures.cta') }}
                            <ArrowRight class="h-4 w-4 shrink-0" aria-hidden="true" />
                        </button>
                    </div>
                </li>
            </ul>

            <!-- What comes on top of the price per person, as it has to be clear before someone asks a place -->
            <p v-if="fees.total > 0" class="mt-8 border-t border-[#ece6da] pt-6 text-[13px] leading-relaxed text-brand-light"
                data-testid="fees">
                {{ $t('trip_show.departures.fees', { amount: money(fees.total), items: feeItems }) }}
            </p>
        </template>
    </section>
</template>
