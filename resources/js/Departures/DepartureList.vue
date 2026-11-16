<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import DepartureCard from './DepartureCard.vue';
import DepartureForm from './DepartureForm.vue';
import CancelDepartureForm from './CancelDepartureForm.vue';

// A list of departures with everything the admin can do to one: edit, guarantee, cancel and delete.
defineProps({
    departures: { type: Array, required: true },
    showTrip: { type: Boolean, default: false },
});

const { t } = useI18n();

// The departure being edited or cancelled, each in its own modal
const editTarget = ref(null);
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
    <div class="space-y-6">
        <DepartureCard v-for="departure in departures" :key="departure.id" :departure="departure"
            :show-trip="showTrip" @edit="editTarget = departure" @guarantee="guarantee(departure)"
            @cancel="cancelTarget = departure" @destroy="destroy(departure)" />

        <Modal :open="editTarget !== null" @close="editTarget = null">
            <!-- The key gives every departure a form of its own, so one does not start with the values of another -->
            <DepartureForm v-if="editTarget !== null" :key="editTarget.id" :departure="editTarget"
                @close="editTarget = null" />
        </Modal>

        <Modal :open="cancelTarget !== null" @close="cancelTarget = null">
            <CancelDepartureForm v-if="cancelTarget !== null" :key="cancelTarget.id" :departure="cancelTarget"
                @close="cancelTarget = null" />
        </Modal>
    </div>
</template>
