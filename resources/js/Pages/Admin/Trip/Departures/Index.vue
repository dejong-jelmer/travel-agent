<script setup>
import { computed, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { Plus, Pencil, ShieldCheck, Ban, Trash2, TriangleAlert, CalendarClock } from '@lucide/vue';
import DepartureProgress from '@/Departures/DepartureProgress.vue';
import DepartureStatusBadge from '@/Departures/DepartureStatusBadge.vue';
import DepartureForm from '@/Departures/DepartureForm.vue';
import CancelDepartureForm from '@/Departures/CancelDepartureForm.vue';
import { formatDate } from '@/Departures/format.js';
import { formatCents } from '@/Support/money.js';

const props = defineProps({
    trip: { type: Object, required: true },
    isGroupTrip: { type: Boolean, required: true },
    departures: { type: Array, required: true },
});

const { t } = useI18n();
const locale = computed(() => usePage().props.locale);

const date = (value) => formatDate(value, locale.value);
const price = (cents) => (cents === null ? t('admin.departures.price.from_trip') : formatCents(cents, locale.value));

// The modals: the form adds a departure (true) or edits one (the departure), the other cancels one
const formTarget = ref(null);
const cancelTarget = ref(null);

function guarantee(departure) {
    const message = departure.has_reached_minimum
        ? t('admin.departures.guarantee_confirm')
        : t('admin.departures.guarantee_confirm_below_minimum', {
            min: departure.min_participants,
            confirmed: departure.confirmed_participants,
        });

    if (confirm(message)) {
        router.post(route('admin.departures.guarantee', departure.id), {}, { preserveScroll: true });
    }
}

function destroy(departure) {
    if (confirm(t('admin.departures.delete_confirm'))) {
        router.delete(route('admin.departures.destroy', departure.id), { preserveScroll: true });
    }
}
</script>

<template>
    <Admin>
        <div class="max-w-wide mx-auto space-y-6">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-700">{{ $t('admin.departures.title') }}</h1>
                    <p class="mt-1 text-sm text-gray-700/50">{{ $t('admin.departures.subtitle', { trip: trip.name }) }}</p>
                </div>
                <Button v-if="isGroupTrip" type="button" data-testid="add-departure" @click="formTarget = true">
                    <span class="inline-flex items-center gap-2">
                        <Plus class="h-4 w-4" /> {{ $t('admin.departures.add') }}
                    </span>
                </Button>
            </div>

            <div v-if="!isGroupTrip" class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 space-y-3">
                <p class="text-brand-text">{{ $t('admin.departures.not_a_group_trip') }}</p>
                <Link :href="route('admin.trips.edit', trip)" class="text-sm text-brand-link underline">
                    {{ $t('admin.departures.edit_trip') }}
                </Link>
            </div>

            <p v-else-if="departures.length === 0"
                class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 text-brand-text">
                {{ $t('admin.departures.empty') }}
            </p>

            <article v-for="departure in departures" :key="departure.id" :data-testid="`departure-${departure.id}`"
                class="bg-white rounded-lg border shadow-sm p-6 space-y-4"
                :class="departure.decision_overdue || departure.awaits_guarantee ? 'border-status-warning' : 'border-gray-200'">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-700">
                            {{ date(departure.departs_on) }} – {{ date(departure.returns_on) }}
                        </h2>
                        <p class="text-sm text-gray-700/50">
                            {{ $t('admin.departures.days', { count: departure.duration_in_days }) }}
                        </p>
                    </div>
                    <DepartureStatusBadge :status="departure.status" :is-guaranteed="departure.is_guaranteed" />
                </div>

                <DepartureProgress :occupied="departure.occupied_seats" :confirmed="departure.confirmed_participants"
                    :min="departure.min_participants" :max="departure.max_participants" />

                <!-- What needs the admin's attention -->
                <p v-if="departure.decision_overdue" data-testid="decision-overdue"
                    class="flex gap-3 text-sm text-brand-text bg-status-warning/10 border border-status-warning rounded-lg p-3">
                    <TriangleAlert class="h-5 w-5 flex-shrink-0 text-status-warning" />
                    {{ $t('admin.departures.decision.overdue') }}
                </p>
                <p v-else-if="departure.awaits_guarantee" data-testid="awaits-guarantee"
                    class="flex gap-3 text-sm text-brand-text bg-status-success/10 border border-status-success rounded-lg p-3">
                    <ShieldCheck class="h-5 w-5 flex-shrink-0 text-status-success" />
                    {{ $t('admin.departures.decision.awaits_guarantee') }}
                </p>

                <dl class="grid grid-cols-1 tablet:grid-cols-3 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-700/50">{{ $t('admin.departures.price.label') }}</dt>
                        <dd class="text-brand-text">{{ price(departure.price_per_person) }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-700/50">{{ $t('admin.departures.price.supplement') }}</dt>
                        <dd class="text-brand-text">{{ price(departure.single_supplement) }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-700/50">{{ $t('admin.departures.decision.label') }}</dt>
                        <dd class="text-brand-text inline-flex items-center gap-2">
                            <CalendarClock class="h-4 w-4 text-brand-light" /> {{ date(departure.decision_date) }}
                        </dd>
                    </div>
                </dl>

                <p v-if="departure.guaranteed_at" class="text-sm text-gray-600">
                    {{ $t('admin.departures.guaranteed_on', { date: date(departure.guaranteed_at) }) }}
                </p>
                <div v-if="departure.cancelled_at" class="text-sm text-gray-600">
                    <p>{{ $t('admin.departures.cancelled_on', { date: date(departure.cancelled_at) }) }}</p>
                    <p><span class="text-gray-700/50">{{ $t('admin.departures.reason') }}:</span>
                        {{ departure.cancellation_reason }}</p>
                </div>

                <div class="flex flex-wrap gap-2 pt-2 border-t border-gray-100">
                    <button v-if="departure.can_be_edited" type="button" class="departure-action"
                        @click="formTarget = departure">
                        <Pencil class="h-4 w-4" /> {{ $t('admin.departures.actions.edit') }}
                    </button>
                    <button v-if="departure.can_be_guaranteed" type="button" data-testid="guarantee"
                        class="departure-action" @click="guarantee(departure)">
                        <ShieldCheck class="h-4 w-4" /> {{ $t('admin.departures.actions.guarantee') }}
                    </button>
                    <button v-if="departure.can_be_cancelled" type="button" data-testid="cancel"
                        class="departure-action" @click="cancelTarget = departure">
                        <Ban class="h-4 w-4" /> {{ $t('admin.departures.actions.cancel') }}
                    </button>
                    <button type="button" class="departure-action hover:!border-status-error hover:!text-status-error"
                        @click="destroy(departure)">
                        <Trash2 class="h-4 w-4" /> {{ $t('admin.departures.actions.delete') }}
                    </button>
                </div>
            </article>
        </div>

        <Modal :open="formTarget !== null" @close="formTarget = null">
            <!-- The key gives every departure a form of its own, so one does not start with the values of another -->
            <DepartureForm v-if="formTarget !== null" :key="formTarget === true ? 'new' : formTarget.id" :trip="trip"
                :departure="formTarget === true ? null : formTarget" @close="formTarget = null" />
        </Modal>

        <Modal :open="cancelTarget !== null" @close="cancelTarget = null">
            <CancelDepartureForm v-if="cancelTarget !== null" :key="cancelTarget.id" :departure="cancelTarget"
                @close="cancelTarget = null" />
        </Modal>
    </Admin>
</template>

<style scoped>
.departure-action {
    @apply inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm text-brand-primary transition-colors hover:border-brand-primary;
}
</style>
