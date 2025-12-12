<script setup>
import { EllipsisVertical } from 'lucide-vue-next';
import { reactive } from 'vue';

const props = defineProps({
    bookings: Object
});
const showActions = reactive({});

</script>
<template>
    <Admin :links="bookings.links">
        <div class="overflow-x-auto bg-white shadow-lg rounded-2xl">
            <template v-if="bookings.data.length > 0">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                            <th class="py-4 px-6 text-center">#</th>
                            <th class="py-4 px-6 text-center">{{ $t('admin.booking.index.table_headers.reference') }}</th>
                            <th class="py-4 px-6 text-center">{{ $t('admin.booking.index.table_headers.trip') }}</th>
                            <th class="py-4 px-6 text-center">{{ $t('admin.booking.index.table_headers.departure_date') }}</th>
                            <th class="py-4 px-6 text-center">{{ $t('admin.booking.index.table_headers.status') }}</th>
                            <th class="py-4 px-6 text-center">{{ $t('admin.booking.index.table_headers.payment') }}</th>
                            <th class="py-4 px-6 text-center">{{ $t('admin.booking.index.table_headers.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm divide-y divide-gray-200">
                        <tr v-for="(booking, index) in bookings.data" :key="index" class="transition hover:bg-gray-100">
                            <td class="py-4 px-6 text-center">{{ booking.id }}</td>
                            <td class="py-4 px-6 text-center">{{ booking.reference }}</td>
                            <td class="py-4 px-6 text-center">{{ booking.trip?.name ?? '-' }}</td>
                            <td class="py-4 px-6 text-center">{{ booking.departure_date_formatted }}</td>
                            <td class="py-4 px-6 text-center">
                                <BookingStatusBadge class="w-full" :status="booking.status">{{ booking.status_label }}</BookingStatusBadge>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <PaymentStatusBadge class="w-full" :status="booking.payment_status">{{ booking.payment_status_label }}
                                </PaymentStatusBadge>
                            </td>
                            <td class="py-4 px-6 text-center space-y-2">
                                <DropdownMenu>
                                    <template #default="{ MenuItem }">
                                        <component :is="MenuItem">
                                            <IconLink icon="Eye"
                                                :href="route('admin.bookings.show', booking)"
                                                v-tippy="$t('admin.booking.actions.show')" />
                                        </component>
                                        <component :is="MenuItem">
                                            <IconLink icon="Pencil"
                                                :href="route('admin.bookings.edit', booking)"
                                                v-tippy="$t('admin.booking.actions.edit')" />
                                        </component>
                                        <component :is="MenuItem">
                                            <IconLink type="delete" icon="Trash2"
                                                :href="route('admin.bookings.destroy', booking)" method="delete"
                                                :showConfirm="true"
                                                :prompt="$t('admin.booking.actions.delete_confirm')"
                                                v-tippy="$t('admin.booking.actions.delete')" />
                                        </component>
                                    </template>
                                </DropdownMenu>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </template>
            <template v-else>
                <div class="p-5">
                    <p>{{ $t('admin.booking.index.no_bookings') }}</p>
                </div>
            </template>
        </div>
    </Admin>
</template>
