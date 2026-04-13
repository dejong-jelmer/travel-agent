<script setup>
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { FileText, Activity, Link } from 'lucide-vue-next';

const { t } = useI18n();

const props = defineProps({
    tripRequest: Object,
    statusOptions: Array,
    bookingOptions: Array,
});

const form = useForm({
    status: props.tripRequest.status,
    notes: props.tripRequest.notes ?? '',
    booking_id: props.tripRequest.booking_id ? String(props.tripRequest.booking_id) : null,
});

function submit() {
    form.put(route('admin.trip-requests.update', props.tripRequest));
}

function formatPreferredMonth(value) {
    if (!value) return null;
    const [year, month] = value.split('-');
    return `${t(`trip_request.form.months.${parseInt(month)}`)} ${year}`;
}
</script>

<template>
    <Admin>
        <div class="max-w-wide mx-auto">
            <div class="grid grid-cols-1 laptop:grid-cols-3 gap-8">
                <!-- Header -->
                <div class="laptop:col-span-3 bg-white py-10 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-700">
                                {{ t('admin.trip_request.edit.title', { name: tripRequest.name }) }}
                            </h1>
                            <div class="mt-2 flex items-center gap-3">
                                <p class="text-sm text-gray-700/50">{{ tripRequest.trip?.name }}</p>
                                <TripRequestStatusBadge :status="tripRequest.status">{{ tripRequest.status_label }}</TripRequestStatusBadge>
                            </div>
                        </div>
                        <IconLink type="delete" icon="Trash2" :href="route('admin.trip-requests.destroy', tripRequest)"
                            method="delete" :showConfirm="true"
                            :prompt="t('admin.trip_request.actions.delete_confirm')"
                            v-tippy="t('admin.trip_request.actions.delete')" />
                    </div>
                </div>

                <!-- Main column -->
                <div class="laptop:col-span-2 space-y-8">
                    <!-- Request details (read-only) -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <div class="flex items-center gap-2">
                                <FileText class="h-4 w-4 text-gray-400 flex-shrink-0" />
                                <h2 class="text-lg font-semibold text-gray-700">{{ t('admin.trip_request.edit.request_details') }}</h2>
                            </div>
                        </div>
                        <dl class="divide-y divide-gray-100">
                            <div class="px-6 py-4 grid grid-cols-3 gap-4 items-start">
                                <dt class="text-sm font-medium text-gray-500">{{ t('admin.trip_request.edit.name') }}</dt>
                                <dd class="text-sm text-gray-900 col-span-2">{{ tripRequest.name }}</dd>
                            </div>
                            <div class="px-6 py-4 grid grid-cols-3 gap-4 items-start">
                                <dt class="text-sm font-medium text-gray-500">{{ t('admin.trip_request.edit.email') }}</dt>
                                <dd class="text-sm text-gray-900 col-span-2">{{ tripRequest.email }}</dd>
                            </div>
                            <div v-if="tripRequest.phone" class="px-6 py-4 grid grid-cols-3 gap-4 items-start">
                                <dt class="text-sm font-medium text-gray-500">{{ t('admin.trip_request.edit.phone') }}</dt>
                                <dd class="text-sm text-gray-900 col-span-2">{{ tripRequest.phone }}</dd>
                            </div>
                            <div class="px-6 py-4 grid grid-cols-3 gap-4 items-start">
                                <dt class="text-sm font-medium text-gray-500">{{ t('admin.trip_request.edit.trip') }}</dt>
                                <dd class="text-sm text-gray-900 col-span-2">{{ tripRequest.trip?.name ?? '-' }}</dd>
                            </div>
                            <div class="px-6 py-4 grid grid-cols-3 gap-4 items-start">
                                <dt class="text-sm font-medium text-gray-500">{{ t('admin.trip_request.edit.preferred_period') }}</dt>
                                <dd class="text-sm text-gray-900 col-span-2">
                                    {{ formatPreferredMonth(tripRequest.preferred_month) ?? t('admin.trip_request.edit.no_preference') }}
                                    <span v-if="tripRequest.preferred_period_note" class="block text-gray-500 mt-0.5 italic">
                                        {{ tripRequest.preferred_period_note }}
                                    </span>
                                </dd>
                            </div>
                            <div class="px-6 py-4 grid grid-cols-3 gap-4 items-start">
                                <dt class="text-sm font-medium text-gray-500">{{ t('admin.trip_request.edit.travelers_count') }}</dt>
                                <dd class="text-sm text-gray-900 col-span-2">{{ tripRequest.travelers_count ?? '-' }}</dd>
                            </div>
                            <div class="px-6 py-4 grid grid-cols-3 gap-4 items-start">
                                <dt class="text-sm font-medium text-gray-500">{{ t('admin.trip_request.edit.departure_station') }}</dt>
                                <dd class="text-sm text-gray-900 col-span-2">
                                    {{ tripRequest.departure_station }}
                                </dd>
                            </div>
                            <div class="px-6 py-4 grid grid-cols-3 gap-4 items-start">
                                <dt class="text-sm font-medium text-gray-500">{{ t('admin.trip_request.edit.created_at') }}</dt>
                                <dd class="text-sm text-gray-900 col-span-2">{{ tripRequest.created_at_formatted }}</dd>
                            </div>
                        </dl>
                    </section>

                    <!-- Notes (editable) -->
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-700">{{ t('admin.trip_request.edit.notes_section') }}</h2>
                        </div>
                        <div class="px-6 py-5">
                            <TextArea
                                v-model="form.notes"
                                :label="t('admin.trip_request.edit.notes_label')"
                                :placeholder="t('admin.trip_request.edit.notes_placeholder')"
                                :error="form.errors.notes"
                                :rows="5" />
                        </div>
                    </section>
                </div>

                <!-- Sidebar -->
                <div class="space-y-8">
                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <div class="flex items-center gap-2">
                                <Activity class="h-4 w-4 text-gray-400 flex-shrink-0" />
                                <h2 class="text-lg font-semibold text-gray-700">{{ t('admin.trip_request.edit.status_section') }}</h2>
                            </div>
                        </div>
                        <div class="px-6 py-5">
                            <Select
                                v-model="form.status"
                                :label="t('admin.trip_request.edit.status_label')"
                                :placeholder="t('admin.trip_request.edit.status_placeholder')"
                                :options="statusOptions"
                                :error="form.errors.status" />
                        </div>
                    </section>

                    <section class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <div class="flex items-center gap-2">
                                <Link class="h-4 w-4 text-gray-400 flex-shrink-0" />
                                <h2 class="text-lg font-semibold text-gray-700">{{ t('admin.trip_request.edit.booking_section') }}</h2>
                            </div>
                        </div>
                        <div class="px-6 py-5">
                            <Select
                                v-model="form.booking_id"
                                :label="t('admin.trip_request.edit.booking_label')"
                                :placeholder="t('admin.trip_request.edit.booking_placeholder')"
                                :options="bookingOptions"
                                :clearable="true"
                                :error="form.errors.booking_id" />
                        </div>
                    </section>

                    <Button @click="submit" :disabled="form.processing" class="w-full">
                        {{ form.processing ? t('admin.trip_request.edit.submit_processing') : t('admin.trip_request.edit.submit') }}
                    </Button>
                </div>
            </div>
        </div>
    </Admin>
</template>
