<script setup>
import { computed, toRef } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Plus, X, Info } from 'lucide-vue-next';

const props = defineProps({
    booking: { type: Object, required: true },
});

const page = usePage();
const costCategories = computed(() => page.props.cost_categories ?? []);

const costItems = toRef(props.booking, 'cost_items');

const marginInPercentage = computed({
    get: () => props.booking.margin_in_percentage,
    set: (value) => (props.booking.margin_in_percentage = value),
});

const fmt = (cents) =>
    new Intl.NumberFormat('nl-NL', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 2,
    }).format((cents ?? 0) / 100);

const subtotalCents = (item) => {
    const amount = Number(item.amount_per_person);
    const quantity = Number(item.quantity);
    if (!Number.isFinite(amount) || !Number.isFinite(quantity)) return 0;
    return Math.round(amount * 100) * quantity;
};

const totalCostCents = computed(() =>
    costItems.value.reduce((acc, item) => acc + subtotalCents(item), 0),
);

const marginFraction = computed(() => {
    const m = Number(props.booking.margin_percentage);
    if (!Number.isFinite(m)) return 0;
    return Math.min(Math.max(m, 0), 95) / 100;
});

const toCents = (euros) => {
    const value = Number(euros);
    return Number.isFinite(value) ? Math.round(value * 100) : 0;
};

// The fee is charged per adult; children do not pay it.
const adultCount = computed(() => {
    const count = Number(props.booking.participants?.adults);
    return Number.isFinite(count) ? count : 0;
});

const feeTotalCents = computed(
    () => toCents(props.booking.fee_per_person) * adultCount.value,
);

// Snapshot for an existing booking, current settings for a new one.
const feesAndFunds = computed(() => page.props.fees_and_funds ?? {});

const feesAndFundsRows = computed(() =>
    Object.entries(feesAndFunds.value).filter(([, cents]) => Number(cents) > 0),
);

const feesAndFundsTotalCents = computed(() =>
    Object.values(feesAndFunds.value).reduce(
        (acc, cents) => acc + (Number(cents) || 0),
        0,
    ),
);

// The sales price of the trip itself, before fees and funds.
const marginedPriceCents = computed(() => {
    if (!marginInPercentage.value) {
        return (
            totalCostCents.value +
            toCents(props.booking.margin_amount) +
            feeTotalCents.value
        );
    }
    if (marginFraction.value >= 1) return 0;
    return Math.round(totalCostCents.value / (1 - marginFraction.value));
});

const calculatedPriceCents = computed(
    () => marginedPriceCents.value + feesAndFundsTotalCents.value,
);

const marginAmountCents = computed(
    () => marginedPriceCents.value - totalCostCents.value,
);

const finalPriceCents = computed(() => {
    const fp = props.booking.final_price;
    if (fp === null || fp === '' || fp === undefined) return null;
    return Math.round(Number(fp) * 100);
});

const hasOverride = computed(() => finalPriceCents.value !== null);

const displayPriceCents = computed(() =>
    hasOverride.value ? finalPriceCents.value : calculatedPriceCents.value,
);

function addItem() {
    costItems.value.push({
        category: costCategories.value[0]?.id ?? 'train',
        label: '',
        amount_per_person: null,
        quantity: 2,
    });
}

function removeItem(index) {
    costItems.value.splice(index, 1);
}

function clearItemError(index, field) {
    props.booking.clearErrors(`cost_items.${index}.${field}`);
}
</script>

