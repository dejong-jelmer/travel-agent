<script setup>
import { BedDouble, Info, MoonStar, TrainFront } from '@lucide/vue';
import { useI18n } from 'vue-i18n';

defineProps({
    // Shape: [{ id, type, day_from, day_to, title, description, accommodation_label, remark, image }], see
    // ItineraryService::forDisplay. A trip without itinerary passes an empty list and the section is not shown.
    items: { type: Array, default: () => [] },
});

const { t } = useI18n();

const isTravelDay = (item) => item.type !== 'stay';

// The day or range of days, e.g. "3" or "3-6"
const days = (item) => item.day_to ? `${item.day_from}-${item.day_to}` : `${item.day_from}`;

const label = (item) => isTravelDay(item)
    ? `${t('trip_itinerary.day')} ${days(item)} · ${t('trip_itinerary.travel_day')}`
    : `${t('trip_itinerary.day')} ${days(item)}`;

// Rendered photo width per breakpoint (screens.js). The photo sits next to the text at 220px once the content column
// is at least 520px wide (the container query in the template), otherwise above it at the full column width. That
// column is the viewport minus the page padding (px-4, tablet:px-6), from laptop on the two-thirds column
// (grid-cols-3, gap-12, max-w-screen-desktop), the card border and padding (px-5, tablet:px-10) and the rail with its
// gap (32px, tablet:44px, plus 20px). It reaches 520px at a 714px viewport, drops below it when the sidebar appears at
// 900px and reaches it again at 1071px. Update this when that layout changes.
const imageSizes = '(min-width: 1071px) 220px, (min-width: 900px) calc(66.7vw - 194px), (min-width: 714px) 220px, (min-width: 600px) calc(100vw - 194px), calc(100vw - 126px)';
</script>

<template>
    <BaseCard v-if="items?.length" aria-labelledby="trip-itinerary-heading">
        <SectionHeader id="trip-itinerary-heading">{{ $t('trip_show.itinerary_heading') }}</SectionHeader>

        <ol>
            <li v-for="(item, index) in items" :key="item.id"
                class="grid grid-cols-[32px_minmax(0,1fr)] tablet:grid-cols-[44px_minmax(0,1fr)] gap-x-5">
                <!-- Rail: the marker with a line below it down to the next item. Hidden from screen readers, as the
                     label next to it names the day as well. -->
                <div class="flex flex-col items-center" aria-hidden="true">
                    <!-- Round, or a pill for a range of days; a pill wider than the rail overflows it on both sides -->
                    <span
                        class="flex items-center justify-center h-8 min-w-8 tablet:h-9 tablet:min-w-9 px-2 rounded-full whitespace-nowrap"
                        :class="item.type === 'night_train'
                            ? 'bg-brand-text text-brand-secondary'
                            : 'bg-white border-2 border-brand-primary text-sm font-semibold text-brand-primary'">
                        <MoonStar v-if="item.type === 'night_train'" class="w-4 h-4 tablet:w-[18px] tablet:h-[18px]" />
                        <TrainFront v-else-if="item.type === 'train'" class="w-4 h-4 tablet:w-[18px] tablet:h-[18px]" />
                        <template v-else>{{ days(item) }}</template>
                    </span>
                    <span v-if="index < items.length - 1" class="w-0.5 flex-1 bg-brand-accent/20"></span>
                </div>

                <!-- The space below an item sits inside it, so the rail line runs on to the next marker -->
                <div class="min-w-0 [container-type:inline-size]"
                    :class="{ 'pb-8 tablet:pb-9': index < items.length - 1 }">
                    <p class="text-[12.5px] uppercase tracking-[0.14em] text-brand-primary/85">
                        {{ label(item) }}
                    </p>
                    <h3 class="mt-1 text-[21px] leading-snug font-semibold text-brand-primary">
                        {{ item.title }}
                    </h3>
                    <!-- mt-[3px] centres the 16px icon on the first line of text -->
                    <p v-if="item.accommodation_label"
                        class="flex items-start gap-2 mt-1.5 text-[14px] tablet:text-[14.5px] leading-[1.5] text-brand-primary/85">
                        <BedDouble class="w-4 h-4 mt-[3px] flex-shrink-0" aria-hidden="true" />
                        <span>{{ item.accommodation_label }}</span>
                    </p>

                    <!-- Photo above the text, or to the right of it once the column has room for both -->
                    <div
                        class="flex flex-col gap-4 mt-4 [@container(min-width:520px)]:flex-row [@container(min-width:520px)]:items-start [@container(min-width:520px)]:gap-6">
                        <ResponsiveImage v-if="item.image" :image="item.image" :sizes="imageSizes" :alt="item.title"
                            loading="lazy"
                            class="w-full aspect-[16/10] flex-shrink-0 object-cover rounded-xl [@container(min-width:520px)]:order-last [@container(min-width:520px)]:w-[220px] [@container(min-width:520px)]:aspect-[4/3]" />
                        <div class="flex-1 min-w-0">
                            <p class="text-[16.5px] leading-[1.75] text-brand-text">
                                {{ item.description }}
                            </p>
                            <p v-if="item.remark"
                                class="flex items-start gap-2 mt-[14px] text-[14px] tablet:text-[14.5px] leading-[1.5] text-brand-primary/85">
                                <Info class="w-4 h-4 mt-[3px] flex-shrink-0" aria-hidden="true" />
                                <span>{{ item.remark }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </li>
        </ol>
    </BaseCard>
</template>
