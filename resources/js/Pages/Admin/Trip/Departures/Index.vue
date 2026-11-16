<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Plus } from '@lucide/vue';
import DepartureList from '@/Departures/DepartureList.vue';
import DepartureForm from '@/Departures/DepartureForm.vue';

defineProps({
    trip: { type: Object, required: true },
    isGroupTrip: { type: Boolean, required: true },
    departures: { type: Array, required: true },
});

const adding = ref(false);
</script>

<template>
    <Admin>
        <div class="max-w-wide mx-auto space-y-6">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-700">{{ $t('admin.departures.title') }}</h1>
                    <p class="mt-1 text-sm text-gray-700/50">{{ $t('admin.departures.subtitle', { trip: trip.name }) }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-4">
                    <Link :href="route('admin.departures.index')" class="text-sm text-brand-link underline">
                        {{ $t('admin.departures.all_trips') }}
                    </Link>
                    <Button v-if="isGroupTrip" type="button" data-testid="add-departure" @click="adding = true">
                        <span class="inline-flex items-center gap-2">
                            <Plus class="h-4 w-4" /> {{ $t('admin.departures.add') }}
                        </span>
                    </Button>
                </div>
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

            <DepartureList :departures="departures" />
        </div>

        <Modal :open="adding" @close="adding = false">
            <DepartureForm v-if="adding" :trip="trip" @close="adding = false" />
        </Modal>
    </Admin>
</template>