<template>
    <div key="price" class="space-y-6">
        <h2 class="text-xl font-bold text-brand-primary">{{ $t('booking_steps.price.heading') }}</h2>
        <hr class="border-brand-subtle/20">

        <section class="space-y-4 p-4 border rounded-lg">
            <h3 class="text-base font-bold text-brand-primary">{{ $t('booking_steps.price.cost_items_heading') }}</h3>

            <div v-if="booking.errors.cost_items" data-error="true"
                class="text-sm text-status-error bg-status-error/10 rounded-lg p-2">
                {{ booking.errors.cost_items }}
            </div>

            <div v-for="(item, index) in costItems" :key="index"
                class="grid grid-cols-1 tablet:grid-cols-12 gap-3 items-start p-3 rounded-lg bg-slate-50/50">
                <div class="tablet:col-span-3">
                    <Select :name="`cost_items.${index}.category`"
                        :label="$t('booking_steps.price.column.category')" :showLabel="true"
                        v-model="item.category" :options="costCategories"
                        :feedback="booking.errors[`cost_items.${index}.category`]"
                        @update:modelValue="clearItemError(index, 'category')" />
                </div>
                <div class="tablet:col-span-5">
                    <Input type="text" :name="`cost_items.${index}.label`"
                        :label="$t('booking_steps.price.column.label')" :showLabel="true" :required="true"
                        v-model="item.label"
                        :placeholder="$t('booking_steps.price.label_placeholder')"
                        :feedback="booking.errors[`cost_items.${index}.label`]"
                        @keyup="clearItemError(index, 'label')" />
                </div>
                <div class="tablet:col-span-2">
                    <Input type="number" :name="`cost_items.${index}.amount_per_person`"
                        :label="$t('booking_steps.price.column.amount_per_person')" :showLabel="true" :required="true"
                        v-model="item.amount_per_person" step="0.01" min="0"
                        placeholder="0.00"
                        :feedback="booking.errors[`cost_items.${index}.amount_per_person`]"
                        @keyup="clearItemError(index, 'amount_per_person')" />
                </div>
                <div class="tablet:col-span-1">
                    <Input type="number" :name="`cost_items.${index}.quantity`"
                        :label="$t('booking_steps.price.column.quantity')" :showLabel="true" :required="true"
                        v-model="item.quantity" min="1" max="20"
                        :feedback="booking.errors[`cost_items.${index}.quantity`]"
                        @keyup="clearItemError(index, 'quantity')" />
                </div>
                <div class="tablet:col-span-1 flex tablet:justify-end items-end h-full pb-1">
                    <button type="button"
                        class="p-2 rounded-lg text-brand-text/60 hover:text-status-error hover:bg-status-error/10 transition-colors"
                        :disabled="costItems.length === 1"
                        :title="$t('booking_steps.price.remove_item')"
                        @click="removeItem(index)">
                        <X class="w-4 h-4" />
                    </button>
                </div>
            </div>

            <button type="button"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-brand-accent text-white font-medium hover:bg-white hover:text-brand-primary border border-transparent hover:border-brand-primary transition-all"
                @click="addItem">
                <Plus class="w-4 h-4" />
                {{ $t('booking_steps.price.add_item') }}
            </button>
        </section>

        <section class="space-y-4 p-4 border rounded-lg bg-white">
            <div class="grid grid-cols-1 tablet:grid-cols-2 gap-4 tablet:gap-6 items-end">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-brand-text/70">{{ $t('booking_steps.price.total_cost') }}</span>
                        <span class="font-bold text-brand-primary">{{ fmt(totalCostCents) }}</span>
                    </div>
                </div>
                <div class="space-y-2">
                    <Checkbox v-model="marginInPercentage" name="margin_in_percentage">
                        {{ $t('booking_steps.price.margin_in_percentage') }}
                    </Checkbox>
                    <Input v-if="marginInPercentage" type="number" name="margin_percentage"
                        :label="$t('booking_steps.price.margin')" :showLabel="true" :required="true"
                        v-model="booking.margin_percentage" step="0.5" min="0" max="95"
                        :feedback="booking.errors['margin_percentage']"
                        @keyup="booking.clearErrors('margin_percentage')" />
                    <template v-else>
                        <Input type="number" name="margin_amount"
                            :label="$t('booking_steps.price.margin_fixed')" :showLabel="true" :required="true"
                            v-model="booking.margin_amount" step="0.01" min="0"
                            placeholder="0.00"
                            :feedback="booking.errors['margin_amount']"
                            @keyup="booking.clearErrors('margin_amount')" />
                        <Input type="number" name="fee_per_person"
                            :label="$t('booking_steps.price.fee_per_person')" :showLabel="true" :required="true"
                            v-model="booking.fee_per_person" step="0.01" min="0"
                            placeholder="0.00"
                            :feedback="booking.errors['fee_per_person']"
                            @keyup="booking.clearErrors('fee_per_person')" />
                    </template>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-brand-subtle/20">
                <span class="text-brand-text/70">
                    {{ $t('booking_steps.price.margin_amount') }}
                </span>
                <span class="font-medium text-brand-text/70">
                    {{ fmt(marginInPercentage ? marginAmountCents : toCents(booking.margin_amount)) }}
                </span>
            </div>

            <div v-if="!marginInPercentage" class="flex items-center justify-between">
                <span class="text-brand-text/70">
                    {{ $t('booking_steps.price.fee_total', { count: adultCount }) }}
                </span>
                <span class="font-medium text-brand-text/70">{{ fmt(feeTotalCents) }}</span>
            </div>

            <div v-for="[key, cents] in feesAndFundsRows" :key="key"
                class="flex items-center justify-between">
                <span class="text-brand-text/70">{{ $t(`booking_steps.overview.${key}`) }}</span>
                <span class="font-medium text-brand-text/70">{{ fmt(cents) }}</span>
            </div>

            <div class="flex items-center justify-between">
                <span class="text-brand-text/70">{{ $t('booking_steps.price.calculated_price') }}</span>
                <span class="font-bold text-brand-primary"
                    :class="{ 'line-through opacity-50': hasOverride }">{{ fmt(calculatedPriceCents) }}</span>
            </div>

            <div class="grid grid-cols-1 tablet:grid-cols-2 gap-4 tablet:gap-6 items-end pt-2 border-t border-brand-subtle/20">
                <div>
                    <Input type="number" name="final_price"
                        :label="$t('booking_steps.price.final_price')" :showLabel="true"
                        v-model="booking.final_price" step="0.01" min="0"
                        :placeholder="$t('booking_steps.price.final_price_help')"
                        :feedback="booking.errors['final_price']"
                        @keyup="booking.clearErrors('final_price')" />
                    <p v-if="hasOverride" class="mt-1 flex items-center gap-1 text-xs text-brand-accent">
                        <Info class="w-3 h-3" />
                        {{ $t('booking_steps.price.override_active') }}
                    </p>
                </div>
                <div class="flex items-center justify-between text-lg">
                    <span class="font-semibold text-brand-primary">{{ $t('booking_steps.price.display_price') }}</span>
                    <span class="font-bold text-brand-primary">{{ fmt(displayPriceCents) }}</span>
                </div>
            </div>
        </section>
    </div>
</template>
